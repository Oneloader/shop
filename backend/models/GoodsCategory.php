<?php
namespace backend\models;

use Codeception\Module\Redis;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\Url;

class GoodsCategory extends ActiveRecord
{
    public function rules()
    {
        return [
            [['tree','parent_id','lft','rgt','depth'],'integer'],
            [['intro'],'string'],
            [['name'],'string','max'=>50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => '名称',
            'parent_id' => '上级分类id',
            'intro' => '简介',
        ];
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    //获取分类信息,作为ztree的节点数据
    public static function getNodes(){
        $nodes = self::find()->select(['id','parent_id','name'])->asArray()->all();
        array_unshift($nodes,['id'=>0,'parent_id'=>0,'name'=>'【顶级分类】']);
        return Json::encode($nodes);
    }

    public static function getCategories(){
        //使用redis作为商品分类的缓存
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $html = $redis->get('category_html');
        if ($html == false){
        $html = '';
            $categories = \backend\models\GoodsCategory::find()->where(['parent_id'=>0])->all();
            foreach ($categories as $key1=>$one){
                $html .=  '<div class="cat '.($key1?'':'item1').'">';
                $html .=  '<h3><a href="">'.$one->name.'</a><b></b></h3>';
                $html .=  '<div class="cat_detail">';
                $cate = \backend\models\GoodsCategory::find()->where(['parent_id'=>$one->id])->all();
                foreach ($cate as $key2=>$two){
                    $html .=  '<dl '.($key2?'':'class="dl_1st"').'>';
                    $html .=  '<dt><a href="'.Url::to(['site/list','id'=>$two->id]).'">'.$two->name.'</a></dt>';

                    $html .=  '<dd>';
                    $ca = \backend\models\GoodsCategory::find()->where(['parent_id'=>$two->id])->all();
                    foreach ($ca as $last){
                        $html .=  '<a href="'.Url::to(['site/list','id'=>$last->id]).'">'.$last->name.'</a>';
                    }
                    $html .=  '</dd>';

                    $html .=  '</dl>';
                }
                $html .=  '</div>';
                $html .=  '</div>';
            }
            //将分类的html代码保存到redis
            $redis->set('category_html',$html,24*3600);
        }
        return $html;
        }
}