<?php
namespace App\Http\Controllers;

use App\Http\Requests\FetchTweetDetailsFormRequest;
use App\Jobs\FetchRetweets;
use App\Models\Tweet;
use App\Repositories\TweetsRepository;
use App\Rules\ValidTweetUrl;
use App\Utils\UrlParsing;
use App\Utils\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Twitter;

/**
 * Class TweetsProcessing
 * @package App\Http\Controllers
 */
class TweetsProcessing extends Controller
{
    protected $urlParser;
    protected $tweetRepo;
    protected $validator;

    /**
     * TweetsProcessing constructor.
     * @param UrlParsing $urlParser
     * @param Validator $validator
     * @param TweetsRepository $tweetRepo
     */
    public function __construct(UrlParsing $urlParser, Validator $validator, TweetsRepository $tweetRepo)
    {
        $this->urlParser = $urlParser;
        $this->validator = $validator;
        $this->tweetRepo = $tweetRepo;
    }

    /**
     * Check if such tweet already exists in the database. If it was added longer than 2 hours ago, it will be updated
     * with fresh data from API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerTask(Request $request)
    {
        $request->validate([
            'url' => ['required', 'string', new ValidTweetUrl()],
        ]);

        $url = $request->input('url');
        $tweetId = $this->urlParser->getTweetId($url);

        $exists = $this->tweetRepo->filterById($tweetId)->get()->count() > 0;

        if ($exists) {
            $tweet = $this->tweetRepo->filterById($tweetId)->get()->first();

            $timeExpired = $this->validator->timeExpired($tweet->getDate(), config('app.persist_time'));

            if ($timeExpired) {
                $tweet->setStatus(Tweet::PENDING_UPDATE_STATUS_ID)
                      ->resetData();

                return $this->runTask($tweetId);
            }

            return new JsonResponse($tweet);
        } else
            return $this->runTask($tweetId);
    }

    /**
     * @param $tweetId
     * @return JsonResponse
     */
    private function runTask($tweetId)
    {
        FetchRetweets::dispatch($tweetId);

        return new JsonResponse([
            'message' => 'Task added to queue.',
            'in_progress' => 1,
            'tweet' => $tweetId
        ]);
    }

    /**
     * This method is used for repeating ajax updating on front-end side to refresh the counter in real-time.
     *
     * @param FetchTweetDetailsFormRequest $request
     * @return JsonResponse
     */
    public function getTweetDetails(FetchTweetDetailsFormRequest $request)
    {
        $tweetId = $request->input('tweet');
        $exists = $this->tweetRepo->filterById($tweetId)->get()->count() > 0;

        if ($exists)
            $tweet = $this->tweetRepo->filterById($tweetId)->get()->first();
        else {
            $tweet = [
                'id' => $tweetId,
                'status' => 'Not created yet.'
            ];
        }

        return new JsonResponse($tweet);
    }

}
