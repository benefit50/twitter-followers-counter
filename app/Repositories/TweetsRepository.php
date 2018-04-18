<?php
namespace App\Repositories;

use App\Models\Tweet;

/**
 * Class TweetsRepository
 * @package App\Repositories
 */
class TweetsRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $tweets;

    /**
     * Use eager loading for 'user' relationship
     *
     * TweetsRepository constructor.
     */
    public function __construct()
    {
        $this->tweets = Tweet::with('user');
    }

    /**
     * @param $tweetId
     * @return $this
     */
    public function filterById( $tweetId )
    {
        $this->tweets->where('tweet_id', $tweetId);

        return $this;
    }

    /**
     * @param int $statusId
     * @return $this
     */
    public function filterByStatusId( int $statusId )
    {
        $this->tweets->where('status', $statusId);

        return $this;
    }

    /**
     * @return Tweet|\Illuminate\Database\Eloquent\Builder
     */
    public function get()
    {
        return $this->tweets;
    }
}