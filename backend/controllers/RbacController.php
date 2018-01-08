<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;

class RbacController extends Controller{
    //RBAC
    public function actionIndex(){
        $authManager = \Yii::$app->authManager;
        $model = $authManager->getPermissions();
        return $this->render('index',['model'=>$model]);
    }

    public function actionTest(){
        $authManager = \Yii::$app->authManager;
        //添加权限,创建一个权限
        $permission = new Permission();
        $permission->name = 'user/add';
        $permission->description = '';
        //保存到数据表
        $authManager->add($permission);

        //删除权限,修改权限
        //先获取该权限
        $permission = $authManager->getPermission('user/add');
        //修改
        $permission->description = '添加用户权限';
        //保存
        $authManager->update('user/add',$permission);

        //删除
        $authManager->remove($permission);

        //获取所有权限
        $authManager->getPermissions();

        //添加角色
        //创建角色
        $role = new Role();
        $role->name = '网管';
        $role->description = '管理网络';
        //保存
        $authManager->add($role);
        //给角色关联权限
        $permission = $authManager->getPermission('user/add');
        $authManager->addChild($role,$permission);
        //取消关联
        $authManager->removeChild($role,$permission);
        $authManager->removeChildren($role);//取消所有权限
    }

    //添加权限
    public function actionAddPermission(){
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD_PERMISSION;
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['rbac/index']);
            }
        }
        return $this->render('add-permission',['model'=>$model]);
    }

    //修改权限
    public function actionEditPermission($name){
        $authManager = \Yii::$app->authManager;
        //添加权限,创建一个权限
        $permission = $authManager->getPermission($name);
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_EDIT_PERMISSION;
        $model->name = $permission->name;
        $model->description = $permission->description;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
               if ($model->update($name)){
                   \Yii::$app->session->setFlash('success', '修改成功');
                   return $this->redirect(['rbac/index']);
               }else{
                   \Yii::$app->session->setFlash('danger', '修改失败');
                   return $this->redirect(['rbac/index']);
               }
            }
        }
        return $this->render('edit-permission',['model'=>$model]);
    }

    //删除权限
    public function actionDelPermission($name){
        $model = new PermissionForm();
        $permission = new Permission();
        $model->del($name);
        \Yii::$app->session->setFlash('danger','删除成功');
        return $this->redirect(['rbac/index']);
    }

    //=====================角色管理===========================
    public function actionIndexRole(){
        $authManager = \Yii::$app->authManager;
        //显示
        $model = $authManager->getRoles();
        return $this->render('index-role',['model'=>$model]);
    }

    //添加角色
    public function actionAddRole(){
        $authManager = \Yii::$app->authManager;
        //显示
        $model = new RoleForm();
        $request = \Yii::$app->request;
        $role = new Role();
        //获取所有权限
        $permissions = $authManager->getPermissions();
        $permission = [];
//        var_dump($permissions);exit;
        foreach ($permissions as $per){
            $permission[$per->name] = $per->description;
        }
//        var_dump($per);exit;
        if ($request->isPost){
            $model->load($request->post());
//            var_dump($model);exit;
            if ($model->validate()){
                //创建角色
                $role->name = $model->name;
                $role->description = $model->description;
                //保存
                $authManager->add($role);
                //给角色关联权限
//                var_dump($model->permission);exit;
                if ($model->permission){
                    foreach ($model->permission as $permissionName){
                        $permission = $authManager->getPermission($permissionName);
//                    var_dump($permission);exit;
                        $authManager->addChild($role,$permission);
                    }
                }
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
        return $this->render('add-role',['model'=>$model,'permission'=>$permission]);
    }

    public function actionEditRole($name){
        $authManager = \Yii::$app->authManager;
        $role = $authManager->getRole($name);
        //显示
        $model = new RoleForm();
        $model->name = $role->name;
        $model->description = $role->description;
//        var_dump($role);exit;
        //获取该角色的权限
        $model->permission = [];
        $permissions = $authManager->getPermissionsByRole($role->name);
//        var_dump($permissions);exit;
        foreach ($permissions as $permission){
            $model->permission[] = $permission->name;
        }
        $permissions = $authManager->getPermissions();
        $permission = [];
//        var_dump($permissions);exit;
        foreach ($permissions as $per){
            $permission[$per->name] = $per->description;
        }
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                //保存角色信息
                $role->name = $model->name;
                $role->description = $model->description;
                $authManager->update($name,$role);
                //处理角色和权限的关系
                //去除角色关联的权限
                $authManager->removeChildren($role);
                //重新关联新的权限
                foreach ($model->permission as $permissionName){
                    $permission = $authManager->getPermission($permissionName);
                    $authManager->addChild($role,$permission);
                }
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['rbac/index-role']);
            }
        }
//        var_dump($model);exit;
        return $this->render('edit-role',['model'=>$model,'permission'=>$permission]);
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