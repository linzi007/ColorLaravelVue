<?php

namespace App\Providers;

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

        //
    }
}
