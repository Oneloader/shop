<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public function rules()
    {
        return [
            [['name','cmbProvince','cmbCity','cmbArea','address','phone'],'required'],
            [['phone','default'],'integer'],
            [['name'],'string','max'=>255],
        ];
    }


}