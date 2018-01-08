<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput()->label('商品分类名称');
echo $form->field($model,'parent_id')->hiddenInput()->label('商品上层分类');

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
        var node = zTreeObj.getNodeByParam("id", $model->id, null);
        zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
echo <<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;

echo $form->field($model,'intro')->textInput()->label('简介');
echo '<button type="submit" class="btn btn-primary">修改</button>';
\yii\bootstrap\ActiveForm::end();