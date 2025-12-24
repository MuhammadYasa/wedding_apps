<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%chat}}`.
 */
class m251224_063137_create_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%chat}}', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->notNull(),
            'guest_name' => $this->string(255)->notNull(),
            'message' => $this->text()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // Add foreign key for invitation_id
        $this->addForeignKey(
            'fk-chat-invitation_id',
            '{{%chat}}',
            'invitation_id',
            '{{%invitation}}',
            'id',
            'CASCADE'
        );

        // Add index for faster queries
        $this->createIndex(
            'idx-chat-invitation_id',
            '{{%chat}}',
            'invitation_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%chat}}');
    }
}
