<?php

namespace App\Traits;

trait WeChatPaymentTrait
{
    protected $orderStatusMap = [
        1 => '内部订单创建成功',
        2 => '预支付订单创建成功',
        3 => '预支付订单创建失败',
        4 => '支付成功',
        5 => '支付失败',
        6 => '取消订单成功',
        7 => '取消订单失败',
    ];

    protected $tradeTypeMap = [
        'NATIVE' => '扫码',
        'APP' => 'app',
    ];

    protected $itemDeliveryStatusMap = [
        0 => '未发货',
        1 => '已发货',
    ];
}