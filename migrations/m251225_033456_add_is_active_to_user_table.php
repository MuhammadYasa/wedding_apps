<?php

use yii\db\Migration;

class m251225_033456_add_is_active_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'is_active', $this->boolean()->notNull()->defaultValue(1));
        $this->createIndex('idx-user-is_active', '{{%user}}', 'is_active');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-is_active', '{{%user}}');
        $this->dropColumn('{{%user}}', 'is_active');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251225_033456_add_is_active_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
