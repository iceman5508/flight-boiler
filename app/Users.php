<?php

namespace App\Models;
use flight;
use App\Database\Database;


class Users extends flight\ActiveRecord
{
   
    public function __construct()
    {
        // you can set it this way
        parent::__construct(Database::getInstance()->getConnection(), 'users');
        
    }

}
