<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //创建订单
        'App\Events\Trade\TradeCreate' => [
            'App\Listeners\Trade\TradeCreateListener',
        ],

        //订单支付
        'App\Events\Payment' => [
            'App\Listeners\Trade\PaymentListener',

            //分佣-统计店铺的订单
            'App\Listeners\Trade\TakeTradeCommission',
        ],

        //订单发货
        'App\Events\AfterDelivery' => [
            'App\Listeners\Trade\TakeTradeCommission',
        ],

        //上传CERT-PDF到云服务器
        'App\Events\Shop\CertPdfSentEvent' => [
            'App\Listeners\Shop\CertPdfSentListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
