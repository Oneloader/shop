<h2>修改用户密码</h2>
<br>
<br>
<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'old_password')->passwordInput()->label('旧密码');
echo $form->field($model,'new_password')->passwordInput()->label('新密码');
echo $form->field($model,'re_password')->passwordInput()->label('确认新密码');
echo '<button type="submit" class="btn btn-primary">修改密码</button>';
\yii\bootstrap\ActiveForm::end();
