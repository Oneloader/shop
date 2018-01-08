<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    public function rules(){
        return[
            [['name','intro','sort','status'],'required'],
            [['status','sort'],'integer'],
            [['name','intro'],'string','max'=>255],
            [['name'],'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '文章名称',
            'intro' => '文章简介',
            'sort' => '文章排序',
            'status' => '文章状态',
        ];
    }

}