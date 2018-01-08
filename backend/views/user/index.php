<html>
<h2 class="text-center">用户管理列表</h2>
<table class="table table-bordered" id="table">
    <tr>
        <th>用户名</th>
        <th>密码</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>最后登录时间</th>
        <th>最后登录IP</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $users):?>
        <tr id="<?=$users->id?>">
            <td><?=$users->username?></td>
            <td><?=$users->password_hash?></td>
            <td><?=$users->email?></td>
            <td><?=$users->status?></td>
            <td>TIME</td>
            <td>IP</td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['user/edit'],['class'=>'btn btn-success'])?>
                <a id="del" class="btn btn-danger">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="14" class="text-center">
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
