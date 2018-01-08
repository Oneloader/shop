<html>
<h2 class="text-center">菜单列表</h2>
<table class="table table-striped table-hover" id="table">
    <tr>
        <th>菜单名</th>
        <th>路由/地址</th>
        <th>上级菜单</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $menu):?>
    <tr>
        <td><?=$menu->label?></td>
        <td><?=$menu->url?></td>
        <td><?=$menu->parent_id?></td>
        <td><?=$menu->sort?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$menu->id],['class'=>'btn btn-success'])?>
            <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$menu->id],['class'=>'btn btn-danger'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
</html>