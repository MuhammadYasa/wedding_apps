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
            'bride_father' => $this->string(255)->null(),
            'bride_mother' => $this->string(255)->null(),
            'groom_father' => $this->string(255)->null(),
            'groom_mother' => $this->string(255)->null(),
            'event_date' => $this->integer()->notNull(),
            'event_time' => $this->string(50)->null(),
            'venue' => $this->text()->null(),
            'venue_address' => $this->text()->null(),
            'venue_map_url' => $this->string(500)->null(),
            'venue_lat' => $this->decimal(10, 8)->null(),
            'venue_lng' => $this->decimal(11, 8)->null(),
            'cover_image' => $this->string(255)->null(),
            'story' => $this->text()->null(),
            'description' => $this->text()->null(),
            'theme' => $this->string(50)->defaultValue('default'),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx-invitation-slug', '{{%invitation}}', 'slug');
        $this->createIndex('idx-invitation-event_date', '{{%invitation}}', 'event_date');
        $this->createIndex('idx-invitation-is_active', '{{%invitation}}', 'is_active');
    }

    public function safeDown()
    {
        $this->dropTable('{{%invitation}}');
    }
}