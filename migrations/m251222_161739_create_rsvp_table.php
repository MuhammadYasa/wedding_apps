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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%rsvp}}');
    }
}
