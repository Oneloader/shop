<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput()->label('文章名称');
echo $form->field($model,'intro')->textarea()->label('文章简介');
echo $form->field($model,'sort')->textInput()->label('文章排序');
echo $form->field($model,'status')->inline()->radioList([0=>'隐藏',1=>'正常'])->label('文章状态');
echo '<button type="submit" class="btn btn-primary">修改</button>';
\yii\bootstrap\ActiveForm::end();