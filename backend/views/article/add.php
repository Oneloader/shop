<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput()->label('文章名称');
echo $form->field($model,'intro')->textInput()->label('文章简介');
echo $form->field($model,'article_category_id')->dropDownList([1=>'科技',2=>'文化'])->label('文章分类');
echo $form->field($model,'sort')->textInput(['number'])->label('文章排序');
echo $form->field($model,'status')->inline()->radioList([1=>'正常',0=>'隐藏'])->label('文章状态');
echo '<button type="submit" class="btn btn-primary">添加</button>';
\yii\bootstrap\ActiveForm::end();