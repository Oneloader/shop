<?php
/**
 * @var $this Yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput()->label('用户名');
echo $form->field($model,'password_hash')->passwordInput()->label('密码');
echo $form->field($model,'email')->textInput()->label('邮箱');
echo $form->field($model,'role')->inline()->checkboxList($role)->label('角色');
echo $form->field($model,'status')->inline()->radioList([0=>'隐藏',1=>'正常'])->label('用户状态');
echo '<button type="submit" class="btn btn-primary">添加</button>';
\yii\bootstrap\ActiveForm::end();
