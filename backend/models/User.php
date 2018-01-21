<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {

    public $old_password;
    public $new_password;
    public $re_password;
    public $role;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    //验证规则
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'required'],
            [['re_password','old_password','new_password','role'],'safe'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            ['re_password', 'compare', 'compareAttribute'=>'new_password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password_hash' => '密码',
            'new_password' => '新密码',
            're_password' => '确认密码',
            'email' => '邮箱',
            'status' => '状态',
            'auth_key' => 'AuthKey',
            'password_reset_token' => 'password_reset_token',
        ];
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id'=>$id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    //获取该用户的菜单
    public function getMenus(){
        $menuItems = [];
        $menus = Menu::find()->where(['parent_id'=>0])->all();
        foreach ($menus as $menu){
            //获取该一级分类的子分类
            $children = Menu::find()->where(['parent_id'=>$menu->id])->all();
            $items = [];
            foreach ($children as $child){
                //判断用户是否有该菜单权限
                if (\Yii::$app->user->can($child->url)){
                    $items[] = ['label' => $child->label, 'url' => [$child->url]];
                }
            }
            //没有子菜单的一级菜单不需要显示
            if ($items){
                $menuItems[] = ['label' => $menu->label, 'items'=>$items];
            }
        }
        return $menuItems;
    }


}