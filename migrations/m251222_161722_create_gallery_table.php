<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%gallery}}`.
 */
class m251222_161722_create_gallery_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%gallery}}', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->notNull(),
            'filename' => $this->string(255)->notNull(),
            'caption' => $this->string(255)->null(),
            'sort_order' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk-gallery-invitation', '{{%gallery}}', 'invitation_id', '{{%invitation}}', 'id', 'CASCADE', 'RESTRICT');
        $this->createIndex('idx-gallery-invitation-sort', '{{%gallery}}', ['invitation_id', 'sort_order']);
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-gallery-invitation', '{{%gallery}}');
        $this->dropTable('{{%gallery}}');
    }
}