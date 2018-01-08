<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput()->label('商品名称');
echo $form->field($intro,'content')->widget('kucha\ueditor\UEditor',[])->label('商品详情');
echo $form->field($model,'sn')->textInput()->label('货号');
echo $form->field($model,'logo')->hiddenInput();
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js',[
    //指定该js文件依赖
    'depends'=>\yii\web\JqueryAsset::className()
]);
echo
<<<HTML
<div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
<img id="img" src="$model->logo">
HTML;
$upload_url = \yii\helpers\Url::to(['goods/uploader']);
//var_dump($upload_url);exit;
$js =
    <<<JS
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: '/webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '{$upload_url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});
uploader.on( 'uploadSuccess', function( file,response ) {
    //回显
    $('#img').attr('src',response.url);
    //将上传成功的图片地址写入logo字段
    $('#goods-logo').val(response.url);
});
JS;
$this->registerJs($js);

//ztree
//加载ztree的css和js文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
//js
$nodes = \backend\models\GoodsCategory::getNodes();
$js = <<<JS
       var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {};
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
                onClick: function(event, treeId, treeNode) {
                    //点击节点,获取该节点id,赋值给$("#goodscategory-parent_id")
                    $("#goodscategory-parent_id").val(treeNode.id)
                }
            }
        };
        var zNodes = {$nodes};
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);
        var node = zTreeObj.getNodeByParam("id", 0, null);
        zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
echo <<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;

echo $form->field($model,'brand_id')->dropDownList([1=>'旺仔',2=>'盼盼'])->label('品牌');
echo $form->field($model,'market_price')->textInput()->label('市场价格');
echo $form->field($model,'shop_price')->textInput()->label('商品价格');
echo $form->field($model,'stock')->textInput()->label('库存');
echo $form->field($model,'is_on_sale')->inline()->radioList([1=>'在售',2=>'下架'])->label('是否在售');
echo $form->field($model,'status')->inline()->radioList([1=>'正常',2=>'回收站'])->label('状态');
echo $form->field($model,'sort')->textInput()->label('排序');

echo '<button type="submit" class="btn btn-primary">添加</button>';
\yii\bootstrap\ActiveForm::end();
