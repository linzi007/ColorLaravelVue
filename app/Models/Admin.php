<?php

namespace App\Models;


class Admin extends Model
{
    protected $table = 'admin';

    protected $primaryKey = 'admin_id';

    protected $hidden = ['admin_password'];
}
