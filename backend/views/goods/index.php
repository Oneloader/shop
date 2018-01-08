<html>
<h2 class="text-center">商品管理</h2>
<form action="" method="get">
    商品名:<input type="text" value="" name="name">
    货号:<input type="text" value="" name="sn">
    价格:<input type="text" value="" name="sn">
    <input type="submit" value="搜索">
</form>
<table class="table table-bordered" id="table">
    <tr>
        <th>商品名称</th>
        <th>货号</th>
        <th>LOGO</th>
        <th>分类</th>
        <th>品牌</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>浏览次数</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $goods):?>
        <tr id="<?=$goods->id?>">
            <td><?=$goods->name?></td>
            <td><?=$goods->sn?></td>
            <td><img src="<?=$goods->logo?>" alt="" width="100px"></td>
            <td><?=$goods->goods_category_id?></td>
            <td><?=$goods->brand_id?></td>
            <td><?=$goods->market_price?></td>
            <td><?=$goods->shop_price?></td>
            <td><?=$goods->stock?></td>
            <td><?=$goods->is_on_sale?></td>
            <td><?=$goods->status?></td>
            <td><?=$goods->sort?></td>
            <td><?=date('Y-m-d H:i:s',$goods->create_time)?></td>
            <td><?=$goods->view_times?></td>
            <td>
                <?=\yii\bootstrap\Html::a('相册',['goods/gallery','goods_id'=>$goods->id],['class'=>'btn btn-warning'])?>
                <?=\yii\bootstrap\Html::a('修改',['goods/edit','id'=>$goods->id],['class'=>'btn btn-success'])?>
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
