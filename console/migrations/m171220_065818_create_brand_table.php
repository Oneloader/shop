<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m171220_065818_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('名称'),
            'intro' => $this->string()->notNull()->comment('简介'),
            'logo' => $this->string()->notNull()->comment('LOGO图片'),
            'sort' => $this->integer()->notNull()->comment('排序'),
            'status' => $this->smallInteger()->notNull()->comment('状态(-1删除 0隐藏 1正常)'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
