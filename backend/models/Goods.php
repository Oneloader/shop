<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\Json;

class Goods extends ActiveRecord{

    public function rules()
    {
        return [
            [['sn','goods_category_id','brand_id','market_price','shop_price','stock','is_on_sale','status','sort','create_time','view_times'],'integer'],
            [['name'],'string','max'=>150],
            [['logo'],'string','max'=>255],
        ];
    }

    //获取分类信息,作为ztree的节点数据
    public static function getNodes(){
        $nodes = self::find()->select(['id','parent_id','name'])->asArray()->all();
        array_unshift($nodes,['id'=>0,'parent_id'=>0,'name'=>'【顶级分类】']);
        return Json::encode($nodes);
    }


}