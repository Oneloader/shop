<html>
<h2 class="text-center">商品分类管理</h2>
<table class="table table-bordered" id="table">
    <tr>
        <th>商品分类名称</th>
        <th>上级商品分类</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $goods):?>
    <tr id="<?=$goods->id?>">
        <td><?=$goods->name?></td>
        <td><?=$goods->parent_id?></td>
        <td><?=$goods->intro?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$goods->id],['class'=>'btn btn-success'])?>
            <a id="del" class="btn btn-danger">删除</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="4" class="text-center">
            <a type="button" class="btn btn-primary" href="<?=\yii\helpers\Url::to(['add'])?>">添加</a>
        </td>
    </tr>
</table>
<?php
$url = \yii\helpers\Url::to(['goods-category/delete']);
$js = <<<JS
    $("#table").on('click','#del',function() {
        var tr = $(this).closest('tr');
        if (confirm('确定删除?此操作不可逆!')){
            $.get('$url',{id:tr.attr('id')},function() {
                tr.remove();
            })
        }
    })
JS;
$this->registerJs($js);
