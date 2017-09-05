<?php

namespace App\Providers;

use Auth;
use App\Libraries\MD5;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
		 \App\Models\Admin::class => \App\Policies\AdminPolicy::class,
		 \App\Models\ExchangeBottle::class => \App\Policies\ExchangeBottlePolicy::class,
		 \App\Models\OrderGoodsPayment::class => \App\Policies\OrderGoodsPaymentPolicy::class,
		 \App\Models\SubOrderPayment::class => \App\Policies\SubOrderPaymentPolicy::class,
		 \App\Models\MainOrderPayment::class => \App\Policies\MainOrderPaymentPolicy::class,
		 \App\Models\GoodsSetting::class => \App\Policies\GoodsSettingPolicy::class,
		 \App\Models\Driver::class => \App\Policies\DriverPolicy::class,
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('MD5', function ($app) {
            $model = config('auth.providers.admin.model');
            return new MD5ServiceProvider(new MD5, $model);
        });
    }
}
