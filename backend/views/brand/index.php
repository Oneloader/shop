<html>
<h2 class="text-center">品牌列表</h2>
<table class="table table-bordered" id="table">
    <tr>
        <th>品牌名称</th>
        <th>品牌简介</th>
        <th>品牌LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $brand):?>
        <tr id="<?=$brand->id?>">
            <td><?=$brand->name?></td>
            <td><?=$brand->intro?></td>
            <td>
                <img src="<?=$brand->logo?>" alt="" width="100px">
            </td>
            <td><?=$brand->sort?></td>
            <td>
                <?php if ($brand->status == 1){
                    echo "正常";
                }else if ($brand->status == 0){
                    echo "隐藏";
                }?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brand->id],['class'=>'btn btn-success'])?>
                <a id="del" class="btn btn-danger">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6" class="text-center">
            <a type="button" class="btn btn-primary" href="<?=\yii\helpers\Url::to(['add'])?>">添加</a>
        </td>
    </tr>
</table>
</html>
<?php
$url = \yii\helpers\Url::to(['brand/delete']);
$js =
    <<<JS
    $('#table').on('click','#del',function() {
        var tr = $(this).closest('tr');
        if (confirm('确定删除?该操作不可逆!')){
            $.get('$url',{id:tr.attr('id')},function () {
                tr.remove();
            })
        }
    })
JS;
$this->registerJs($js);