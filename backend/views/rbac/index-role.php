<html>
<h2 class="text-center">角色列表</h2>
<table class="table table-bordered display" id="example">
    <thead>
    <tr>
        <th>角色名</th>
        <th>详情</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($model as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['rbac/edit-role','name'=>$role->name],['class'=>'btn btn-success'])?>
                <?=\yii\bootstrap\Html::a('删除',['rbac/del-role','name'=>$role->name],['class'=>'btn btn-danger'])?>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
</html>
<?php
/**
// * @var $this Yii\web\View
// */
//$this->registerCssFile('@web/datatables/media/css/jquery.dataTables.css');
//$this->registerJsFile('@web/datatables/media/js/jquery.dataTables.js',[
//    //指定该js文件依赖
//    'depends'=>\yii\web\JqueryAsset::className()
//]);
//$js=<<<JS
//    $(document).ready( function () {
//        $('#table_id_example').DataTable();
//    } );
//    $('#example').dataTable({
//        "oLanguage": {
//            "sLengthMenu": "每页显示 _MENU_ 条记录",
//            "sZeroRecords": "对不起，查询不到任何相关数据",
//            "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_条记录",
//            "sInfoEmtpy": "找不到相关数据",
//            "sInfoFiltered": "数据表中共为 _MAX_ 条记录)",
//            "sProcessing": "正在加载中...",
//            "sSearch": "搜索",
//            "oPaginate": {
//            "sFirst": "第一页",
//            "sPrevious":" 上一页 ",
//            "sNext": " 下一页 ",
//            "sLast": " 最后一页 "
//            },
//        }
//    });
//JS;
//$this->registerJs($js);
