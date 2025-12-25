<?php

use yii\db\Migration;

class m251224_071828_add_user_id_to_invitation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%invitation}}', 'user_id', $this->integer()->null());
        
        // Add foreign key
        $this->addForeignKey(
            'fk-invitation-user_id',
            '{{%invitation}}',
            'user_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );
        
        // Assign existing invitations to admin user (super_user)
        $this->update('{{%invitation}}', ['user_id' => 1]); // admin user
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-invitation-user_id', '{{%invitation}}');
        $this->dropColumn('{{%invitation}}', 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_071828_add_user_id_to_invitation_table cannot be reverted.\n";

        return false;
    }
    */
}
