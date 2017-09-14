<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Admin extends Authenticatable
{
    use HasRolesAndAbilities;

    protected $table = 'admin';

    protected $primaryKey = 'admin_id';

    protected $hidden = ['admin_password'];

    public $timestamps = false;

    private $cacheKey = 'admin_list';

    private $cacheMinutes = 60*6;

    public function isSuperAdmin()
    {
        return $this->admin_is_super;
    }

    public function getAuthPassword()
    {
        return $this->admin_password;
    }

    public function updateRememberToken()
    {

    }

    public function getCache()
    {
        return Cache::remember($this->getCacheKey(), $this->cacheMinutes, function () {
            $admins = $this->orderBy('admin_id', 'desc');
            return $admins->get()->toArray();
        });
    }

    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    public function getKeyMap()
    {
        $admins = $this->getCache();

        return array_column($admins, 'admin_name', 'admin_id');
    }
}
