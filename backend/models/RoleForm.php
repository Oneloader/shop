<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permission;

    public function rules()
    {
        return [
            [['name','description'], 'required'],
            [['permission'],'safe'],
        ];
    }

//    public function attributeLabels()
//    {
//        return [
//            'name'=>'角色名',
//            'description'=>'详情',
//        ];
//    }
}