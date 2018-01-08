<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入上传类
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
class BrandController extends Controller{
    public $enableCsrfValidation = false;
    //显示功能
    public function actionIndex(){
        $model = Brand::find()->where(['status'=>[1,0]])->all();
//        var_dump($model);exit;
        return $this->render('index',['model'=>$model]);
    }

    //处理ajax文件上传
    public function actionUpload(){
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/'.uniqid().'.'.$img->extension;
        if ($img->saveAs(\Yii::getAlias('@webroot').$fileName,0)){
            //七牛上传

            // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="Lwgm_rH5tFEh6FxSCOtc_mF3f_WeXVx1wszIGeiO";
            $secretKey = "1jrtnbDmh2LZ-V00Pb1kXMdl4oo9Y5_Fv0ZD1O8a";
            $bucket = "wangdi";
            $domain  = 'p1bgfgwn5.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$fileName;
            // 上传到七牛后保存的文件名
            $key = $fileName;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                //上传失败
                return Json::encode(['error'=>1]);
            } else {
                $url = "http://{$domain}/{$key}";
                //上传成功
                 return json_encode(['url'=>$url]);
            }
        }
    }

    //添加功能
    public function actionAdd(){
        $model = new Brand();
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
//            var_dump($model->logo);exit;
            if ($model->validate()){
                $model->save(false);
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        };
        return $this->render('add',['model'=>$model]);
    }

    //修改功能
    public function actionEdit($id){
        $request = new Request();
        //根据id获取数据
        $model = Brand::findOne(['id'=>$id]);
//        var_dump($model);exit;
        if ($request->isPost){
            $model->load($request->post());
            //先处理图片再验证
            if ($model->validate()){
                $model->save(false);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('edit',['model'=>$model]);
    }

    //删除功能
    public function actionDelete($id){
        Brand::updateAll(['status'=>-1],['id'=>$id]);
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