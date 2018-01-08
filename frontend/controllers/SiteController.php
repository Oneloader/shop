<?php
namespace frontend\controllers;

use backend\models\Cart;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\Member;
use Codeception\Module\Redis;
use backend\models\Address;
use frontend\models\Order;
use frontend\models\OrderGoods;
use Yii;
use yii\base\InvalidParamException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\SignatureHelper;
use yii\web\Cookie;

/**
 * Site controller
 */
class SiteController extends Controller
{
//    public function
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
//                    [
//                        'actions' => ['register'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionList($id)
    {
        //判断是二级分类还是三级
        $cate = GoodsCategory::findOne(['id'=>$id]);
        if ($cate->depth == 2){
            //再根据三级分类id查找商品
            //三级分类
            $ids = [$id];
        }else{
            //二级分类
            //获取该二级分类下面的三级分类
            $categories = $cate->children()->select('id')->andWhere(['depth'=>2])->asArray()->all();
            $ids = ArrayHelper::map($categories,'id','id');
        }
        $goods = Goods::find()->where(['in','goods_category_id',$ids])->all();
        return $this->render('list',['goods'=>$goods]);
    }

    //商品详情页面
    public function actionGoods($id){
        $good = Goods::findOne(['id'=>$id]);
        $gallery = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        $intro = GoodsIntro::findOne(['goods_id'=>$id]);
//        var_dump($good->name);exit;
        return $this->render('goods',['good'=>$good,'gallery'=>$gallery,'intro'=>$intro]);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    //注册页面
    public function actionRegister()
    {
        $model = new Member();
//        var_dump($model);exit;
        $request = Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post(), '');
//            var_dump($model);exit;
            if ($model->validate()) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->save();
                return $this->redirect(['index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('register');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $request = Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post(),'');
            if ($model->login()){
                //登录成功时,判断cookie中是否有数据
                //如果有,就将cookie里的购物车信息保存到数据表
                $cookies = Yii::$app->request->cookies;
                if ($cookies->has('cart')){
                    //读取cookie
                    $cookie = $cookies->getValue('cart');
                    $cart = unserialize($cookie);
                    foreach ($cart as $key=>$value){
                        $member_id = Yii::$app->user->identity->getId();
                        $good = Cart::findOne(['goods_id'=>$key,'member_id'=>$member_id]);
                        //判断用户购物车里是否拥有相同商品
                        if ($good){
                            $num = $good->amount + $value;
                            Cart::updateAll(['amount'=>$num],['goods_id'=>$key,'member_id'=>$member_id]);
                        }else{
                            $goods = new Cart();
                            $goods->goods_id = $key;
                            $goods->amount = $value;
                            $goods->member_id = $member_id;
                            $goods->save();
                        }
                    }
                }else{
                    $cart = [];
                }

                return $this->redirect(['site/index']);
            } else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    //关闭csfr验证
    public $enableCsrfValidation=false;

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['site/index']);
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    //阿里大鱼短信功能
    public function actionSms($num){
//        var_dump($num);exit;
        //正则表达式 验证电话号码
        if(strlen($num) == "11")
        {
//上面部分判断长度是不是11位
            $n = preg_match_all("/^1[3|4|5|6|7|8][0-9]\d{4,8}$/",$num,$array);
//            var_dump($n); exit;//看看是不是找到了,如果找到了,就会输出电话号码的
            //        return '电话号码不正确';
            if ($n == 1){
                $code = rand(100000,999999);
                $result = Yii::$app->sms->send($num,['code'=>$code]);
//                var_dump($result);exit;
                if ($result->Code == 'OK'){
                    //短信发送成功
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1');
                    $redis->set('code_'.$num,$code,5*60);
                    return 'true';
                }else{
                    //短信发送失败
                    return '短信发送失败';
                }
            }else{
                return '电话号码不正确';
            }
        }else
        {
            return '电话号码不够11位数';
        }
    }

    //验证用户名唯一
    public function actionOnlyUser($username){
//        echo 1;die;
        $user = Member::findOne(['username'=>$username]);
//        var_dump($user);exit;
        if ($user){
            //已存在
            echo 'false';
        }else{
            //不存在
            echo 'true';
        }
    }

    //验证短信验证码
    public function actionCheck($sms,$num){
//        var_dump($sms,$num);exit;
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $chaptcha = $redis->get('code_'.$num);
        if ($chaptcha != $sms){
            return '短信验证码不正确';
        }
    }

    /**
     * @inheritdoc
     */
    //图形验证码
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }

    //收货地址管理
    public function actionAddress(){
        $user = Yii::$app->user->identity->getId();
        $model = Address::find()->where(['member_id'=>$user])->all();
        return $this->render('address',['model'=>$model]);
    }

    //新增收货地址管理
    public function actionAddAddress(){
        $request = Yii::$app->request;
        $model = new Address();
//        var_dump($request->isPost);exit;
        if ($request->isPost){
            $model->load($request->post(), '');
//            var_dump($model);exit;
            if ($model->validate()){
                if ($model->default == 1){
                    $all = Address::find()->all();
                    foreach ($all as $a){
//                        var_dump($a);exit;
                        $a->default = 0;
                        $a->save();
                    }
                }
                $user = Yii::$app->user->identity->getId();
                $model->member_id = $user;
                $model->save();
                return $this->redirect(['site/address']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('address');
    }

    //修改收货地址
    public function actionEditAddress($id){
        $model = Address::find()->all();
        $re = Address::findOne(['id'=>$id]);
        $request = Yii::$app->request;
        if ($request->isPost){
            $re->load($request->post(), '');
            if ($re->validate()){
                if ($re->default == 1){
                    $all = Address::find()->all();
                    foreach ($all as $a){
                        $a->default = 0;
                        $a->save();
                    }
                }
                $re->save();
                return $this->redirect(['site/address']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('edit-address',['model'=>$model,'re'=>$re]);
    }

    //点击设为默认地址,修改默认选项
    public function actionEditDefault($id){
        $re = Address::findOne(['id'=>$id]);
        $request = Yii::$app->request;
        if ($request->isGet){
            $re->load($request->get(), '');
            if ($re->validate()) {
                    $all = Address::find()->all();
                    foreach ($all as $a) {
                        $a->default = 0;
                        $a->save();
                    }
                $re->default = 1;
                $re->save();
                return $this->redirect(['site/address']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
    }

    //点击删除,删除收货地址数据
    public function actionDelAddress($id){
        $re = Address::findOne(['id'=>$id]);
        $re->delete();
        return $this->redirect(['site/address']);
    }

    //刷新页面,浏览次数加1
    public function actionRead(){
        $id = $_POST['id'];
        $good = Goods::findOne(['id'=>$id]);
//        var_dump($good);exit;
        $request = Yii::$app->request;
        if ($request->isPost){
            $good->load($request->post());
            if ($good->validate()){
                $good->view_times = $good->view_times +1;
//                var_dump($good->view_times);exit;
                $good->save();
                echo json_encode($good->view_times);
            }
        }
    }

    //购物车添加成功页面
    public function actionAddToCart($goods_id,$amount){
        //商品添加到购物车
        if (Yii::$app->user->isGuest){
            //未登录,将购物车数据保存到cookie
            //先查看cookie,是否有商品
            $cookies = Yii::$app->request->cookies;
            if ($cookies->has('cart')){
                $cookie = $cookies->getValue('cart');
                $cart = unserialize($cookie);
            }else{
                $cart = [];
            }
            //写入数据到cookie
            //判断是否已经存在该商品,如果存在在存在的数量基础上加上本次添加的数量
            if (array_key_exists($goods_id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
            $cookies = Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            $cookies->add($cookie);
        }else{
            //已登录,将购物车数据保存至数据表
            //将购物车数据添加到数据表
            $model = Cart::findOne(['goods_id'=>$goods_id]);
//            var_dump($model);exit;
            if ($model){
//                var_dump(1);
                $num = $model->amount+$amount;
                var_dump($num);
                Cart::updateAll(['amount'=>$num],['goods_id'=>$goods_id,'member_id'=>Yii::$app->user->id]);
            }else{
//                var_dump(2);
                $model = new Cart();
                $user = Yii::$app->user->id;
//                var_dump($user);exit;
                $model->goods_id = $goods_id;
                $model->amount = $amount;
                $model->member_id = $user;
//                var_dump($model);exit;
                $model->save();
            }
//            die;
        }
        //跳转到购物车
        return $this->redirect(['site/cart']);
    }

    //购物车页面
    public function actionCart(){
        //判断是否登录
        //未登录数据从cookie获取
        if (Yii::$app->user->isGuest){
            //读取cookie
            $cookies = Yii::$app->request->cookies;
            $cookie = $cookies->getValue('cart');
            if ($cookie){
                $cart = unserialize($cookie);
                $ids = array_keys($cart);
            }else{
                $cart = [];
                $ids = [];
            }
        }else{
            $user = Yii::$app->user->getId();
            //已登录,数据从数据表获取
            $carts = Cart::find()->where(['member_id'=>$user])->all();
//            var_dump($carts);exit;
//            $cart = [];
//            foreach ($carts as $ca){
//                $ids[] = $ca->goods_id;
//                $cart[$ca->goods_id]=$ca->amount;
//            }
            $ids = ArrayHelper::map($carts,'goods_id','goods_id');
//            var_dump($ids);exit;
            $cart = ArrayHelper::map($carts,'goods_id','amount');
//            var_dump($cart);exit;
//            $cartGoods = Cart::find()->where(['member_id'=>$user])->all();
//            foreach ($cartGoods as $goods){
//                $good = Goods::find()->where(['id'=>$goods->goods_id])->all();
//                $money = 0;
//                foreach ($good as $go){
//                $money += $go->shop_price;
//                }
//            }
//            var_dump($goods->goods_id);exit;
        }
        $model = Goods::find()->where(['in','id',$ids])->all();
        return $this->renderPartial('cart',['model'=>$model,'cart'=>$cart]);
    }

    //修改购物车商品数量
    public function actionChange(){
        $goods_id = Yii::$app->request->post('goods_id');
        $amount = Yii::$app->request->post('amount');
        if (Yii::$app->user->isGuest){
//            未登录修改cookie购物车数量
            //读取cookie
            //先查看cookie,是否有商品
            $cookies = Yii::$app->request->cookies;
            if ($cookies->has('cart')){
                $cookie = $cookies->getValue('cart');
                $cart = unserialize($cookie);
            }else{
                $cart = [];
            }
            //修改cookie数据
            $cart[$goods_id] = $amount;
            $cookies = Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            $cookies->add($cookie);
        }else{
            //如果登录,修改数据库数据
            $user = Yii::$app->user->getId();
            $model = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$user]);
//            $model = Cart::find()->where(['member_id'=>$user])->all();
            $model->amount = $amount;
            $model->save();
        }
    }

    //删除购物车商品
    public function actionDelCart($goods_id){
        if (Yii::$app->user->isGuest){
            //读取cookie
            //先查看cookie,是否有商品
            $cookies = Yii::$app->request->cookies;
            if ($cookies->has('cart')){
                $cookie = $cookies->getValue('cart');
                $cart = unserialize($cookie);
            }else{
                $cart = [];
            }
            $cookies = Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            $cookies->remove($cookie->name);
//            var_dump($cookie);exit;

            return $this->redirect(['site/cart']);
        }else{
            Cart::findOne(['goods_id'=>$goods_id])->delete();
            return $this->redirect(['site/cart']);
        }
    }

    //订单结算页面
    public function actionOrder(){
        //判断是否登录,如果未登录,引导用户进入登录页面
        if (Yii::$app->user->isGuest){
            return $this->render('login');
        }else{
            $user = Yii::$app->user->identity->getId();
            //已登录,数据从数据表获取
            $carts = Cart::find()->where(['member_id'=>$user])->all();
            $address = Address::find()->where(['member_id'=>$user])->all();
            $ids = ArrayHelper::map($carts,'goods_id','goods_id');
            $cart = ArrayHelper::map($carts,'goods_id','amount');
            $model = Goods::find()->where(['in','id',$ids])->all();
            if ($model){
                //接收用户提交数据
                $request = Yii::$app->request;

                if ($request->isPost){
                    $order = new Order();
                    $order->load($request->post(),'');
                    //获取订单用户id
                    $order->member_id = Yii::$app->user->identity->getId();
                    //获取收货地址信息
                    $address = Address::findOne(['id'=>$order->address_id]);
                    if($address){
                        $order->name = $address->name;
                        $order->province = $address->cmbProvince;
                        $order->city = $address->cmbCity;
                        $order->area = $address->cmbArea;
                        $order->area = $address->address;
                        $order->tel = $address->phone;
                    }else{
//                    Yii::$app->getSession()->setFlash('error', '当前用户还未拥有收货地址,请先设置一个收货地址');
                        return $this->redirect(['site/address']);
//                    $this->redirect( Yii::$app->getUrlManager()->createUrl(['site/address']));
                    }

                    //获取配送方式
                    $order->delivery_name = Order::$deliveries[$order->delivery_id][0];
                    $order->delivery_price = Order::$deliveries[$order->delivery_id][1];

                    //支付方式
                    $order->payment_name = Order::$pays[$order->payment_id][0];

                    //金额
                    $order->total = 0;

                    //订单状态
                    $order->status = 1;
//                var_dump();exit;
//                $order->save();
                    $order->create_time = time();

                    //操作数据库之前开启事务
                    $transaction = Yii::$app->db->beginTransaction();
                    try{
                        if ($order->validate()){
                            $order->save();
                        }
                        //遍历购物车商品信息,依次保存
                        $carts = Cart::find()->where(['member_id'=>$user])->all();
                        foreach ($carts as $cart){
                            $goods = Goods::findOne(['id'=>$cart->goods_id]);
                            //判断库存
                            if ($goods->stock >= $cart->amount){
                                //库存数足够用户购买
                                $orderGoods = new OrderGoods();
                                $orderGoods->order_id = $order->id;
                                $orderGoods->goods_id = $goods->id;
                                $orderGoods->goods_name = $goods->name;
                                $orderGoods->logo = $goods->logo;
                                $orderGoods->price = $goods->shop_price;
                                $orderGoods->amount = $cart->amount;
                                $orderGoods->total = $orderGoods->price*$orderGoods->amount;
                                $orderGoods->save();
                                $order->total += $orderGoods->total;
                                //扣减商品库存
                                $goods->stock -= $cart->amount;
                                $goods->save();
                            }else{
                                //库存不足用户购买 抛出异常
                                throw new Exception('库存不足,请重新购买');
                            }
                        }
                        //处理运费
//                        var_dump($order->total += $order->delivery_price);exit;
                        $order->total += $order->delivery_price;
                        $order->save();
                        //保存成功,清除购物车
                        foreach ($carts as $cart){
                            $cart->delete();
                        }
//                    var_dump($carts);exit;
                        //提交事务
                        $transaction->commit();
                    }catch (Exception $exception){
                        //回滚事务至事务开启前
                        $transaction->rollBack();
                    }
                    //跳转至购买成功页面
                    return $this->redirect(['site/succeed']);
                }
                return $this->renderPartial('order',['address'=>$address,'model'=>$model,'cart'=>$cart]);
            }else{
                return $this->redirect(['site/cart']);
            }
        }
    }

    //订单成功页面
    public function actionSucceed(){
        return $this->render('succeed');
    }

    //我的订单功能
    public function actionOrderList(){
        $user = Yii::$app->user->id;
        $order = Order::find()->where(['member_id'=>$user])->all();
//        var_dump($order);exit;
        return $this->render('order-list',['order'=>$order]);
//        return $this->redirect(['order-list','order'=>$order]);
    }

    //搜索框搜索
    public function actionSearch($keyword){
//        var_dump($keyword);exit;
        $goods = Goods::find()->where(['name'=>$keyword])->all();
        return $this->render('search',['goods'=>$goods]);
//        return $this->redirect(['site/goods-form','goods'=>$goods]);
    }
}
