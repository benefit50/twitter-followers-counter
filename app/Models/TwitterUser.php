<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TwitterUser
 * @package App\Models
 */
class TwitterUser extends Model
{
    protected $table = 'twitter_users';

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = ['user_id', 'name', 'screen_name', 'followers'];

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getScreenName() : string
    {
        return $this->screen_name;
    }

    /**
     * @return int
     */
    public function getFollowers() : int
    {
        return $this->followers;
    }

}
