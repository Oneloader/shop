<?php
//微信开发
namespace frontend\controllers;

use backend\models\Article;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use frontend\models\Goods;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller{
    //必须关闭csrf
    public $enableCsrfValidation = false;

    //与微信交互
    public function actionIndex(){

        $app = new Application(\Yii::$app->params['wechat']);

// 从项目实例中得到服务端应用实例。
        $server = $app->server;

        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    //判断是否是点击事件
                    if ($message->Event == 'CLICK'){
                        switch ($message->EventKey){
                            case 'act':
                                $articles = \backend\models\Goods::find()->orderBy('create_time desc')->limit(5)->all();
                                $ar = [];
                                foreach ($articles as $article){
                                    $news = new News([
                                        'title'=>$article->name,
                                        'description' => '成都的火锅,世界的火锅',
                                        'url'         => 'http://shop.wangdi.fun/index.php?r=site/goods&id='.$article->id,
                                        'image'       => $article->logo,
                                    ]);
                                    $ar[] = $news;
                                }
                                return $ar;
                                break;

                            default:
                                return $message->EventKey;
                        }
                    }
                    return '收到事件消息';
                    break;
                case 'text':
//                    return '收到文字消息';
//                    return $message->Content;
//                    $text = new Text();
//                    $text->content = $message->Content;
//                    return $text;
                    //去redis查询是否有位置信息
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1');
                    if ($redis->exists('location_'.$message->FromUserName)){
                        $location = $redis->hGetAll('location_'.$message->FromUserName);
//                        $message->Content;
//                        http://api.map.baidu.com/place/v2/search?query=%E9%93%B6%E8%A1%8C&location=39.915,116.404&radius=2000&output=json&ak=CgaHVpFzBpGdAxZmiO7GtxhhkiGopcYj
                        $url = "http://api.map.baidu.com/place/v2/search?query={$message->Content}&location={$location['x']},{$location['y']}&radius=2000&output=json&ak=CgaHVpFzBpGdAxZmiO7GtxhhkiGopcYj&page_size=8&scope=2";
//                        return $url;
                        $json_str = file_get_contents($url);
                        $data = json_decode($json_str);
                        $re = [];
                        $images = [
                            'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1517073392&di=30a5c22d55e1b5ab0541b7032e3aee2f&imgtype=jpg&er=1&src=http%3A%2F%2Fdimg02.c-ctrip.com%2Fimages%2Ftg%2F863%2F853%2F499%2F71b80d8e5564437ebad267924b8deda2_R_228_10000.jpg',

                        ];
                        foreach ($data->results as $result){
                            $news = new News([
                                'title'=>$result->name,
                                'url'=>$result->detail_info->detail_url,
                                'image'=>$images[rand(0,3)],
                            ]);
                            $re[] = $news;
                        }
                        return $re;

                        //回复图文消息
//                        $news = new News([
//                            'title'       => '成都人的火锅',
//                            'description' => '成都的火锅,世界的火锅',
//                            'url'         => 'http://www.baidu.com',
//                            'image'       => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1517073392&di=30a5c22d55e1b5ab0541b7032e3aee2f&imgtype=jpg&er=1&src=http%3A%2F%2Fdimg02.c-ctrip.com%2Fimages%2Ftg%2F863%2F853%2F499%2F71b80d8e5564437ebad267924b8deda2_R_228_10000.jpg',
//                            // ...
//                        ]);
//                        return $news;
                    }else{
                        return '请先发送位置信息,才能查询附近';
                    }
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    //将用户的位置信息(Location_X Location_Y Label)保存到redis
                    $message->Location_Y;
                    $message->Location_X;
                    $message->Label;
                    $message->FromUserName;
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1');
//                    $redis->hSet('location_'.$message->FromUserName,'x',$message->Location_X);
                    $redis->hMset('location_'.$message->FromUserName,[
                        'x'=>$message->Location_X,
                        'y'=>$message->Location_Y,
                        'label'=>$message->Label,
                    ]);
                    return '已收到坐标消息,请发送您想查询的附近场景';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });

        $response = $server->serve();

        $response->send(); // Laravel 里请使用：return $response;
    }

    //设置菜单
    public function actionSetMenu(){
        $app = new Application(\Yii::$app->params['wechat']);

        $menu = $app->menu;
        $buttons = [
            [
                "type" => "view",
                "name" => "在线商城",
                "url"  => "http://shop.wangdi.fun/index.php"
            ],
            [
                "type" => "click",
                "name" => "最新活动",
                "key"  => "act"
            ],
            [
                "name"       => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => "http://shop.wangdi.fun/index.php"
                    ],
//                    [
//                        "type" => "view",
//                        "name" => "视频",
//                        "url"  => "http://v.qq.com/"
//                    ],
//                    [
//                        "type" => "click",
//                        "name" => "赞一下我们",
//                        "key" => "V1001_GOOD"
//                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        echo '菜单设置成功';
    }
    public function actionGetMenu(){
        $app = new Application(\Yii::$app->params['wechat']);

        $menu = $app->menu;
        $menus = $menu->all();
        var_dump($menus);
    }
}