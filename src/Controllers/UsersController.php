<?php

namespace Wallace\Controllers;

use Wallace\Models\Persistent\User;

class UsersController extends AbstractController
{

    public function create($apikey, $identifier)
    {
        $user = User::create([
            'apikey' => $apikey,
            'identifier' => $identifier
        ]);

        return $user;
    }
}
