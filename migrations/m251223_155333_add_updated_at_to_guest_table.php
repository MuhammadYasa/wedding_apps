<?php

use yii\db\Migration;

class m251223_155333_add_updated_at_to_guest_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%guest}}', 'updated_at', $this->integer()->null()->after('created_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%guest}}', 'updated_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251223_155333_add_updated_at_to_guest_table cannot be reverted.\n";

        return false;
    }
    */
}
