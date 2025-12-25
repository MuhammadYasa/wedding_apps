<?php

use yii\db\Migration;

class m251225_024655_add_google_id_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'google_id', $this->string(255)->null()->unique());
        $this->createIndex('idx-user-google_id', '{{%user}}', 'google_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-google_id', '{{%user}}');
        $this->dropColumn('{{%user}}', 'google_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251225_024655_add_google_id_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
