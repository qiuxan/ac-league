<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const MANUAL_MESSAGE = 1;
    const SYSTEM_MESSAGE = 2;
    const UNREAD = 0;
    const READ = 1;
}