<?php
namespace App\Jobs;

use App\Models\Tweet;
use App\Models\TwitterUser;
use App\Repositories\TweetsRepository;
use App\Repositories\TwitterUsersRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Twitter;

/**
 * Class FetchRetweets
 * @package App\Jobs
 */
class FetchRetweets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tweetId;
    protected $tweet;
    protected $rawTweet;
    protected $rawUser;

    /**
     * @var TwitterUsersRepository
     */
    protected $userRepository;

    /**
     * @var TweetsRepository
     */
    protected $tweetsRepository;

    /**
     * FetchRetweets constructor.
     * @param int $tweetId
     */
    public function __construct(int $tweetId)
    {
        $this->tweetId = $tweetId;
    }

    /**
     * Repositories were type-hinted here to be retrieved from Laravel service container
     *
     * @param TweetsRepository $tweetsRepository
     * @param TwitterUsersRepository $repository
     */
    public function handle(TweetsRepository $tweetsRepository, TwitterUsersRepository $repository)
    {
        $this->userRepository = $repository;
        $this->tweetsRepository = $tweetsRepository;

        $this->fetchTweet()
             ->updateTweet()
             ->saveUser()
             ->fetchRetweets()
             ->finish();
    }

    /**
     * @return $this
     */
    private function fetchTweet()
    {
        $tweet = Twitter::getTweet($this->tweetId);

        $this->rawTweet = $tweet;
        $this->rawUser = $tweet->user;

        return $this;
    }

    /**
     * @return $this
     */
    private function updateTweet()
    {
        $tweet = $this->rawTweet;

        $queryResults = $this->tweetsRepository->filterById($this->tweetId)->get();
        $exists = $queryResults->count() > 0;

        $_tweet = $exists ? $queryResults->first() : new Tweet();

        $_tweet->tweet_id           = $this->tweetId;
        $_tweet->twitter_user_id    = $tweet->user->id_str;
        $_tweet->retweets           = $tweet->retweet_count;
        $_tweet->text               = $tweet->text;

        $_tweet->save();

        $this->tweet = $_tweet;

        return $this;
    }

    /**
     * @return $this
     */
    private function saveUser()
    {
        $user = $this->rawUser;

        $queryResults = $this->userRepository->filterById($user->id_str)->get();
        $exists = $queryResults->count() > 0;

        $_user = $exists ? $queryResults->first() : new TwitterUser();

        $_user->user_id     = $user->id_str;
        $_user->name        = $user->name;
        $_user->screen_name = $user->screen_name;
        $_user->followers   = $user->followers_count;

        $_user->save();

        return $this;
    }

    /**
     * Perform recursive API calls in order to reach all results. Each call can return up to 100 records.
     *
     * @return $this
     */
    private function fetchRetweets()
    {
        $retweets = Twitter::getRts($this->tweetId, ['count' => 100]);
        $totalFollowers = 0;

        while (count($retweets) != 1) {

            foreach ($retweets as $retweet) {
                $totalFollowers += $retweet->user->followers_count;
            }

            $this->saveFollowersCount($totalFollowers);

            $lastObjectId = (int)$retweets[count($retweets)-1]->id_str;
            $retweets = Twitter::getRts($this->tweetId, ['count' => 100, 'max_id' => $lastObjectId ]);
        }

        return $this;
    }

    /**
     * @param int $count
     */
    private function saveFollowersCount(int $count)
    {
        $this->tweet->retweets_followers = $count;

        $this->tweet->save();
    }

    private function finish()
    {
        $this->tweet->setStatus(Tweet::PROCESSED_STATUS_ID);
    }
}
