<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tweet
 * @package App\Models
 */
class Tweet extends Model
{
    protected $table = 'tweets';

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = ['tweet_id', 'twitter_user_id', 'retweets', 'text'];

    const UNPROCESSED_STATUS_ID = 0;
    const PROCESSED_STATUS_ID = 1;
    const PENDING_UPDATE_STATUS_ID = 2;

    /**
     * @return string
     */
    public function getTweetId() : string
    {
        return $this->tweet_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(TwitterUser::class, 'user_id', 'twitter_user_id');
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getRetweets() : int
    {
        return $this->retweets;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->updated_at;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus(int $value)
    {
        $this->status = $value;
        $this->save();

        return $this;
    }

    public function resetData()
    {
        $this->retweets = 0;
        $this->retweets_followers = 0;

        $this->save();
    }

    /**
     * @return bool
     */
    public function isPendingUpdate()
    {
        return $this->status == self::PENDING_UPDATE_STATUS_ID;
    }
}
