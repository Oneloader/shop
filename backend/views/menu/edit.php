<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo '<h2 style="letter-spacing:7px;">修改菜单</h2>';
echo '<h6>¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯</h6>';
echo $form->field($model,'label')->textInput();
echo $form->field($model,'url')->textInput();
echo $form->field($model,'parent_id')->textInput();
echo $form->field($model,'sort')->textInput();
echo '<button type="submit" class="btn btn-primary">修改</button>';
\yii\bootstrap\ActiveForm::end();