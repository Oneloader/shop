<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m171220_071846_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'intro' => $this->string()->notNull()->comment('简介'),
            'sort' => $this->integer(11)->notNull()->comment('排序'),
            'status' => $this->integer(2)->notNull()->comment('状态(-1删除 0隐藏 1正常)'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
