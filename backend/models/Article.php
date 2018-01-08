<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    //验证规则
    public function rules(){
        return[
            [['name','intro','article_category_id','sort','status'],'required'],
            [['article_category_id','sort','status'],'integer'],
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
            'article_category_id' => '文章分类id',
            'sort' => '文章排序',
            'status' => '文章状态',
        ];
    }

}