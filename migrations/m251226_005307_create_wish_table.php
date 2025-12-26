<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wish}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%invitation}}`
 * - `{{%guest}}`
 */
class m251226_005307_create_wish_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wish}}', [
            'id' => $this->primaryKey(),
            'invitation_id' => $this->integer()->notNull(),
            'guest_id' => $this->integer(),
            'name' => $this->string(255)->notNull(),
            'email' => $this->string(191),
            'message' => $this->text()->notNull(),
            'is_approved' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        // creates index for column `invitation_id`
        $this->createIndex(
            '{{%idx-wish-invitation_id}}',
            '{{%wish}}',
            'invitation_id'
        );

        // add foreign key for table `{{%invitation}}`
        $this->addForeignKey(
            '{{%fk-wish-invitation_id}}',
            '{{%wish}}',
            'invitation_id',
            '{{%invitation}}',
            'id',
            'CASCADE'
        );

        // creates index for column `guest_id`
        $this->createIndex(
            '{{%idx-wish-guest_id}}',
            '{{%wish}}',
            'guest_id'
        );

        // add foreign key for table `{{%guest}}`
        $this->addForeignKey(
            '{{%fk-wish-guest_id}}',
            '{{%wish}}',
            'guest_id',
            '{{%guest}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%invitation}}`
        $this->dropForeignKey(
            '{{%fk-wish-invitation_id}}',
            '{{%wish}}'
        );

        // drops index for column `invitation_id`
        $this->dropIndex(
            '{{%idx-wish-invitation_id}}',
            '{{%wish}}'
        );

        // drops foreign key for table `{{%guest}}`
        $this->dropForeignKey(
            '{{%fk-wish-guest_id}}',
            '{{%wish}}'
        );

        // drops index for column `guest_id`
        $this->dropIndex(
            '{{%idx-wish-guest_id}}',
            '{{%wish}}'
        );

        $this->dropTable('{{%wish}}');
    }
}
