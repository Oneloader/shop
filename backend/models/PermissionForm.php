<?php
namespace backend\models;

use yii\base\Model;
use yii\rbac\Permission;
class PermissionForm extends Model{
    //字段
    public $name;
    public $description;

    //场景
    const SCENARIO_ADD_PERMISSION = 'add-permission';//添加权限场景
    const SCENARIO_EDIT_PERMISSION = 'edit-permission';//添加权限场景

    //规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
            //添加时验证
            ['name','only','on'=>self::SCENARIO_ADD_PERMISSION],
            //修改时验证
            ['name','validateName','on'=>self::SCENARIO_EDIT_PERMISSION],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'权限名',
            'description'=>'权限详情',
        ];
    }

    public function only(){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($this->name);
        if ($permission){
            $this->addError('name','权限已存在');
        }
    }

    public function validateName(){
        //名称是否修改
        $authManager = \Yii::$app->authManager;
        $oldName = \Yii::$app->request->get('name');
        //修改时名称是否存在
        if ($oldName != $this->name){
            $per = $authManager->getPermission($this->name);
            if ($per){
                $this->addError('name','已存在');
                return false;
            }
        }
    }

    //添加功能
    public function save(){
        $authManager = \Yii::$app->authManager;
        //保存
        $permission = new Permission();
        $permission->name = $this->name;
        $permission->description = $this->description;
        return $authManager->add($permission);
    }

    //修改功能
    public function update($name){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $permission->name = $this->name;
        $permission->description = $this->description;
        return $authManager->update($name,$permission);
    }

    //删除功能
    public function del($name){
        $authManager = \Yii::$app->authManager;
        //删除
        $permission = $authManager->getPermission($name);
        return $authManager->remove($permission);
    }
}