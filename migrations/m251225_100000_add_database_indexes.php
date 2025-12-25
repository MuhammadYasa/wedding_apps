<?php

use yii\db\Migration;

/**
 * Add database indexes for performance optimization (Day 10)
 */
class m251225_100000_add_database_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // User table indexes (skip email and google_id as they already have unique indexes)
        $this->createIndexSafe('idx-user-role', '{{%user}}', 'role');

        // Invitation table indexes (skip slug as it already has unique index)
        $this->createIndexSafe('idx-invitation-user_id', '{{%invitation}}', 'user_id');
        $this->createIndexSafe('idx-invitation-is_active', '{{%invitation}}', 'is_active');
        $this->createIndexSafe('idx-invitation-event_date', '{{%invitation}}', 'event_date');

        // Guest table indexes (skip token as it already has unique index)
        $this->createIndexSafe('idx-guest-invitation_id', '{{%guest}}', 'invitation_id');
        $this->createIndexSafe('idx-guest-slug', '{{%guest}}', 'slug');
        $this->createIndexSafe('idx-guest-email', '{{%guest}}', 'email');

        // RSVP table indexes
        $this->createIndexSafe('idx-rsvp-invitation_id', '{{%rsvp}}', 'invitation_id');
        $this->createIndexSafe('idx-rsvp-email', '{{%rsvp}}', 'email');
        $this->createIndexSafe('idx-rsvp-attendance', '{{%rsvp}}', 'attendance');
        $this->createIndexSafe('idx-rsvp-token', '{{%rsvp}}', 'token');
        $this->createIndexSafe('idx-rsvp-invitation_email', '{{%rsvp}}', ['invitation_id', 'email']);

        // Gallery table indexes (if exists)
        $tableSchema = $this->db->getTableSchema('{{%gallery}}');
        if ($tableSchema !== null) {
            $this->createIndexSafe('idx-gallery-invitation_id', '{{%gallery}}', 'invitation_id');
            $this->createIndexSafe('idx-gallery-sort_order', '{{%gallery}}', 'sort_order');
        }
    }

    /**
     * Create index safely (skip if already exists)
     */
    private function createIndexSafe($name, $table, $columns)
    {
        try {
            $this->createIndex($name, $table, $columns);
        } catch (\Exception $e) {
            echo "    > index $name already exists ... skipping\n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // User indexes
        $this->dropIndexSafe('idx-user-role', '{{%user}}');

        // Invitation indexes
        $this->dropIndexSafe('idx-invitation-user_id', '{{%invitation}}');
        $this->dropIndexSafe('idx-invitation-is_active', '{{%invitation}}');
        $this->dropIndexSafe('idx-invitation-event_date', '{{%invitation}}');

        // Guest indexes
        $this->dropIndexSafe('idx-guest-invitation_id', '{{%guest}}');
        $this->dropIndexSafe('idx-guest-slug', '{{%guest}}');
        $this->dropIndexSafe('idx-guest-email', '{{%guest}}');

        // RSVP indexes
        $this->dropIndexSafe('idx-rsvp-invitation_id', '{{%rsvp}}');
        $this->dropIndexSafe('idx-rsvp-email', '{{%rsvp}}');
        $this->dropIndexSafe('idx-rsvp-attendance', '{{%rsvp}}');
        $this->dropIndexSafe('idx-rsvp-token', '{{%rsvp}}');
        $this->dropIndexSafe('idx-rsvp-invitation_email', '{{%rsvp}}');

        // Gallery indexes (if exists)
        $tableSchema = $this->db->getTableSchema('{{%gallery}}');
        if ($tableSchema !== null) {
            $this->dropIndexSafe('idx-gallery-invitation_id', '{{%gallery}}');
            $this->dropIndexSafe('idx-gallery-sort_order', '{{%gallery}}');
        }
    }

    /**
     * Drop index safely (skip if doesn't exist)
     */
    private function dropIndexSafe($name, $table)
    {
        try {
            $this->dropIndex($name, $table);
        } catch (\Exception $e) {
            echo "    > index $name doesn't exist ... skipping\n";
        }
    }
}
