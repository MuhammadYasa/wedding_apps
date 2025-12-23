<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%rsvp}}`.
 */
class m251222_161739_create_rsvp_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%rsvp}}', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->notNull(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(191)->notNull(),
            'phone' => $this->string(50)->null(),
            'attendance' => $this->string(20)->notNull()->defaultValue('attending'), // attending, not_attending
            'guests_count' => $this->integer()->notNull()->defaultValue(1),
            'message' => $this->text()->null(),
            'token' => $this->string(128)->null(),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk-rsvp-invitation', '{{%rsvp}}', 'invitation_id', '{{%invitation}}', 'id', 'CASCADE', 'RESTRICT');
        $this->createIndex('idx-rsvp-invitation-email', '{{%rsvp}}', ['invitation_id', 'email'], true);
        $this->createIndex('idx-rsvp-attendance', '{{%rsvp}}', 'attendance');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-rsvp-invitation', '{{%rsvp}}');
        $this->dropTable('{{%rsvp}}');
    }
}