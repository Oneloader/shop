<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\GoodsGallery;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class GoodsController extends Controller{

    public $enableCsrfValidation = false;
    //显示功能
    public function actionIndex(){
//        $name = \Yii::$app->request->get('name');
//        $sn = \Yii::$app->request->get('sn');
//        $shop_price = \Yii::$app->request->get('shop_price');
//        $model = Goods::find()->where(['like','name',$name])->andWhere(['like','sn',$sn])->andWhere(['like','shop_price',$shop_price])->all();
        $model = Goods::find()->all();
        return $this->render('index',['model'=>$model]);
    }

    //商品相册功能
    public function actionGallery($goods_id){
        $model = GoodsGallery::find()->where(['goods_id'=>$goods_id])->all();
//        var_dump($gal);exit;
        return $this->render('gallery',['model'=>$model,'goods_id'=>$goods_id]);
    }

//    商品相册添加功能
    public function actionGalleryAdd($id){
        $model = new GoodsGallery();
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/'.uniqid().'.'.$img->extension;
        if ($img->saveAs(\Yii::getAlias('@webroot').$fileName,0)){
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
                $model->path = $url;
                $model->goods_id = $id;
//                var_dump($model);exit;
                $model->save();
                return json_encode(['url'=>$url,'id'=>$id]);
            }
        }
    }


    //处理ajax文件上传
    public function actionUploader(){
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/upload/'.uniqid().'.'.$img->extension;
        if ($img->saveAs(\Yii::getAlias('@webroot').$fileName,0)){
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

    //UEditor
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "http://www.wangdishop.cn/",//图片访问路径前缀
                    "imagePathFormat" => "/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}",//上传保存路径
                    "imageMaxSize"            => 2048000,/*上传大小限制，单位B */
                    "imageAllowFiles"         => [
                        ".png",
                        ".jpg",
                        ".jpeg",
                        ".gif",
                        ".bmp"
                    ],
                    /* 上传图片格式显示 */
                ]
            ]
        ];
    }

    //添加功能
    public function actionAdd()
    {
        $model = new Goods();
        $intro = new GoodsIntro();
        $model->create_time = time();
        $request = \Yii::$app->request;
            $day = date('Y-m-d', time());
        if ($model->load($request->post()) && $model->validate()) {
            $goods_day_count = GoodsDayCount::find()->where(['day' => date('Y-m-d', time())])->one();
            if ($goods_day_count) {
                $goods_day_count->count += 1;
                $goods_day_count->save();
            }else{
                $goods_day_count = new GoodsDayCount();
                $goods_day_count->day = $day;
                $goods_day_count->count = 1;
                $goods_day_count->save();
            }
            $model->sn = date('Ymd').sprintf("%05d", $goods_day_count->count);
            $model->save();
            if ($intro->load($request->post()) && $model->validate()) {
                $intro->goods_id = $model->id;
                $intro->save();
            }
            \Yii::$app->session->setFlash('success', '添加成功');
            return $this->redirect(['index']);
        };
        return $this->render('add',['model'=>$model,'intro'=>$intro]);
    }

    //修改功能
    public function actionEdit($id){
        $request = new Request();
        //根据id获取数据
        $model = Goods::findOne(['id'=>$id]);
        $intro = GoodsIntro::findOne(['goods_id'=>$id]);
//        var_dump($intro);exit;
        //判断是否为post传输数据
        if ($request->isPost){
            //获取加载数据
            $model->load($request->post());
            if ($model->validate()){
                if ($intro->load($request->post()) && $model->validate()){
//                    var_dump($intro);exit;
                    $intro->updateAll(['content'=>$intro->content],['goods_id'=>$id]);
//                    $intro->save(false);
                }
                $model->save(false);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('edit',['model'=>$model,'intro'=>$intro]);
    }

    //删除功能
    public function actionDelete($id){
        Goods::updateAll(['status'=>-1],['id'=>$id]);
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['uploader','upload'],
            ]
        ];
    }

}