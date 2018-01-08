<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use yii\web\Controller;
use yii\web\Request;

class GoodsCategoryController extends Controller{
    //首页显示功能
    public function actionIndex(){
        $model = GoodsCategory::find()->where(['>','parent_id','-1'])->all();
        return $this->render('index',['model'=>$model]);
    }

    //添加功能
    public function actionAdd(){
        $model = new GoodsCategory();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()){
            if ($model->parent_id){
                //创建子节点
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                $model->appendTo($parent);
            }else{
                //创建根节点
                $model->makeRoot();
            }
            $model->save();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['index']);
        }
        return $this->render('add',['model'=>$model]);
    }

    //修改功能
    public function actionEdit($id){
        $request = new Request();
        //根据id获取数据
        $model = GoodsCategory::findOne(['id'=>$id]);
//        var_dump($model);exit;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods-category/index']);
            } else{
                var_dump($model->getErrors());
            }
        }
        return $this->render('edit',['model'=>$model]);
    }

    //删除功能
    public function actionDelete($id){
            GoodsCategory::updateAll(['parent_id'=>-1],['id'=>$id]);
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['uploader','upload'],
            ]
        ];
    }
}