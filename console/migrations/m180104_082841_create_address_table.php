<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180104_082841_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(),
            'member_id'=>$this->integer(),
            'cmbProvince'=>$this->string(),
            'cmbCity'=>$this->string(),
            'cmbArea'=>$this->string(),
            'address'=>$this->string(),
            'phone'=>$this->integer(11),
            'default'=>$this->integer(1),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
