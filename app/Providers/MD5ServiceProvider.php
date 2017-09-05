<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class MD5ServiceProvider extends EloquentUserProvider
{

    //继承EloquentUserProvider类，调用父类的构造函数
    public function __construct($hasher, $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['admin_password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}