<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Admin extends Authenticatable
{
    use HasRolesAndAbilities;

    protected $table = 'admin';

    protected $primaryKey = 'admin_id';

    protected $hidden = ['admin_password'];

    public function isSuperAdmin()
    {
        return $this->admin_is_super;
    }

    public function getAuthPassword()
    {
        return $this->admin_password;
    }


}
