<?php
namespace App\Repositories;

use App\Models\Tweet;
use App\Models\TwitterUser;

/**
 * Class TwitterUsersRepository
 * @package App\Repositories
 */
class TwitterUsersRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $users;

    /**
     * TwitterUsersRepository constructor.
     */
    public function __construct()
    {
        $this->users = TwitterUser::query();
    }

    /**
     * @param $userId
     * @return $this
     */
    public function filterById( $userId )
    {
        $this->users->where('user_id', $userId);

        return $this;
    }

    /**
     * @return Tweet|\Illuminate\Database\Eloquent\Builder
     */
    public function get()
    {
        return $this->users;
    }
}