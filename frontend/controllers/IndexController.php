<?php
namespace frontend\controllers;

use frontend\models\Index;
use yii\web\Controller;

class IndexController extends Controller{

    public function actionIndex(){
        $model = Index::find()->all();
        return $this->render('index',['model'=>$model]);
    }

    public function actionRegister(){
        $model = Index::find()->all();
        return $this->render('register',['model'=>$model]);
    }


}