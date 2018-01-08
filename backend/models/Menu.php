<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord{

    public static function tableName()
    {
        return 'menu';
    }

    public function rules()
    {
        return [
            [['label','sort','parent_id'],'required'],
            [['url'],'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'label' => '菜单名',
            'url' => '路由/地址',
            'parent_id' => '上级菜单',
            'sort' => '排序',
        ];
    }

//    //获取该用户的菜单
//    public function getMenus(){
//        $menuItems = [];
//        $menus = Menu::find()->where(['parent_id'=>0])->all();
//        foreach ($menus as $menu){
//            //获取该一级分类的子分类
//            $children = Menu::find()->where(['parent_id'=>$menu->id])->all();
//            $items = [];
//            foreach ($children as $child){
//                $items[] = ['label' => $child->label, 'url' => [$child->url]];
//            }
//            $menuItems[] = ['label' => $menu->label, 'items'=>$items];
//        }
//        return $menuItems;
//    }

}