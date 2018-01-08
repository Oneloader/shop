<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller{
    //显示列表功能
    public function actionIndex(){
        $model = Menu::find()->all();
        return $this->render('index',['model'=>$model]);
    }

    //添加功能
    public function actionAdd(){
        $model = new Menu();
        $request = \Yii::$app->request;
        $parents = Menu::find()->all();
        $arr =[];
        array_unshift($arr,'顶级菜单');
        foreach ($parents as $parent){
            $arr[$parent->id] = $parent->label;
        }
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                return $this->redirect('index');
            }
        }
        return $this->render('add',['model'=>$model,'arr'=>$arr]);
    }

    //修改功能
    public function actionEdit($id){
        $request = new Request();
        $model = Menu::findOne(['id'=>$id]);
        if ($request->isPost){
            $model->load($request->post());
                if ($model->validate()){
                    $model->save();
                    return $this->redirect('index');
                }
        }
        return $this->render('edit',['model'=>$model]);
    }

    //删除功能
    public function actionDelete($id){
//        Menu::delete(['id'=>$id]);
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