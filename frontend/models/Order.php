<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    public static $deliveries = [
        1=>['顺丰快递',25,'速度快,服务好,价格贵'],
        2=>['EMS',20,'速度慢,服务一般,价格贵'],
        3=>['韵达快递',15,'速度快,服务一般,价格中等'],
        4=>['天天快递',10,'速度慢,服务一般,价格便宜'],
    ];

    public static $pays = [
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡，微信支付，支付宝支付'],
        3=>['上门自提','自提时付款，支持现金、POS刷卡、支票支付、微信支付、支付宝支付'],
    ];

    public function rules()
    {
        return [
            [['member_id','name','province','city','area','address','address_id','tel','delivery_id','delivery_name','delivery_price','payment_id','payment_name','total','status'],'safe'],
        ];
    }
}