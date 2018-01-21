<?php
namespace frontend\controllers;

use backend\models\Address;
use backend\models\Member;
use backend\models\User;
use Codeception\Module\Redis;
use frontend\models\LoginForm;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller{
    //关闭跨站请求
    public $enableCsrfValidation = false;
    //初始化方法
    public function init()
    {
        parent::init();
        //设置响应数据的格式
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    //用户注册
    public function actionMemberRegister(){
        $result = [
            'error_code'=>1,
            'msg'=>'',
            'data'=>[]
        ];
        if (\Yii::$app->request->isPost){
            $user = new Member();
            $user->username = \Yii::$app->request->post('username');
            $user->password_hash = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password_hash'));
            $user->email = \Yii::$app->request->post('email');
            if ($user->validate()){
                $user->save();
                //注册成功
                $result['error_code']=0;
                $result['msg']='注册成功';
            }else{
                //注册失败
                $result['msg']=$user->getErrors();
            }
        }else{
            $result['msg']='请使用POST请求';
        }
        return $result;
    }

    //用户登录
    public function actionMemberLogin(){
        $result = [
            'error_code'=>1,
            'msg'=>'',
            'data'=>[]
        ];
        if (\Yii::$app->request->isPost){
//            $username = \Yii::$app->request->post('username');
//            $password_hash = \Yii::$app->request->post('password_hash');
            $request = \Yii::$app->request;
            $model = new LoginForm();
            $model->load($request->post(),'');
            if ($user = $model->login()){
                $result['error_code'] = 0;
                $result['msg'] = '登陆成功';
                $token = md5($user->id.time());
                //将token保存到redis
                $redis = new \Redis();
                $redis->set('token_'.$token,$user->id,7*24*3600);
                $result['data']['token'] = $token;
                $result['data']['user'] = $user;
            }else{
                $result['msg'] = $model->getErrors();
            }
            return $result;
        }
    }

    //添加地址
    public function actionAddAddress(){
        $result = [
            'error_code'=>1,
            'msg'=>'',
            'data'=>[]
        ];
        $name = \Yii::$app->request->post('name');
        $tel = \Yii::$app->request->post('tel');
        $uid = \Yii::$app->request->post('uid');
        $token = \Yii::$app->request->post('token');
        //验证token
        $redis = new \Redis();
        $to = $redis->get('token_'.$token);
        if ($to){
            $redis->expire('token_'.$token,7*24*3600);
            //token有效
            //保存新地址
            $address = new Address();
            $address->load(\Yii::$app->request->post(),'');
            $address->member_id = $to;
            if ($address->validate()){
                $address->save();
                //保存成功
                $result['error_code'] = 0;
                $result['msg'] = '收货地址保存成功';
            }else{
                //地址参数验证失败
                $result['msg'] = $address->getErrors();
            }
        }else{
            //token无效或已过期
            $result['msg'] = 'token无效或已过期';
        }
        return $result;
    }
}