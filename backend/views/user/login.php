<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput()->label('用户名');
echo $form->field($model,'password')->passwordInput()->label('密码');
echo $form->field($model,'rem')->checkboxList([1=>'记住密码'])->label('记住密码');
echo '<button type="submit" class="btn btn-primary">登录</button>';
\yii\bootstrap\ActiveForm::end();
