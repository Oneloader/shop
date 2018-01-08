<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput()->label('品牌名称');
echo $form->field($model,'intro')->textarea()->label('品牌简介');
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
<img id="img" src="$model->logo"/>
HTML;
$upload_url = \yii\helpers\Url::to(['brand/upload']);
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
    // $( '#'+file.id ).addClass('upload-state-done');
    // response.url;
    console.log(response);
    //回显
    $('#img').attr('src',response.url);
    //将上传成功的图片地址写入logo字段
    $('#brand-logo').val(response.url);
});
JS;
$this->registerJs($js);

//echo $form->field($model,'imgFile')->fileInput()->label('品牌LOGO');
echo $form->field($model,'sort')->textInput()->label('品牌排序');
echo $form->field($model,'status')->inline()->radioList([0=>'隐藏',1=>'正常'])->label('品牌状态');
echo '<button type="submit" class="btn btn-primary">添加</button>';
\yii\bootstrap\ActiveForm::end();