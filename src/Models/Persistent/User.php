<?php

namespace Wallace\Models\Persistent;

use ActiveRecord\Model;

class User extends Model
{

    public static $validates_presence_of = [
        ['apikey'],
        ['identifier'],
    ];
}
