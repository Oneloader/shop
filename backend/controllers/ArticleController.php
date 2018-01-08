<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\web\Controller;
use yii\web\Request;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class ArticleController extends Controller{

    //列表显示功能
    public function actionIndex(){
        $model = Article::find()->where(['status'=>[1,0]])->all();
//        var_dump($det);exit;
        return $this->render('index',['model'=>$model]);
    }

    //文章添加
    public function actionAdd(){
        $model = new Article();
        $det = new ArticleDetail();
        $request = \Yii::$app->request;
        if ($request->isPost){
            //加载页面数据
            $model->load($request->post());
            if ($model->validate()){
                $model->create_time = time();
                $model->save();
                $id  = \Yii::$app->db->getLastInsertID();
                $det->article_id = $id ;
                $det->content = $model->intro;
                $det->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //文章修改功能
    public function actionEdit($id){
        $request = new Request();
        $model = Article::findOne(['id'=>$id]);
        $det = ArticleDetail::findOne(['article_id'=>$id]);
        if ($request->isPost){
            //加载页面数据
            $model->load($request->post());
            if ($model->validate()){
                $model->save();
                $det->article_id = $id ;
                $det->content = $model->intro;
                $det->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('edit',['model'=>$model]);
    }

    //删除功能
    public function actionDelete($id){
        //根据id删除
        $model = Article::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
    }

    public function actionQiniu(){
        //测试七牛
        // 需要填写你的 Access Key 和 Secret Key
            $accessKey ="Lwgm_rH5tFEh6FxSCOtc_mF3f_WeXVx1wszIGeiO";
            $secretKey = "1jrtnbDmh2LZ-V00Pb1kXMdl4oo9Y5_Fv0ZD1O8a";
        $bucket = "wangdi";
        // 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 要上传文件的本地路径

        $filePath = \Yii::getAlias('@web').'/upload/php-logo.png';
        // 上传到七牛后保存的文件名
        $key = 'my-php-logo.png';
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
        var_dump($err);
        } else {
        var_dump($ret);
        }
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