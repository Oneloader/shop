<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_073407_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'label' => $this->string(),
            'url' => $this->string(),
            'parent_id' => $this->integer(),
            'sort' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
