<?php

use yii\db\Migration;

class m251224_100834_update_title_length_in_invitation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Increase title column length to 500
        $this->alterColumn('{{%invitation}}', 'title', $this->string(500)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Revert to original length of 255
        $this->alterColumn('{{%invitation}}', 'title', $this->string(255)->notNull());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_100834_update_title_length_in_invitation_table cannot be reverted.\n";

        return false;
    }
    */
}
