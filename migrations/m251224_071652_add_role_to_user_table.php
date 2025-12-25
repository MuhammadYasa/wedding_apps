<?php

use yii\db\Migration;

class m251224_071652_add_role_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'role', $this->string(50)->notNull()->defaultValue('client'));
        
        // Update existing users
        $this->update('{{%user}}', ['role' => 'super_user'], ['username' => 'admin']);
        $this->update('{{%user}}', ['role' => 'client'], ['username' => 'demo']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_071652_add_role_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
