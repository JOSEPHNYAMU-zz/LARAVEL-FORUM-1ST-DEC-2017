<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /*
     * Override the route get key name method like so
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}
