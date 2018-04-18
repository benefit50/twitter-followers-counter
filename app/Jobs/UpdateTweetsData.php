<?php
namespace App\Jobs;

use App\Models\Tweet;
use App\Repositories\TweetsRepository;
use App\Utils\Validator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * This job is used by cron to update the tweets data in the database
 *
 * Class UpdateTweetData
 * @package App\Jobs
 */
class UpdateTweetsData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Tweet
     */
    protected $tweets;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var TweetsRepository
     */
    protected $tweetsRepo;

    /**
     * @param TweetsRepository $repository
     * @param Validator $validator
     */
    public function handle(TweetsRepository $repository, Validator $validator)
    {
        $this->tweetsRepo = $repository;
        $this->validator = $validator;

        $this->tweets = $this->tweetsRepo->filterByStatusId(Tweet::PROCESSED_STATUS_ID)->get()->get();

        foreach ($this->tweets as $tweet) {
            if ($this->requiresUpdate($tweet))
                FetchRetweets::dispatch($tweet->tweet_id);
        }
    }

    /**
     * @param Tweet $tweet
     * @return bool
     */
    private function requiresUpdate(Tweet $tweet)
    {
        return $this->validator->timeExpired($tweet->getDate(), config('app.persist_time'));
    }
}
