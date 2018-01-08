<html>
<h2 class="text-center">文章分类列表</h2>
<table class="table table-bordered" id="table">
    <tr>
        <th>文章名称</th>
        <th>文章简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $art):?>
        <tr id="<?=$art->id?>">
            <td><?=$art->name?></td>
            <td><?=$art->intro?></td>
            <td><?=$art->sort?></td>
            <td>
                <?php if ($art->status == 1){
                    echo "正常";
                }else if ($art->status == 0){
                    echo "隐藏";
                }?>
            </td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$art->id],['class'=>'btn btn-success'])?>
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
$url = \yii\helpers\Url::to(['article-category/delete']);
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