<?php

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = ['by', 'popular', 'unanswered'];

    /**
     * Filter the query by a given user name
     *
     * @param $username
     *
     * @return mixed
     */
    public function by($username)
    {
        $user = User::where('name', $username)->first();

        return $this->builder ->where('user_id', $user->id);
    }


    /**
     * Filter the query according to most popular thread
     *
     * @return mixed
     */
    public function popular()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }

    public function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}