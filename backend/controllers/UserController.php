<?php
namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\User;
use yii\rbac\Role;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller{

    //用户列表
    public function actionIndex(){
        $model = User::find()->all();
        return $this->render('index',['model'=>$model]);
    }

    //添加用户
    public function actionAdd(){
        $model = new User();
        $request = \Yii::$app->request;
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        $role = [];
        foreach ($roles as $rol){
            $role[$rol->name] = $rol->name;
        }
//        var_dump($role);exit;
        if ($request->isPost) {
            $model->load($request->post());
            $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
            if ($model->validate()) {
//                $ro = new Role();
//                var_dump($ro);exit;
//                $ro->name = $model->role;
//                var_dump($ro);exit;
//                var_dump($ro);exit;
                $model->save();
                if ($model->role) {
//                    $i = 0;
                    foreach ($model->role as $roleName){
                    $role = $authManager->getRole($roleName);
//                        var_dump($roleName);exit;
                    $user = User::findOne(['username' => $model->username]);
//                        var_dump($user->id);exit;
                    $authManager->assign($role, $user->id);
                    }
                }
                return $this->redirect(['index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        };
        return $this->render('add',['model'=>$model,'role'=>$role]);
    }

//    //修改用户
    public function actionEdit($name){
        if (\Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }
        $name = \Yii::$app->user->identity->username;
        $request = new Request();
        //根据id获取数据
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        $role = [];
        foreach ($roles as $rol){
            $role[$rol->name] = $rol->description;
        }
        $model = User::findOne(['username'=>$name]);
//        $password_hash = $model->password_hash;
        $rol = $authManager->getRolesByUser($model->id);
        if ($request->isPost){
            $model->load($request->post());
                if ($model->validate()){
                    $model->save();
                    $model = User::findOne(['username'=>$model->username]);
                    $authManager->assign($model->role,$model->id);
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }
            }
        return $this->render('edit',['model'=>$model,'role'=>$role]);
    }

    //修改用户密码
    public function actionEditPwd(){
        if (\Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }
        $name = \Yii::$app->user->identity->username;
        $request = new Request();
        //根据id获取数据
        $model = User::findOne(['username'=>$name]);
        $password_hash = $model->password_hash;
        if ($request->isPost){
            $model->load($request->post());
            if(\Yii::$app->security->validatePassword($model->old_password, $password_hash)){
                if ($model->validate()){
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->new_password);
                    $model->save();
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }
            }else{
                $model->addError('old_password','旧密码错误');
            }
        }
        return $this->render('edit',['model'=>$model,'role'=>$role]);
    }

    //删除功能
//    public function actionDelete(){
//        User::updateAll([]);
//    }

//登录功能
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
//        $model->remember = '';
        if ($request->isPost)
            $model->load($request->post());
//        var_dump($model);exit;
            if ($model->login()) {
                \Yii::$app->session->setFlash('success','欢迎'.\Yii::$app->user->identity->username.'访问');
                    $this->redirect(['index']);
                } else {
                    return $this->render('login', [
                    'model' => $model,
                    ]);
                }
                return $this->render('login',['model'=>$model]);
        }

        //退出功能
    public function actionLogout(){
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('danger','用户已退出');
        return $this->redirect(['user/index']);
    }
}