<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
//    public $imgFile;

    public function rules()
    {
        return [
            [['name', 'intro','sort','status','logo'], 'required'],
            [['status', 'sort'], 'integer'],
            [['name', 'intro'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['logo'],'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '品牌名称',
            'intro' => '品牌简介',
            'logo' => '品牌LOGO',
            'sort' => '品牌排序',
            'status' => '状态',
        ];
    }

}