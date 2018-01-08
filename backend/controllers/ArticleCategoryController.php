<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller{
    //显示功能
    public function actionIndex(){
        //查询数据库,查询出所有数据
//        $model = ArticleCategory::find()->all();
//        var_dump($model);exit;
//        if ($model->status == -1){
            $model = ArticleCategory::find()->where(['status'=>[1,0]])->all();
//        }
        //将数据遍历到页面
        return $this->render('index',['model'=>$model]);
    }

    //添加功能
    public function actionAdd(){
        //实例化模型
        $model = new ArticleCategory();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //加载页面数据
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //修改功能
    public function actionEdit($id){
        $request = new Request();
        $model = ArticleCategory::findOne(['id'=>$id]);
        if ($request->isPost){
            $model->load($request->post());
            //验证数据
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success
                ','修改成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('edit',['model'=>$model]);
    }

    //删除功能
    public function actionDelete($id){
        //根据id删除
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        //提示跳转
//        \Yii::$app->session->setFlash('danger','删除成功');
//        //跳转页面
//        return $this->redirect(['article-category/index']);
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}