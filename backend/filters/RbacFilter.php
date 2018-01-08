<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{

    //操作执行之前
    public function beforeAction($action)
    {
        //判断当前用户是否拥有权限
        //return \Yii::$app->user->can($action->uniqueId);
        if (!\Yii::$app->user->can($action->uniqueId)){
            //没登录,引导用户登录
            if (\Yii::$app->user->isGuest){
                //跳转登录
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }

            //没有权限
            throw new HttpException(403,'该用户没有此操作权限');
        }
        return true;
    }
}