<?php
namespace frontend\models;

use backend\models\Member;
use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $rem;

    public function rules()
    {
        return [
            [['username','password'],'required'],
            [['rem'],'safe'],
        ];
    }

    //登录方法
    public function login(){
        //验证账号密码
        $user = Member::findOne(['username'=>$this->username]);
        if ($user){
//            var_dump($user->password_hash);exit;
            if (\Yii::$app->security->validatePassword($this->password,$user->password_hash)){
                //密码正确,登录
                if ($this->rem){
                    \Yii::$app->user->login($user,7*24*3600);
                }else{
                    \Yii::$app->user->login($user);
                }
                return true;
            }else{
                //密码不正确,返回错误信息
                $this->addError('password','密码不正确');
            }
        }else{
            //用户不存在
            $this->addError('username','用户名不存在');
        }
        return false;
    }

    //用户注销方法
    public function actionLogout(){
        \Yii::$app->user->logout();

    }
}