<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invitation}}`.
 */
class m251222_161706_create_invitation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%invitation}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'slug' => $this->string(191)->notNull()->unique(),
            'bride_name' => $this->string(255)->notNull(),
            'groom_name' => $this->string(255)->notNull(),
            'event_date' => $this->integer()->notNull(),
            'venue' => $this->text()->null(),
            'cover_image' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx-invitation-event_date', '{{%invitation}}', 'event_date');
    }

    public function safeDown()
    {
        $this->dropTable('{{%invitation}}');
    }
}