<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%guest}}`.
 */
class m251222_161749_create_guest_table extends Migration
{
    /**
     * {@inheritdoc}
     */
   public function safeUp()
    {
        $this->createTable('{{%guest}}', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(191)->null(),
            'phone' => $this->string(50)->null(),
            'whatsapp' => $this->string(50)->null(),
            'slug' => $this->string(191)->null(),
            'token' => $this->string(128)->notNull()->unique(),
            'token_created_at' => $this->integer()->null(),
            'viewed_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk-guest-invitation', '{{%guest}}', 'invitation_id', '{{%invitation}}', 'id', 'CASCADE', 'RESTRICT');
        $this->createIndex('idx-guest-invitation-token', '{{%guest}}', ['invitation_id', 'token']);
        $this->createIndex('idx-guest-token', '{{%guest}}', 'token');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-guest-invitation', '{{%guest}}');
        $this->dropTable('{{%guest}}');
    }
}