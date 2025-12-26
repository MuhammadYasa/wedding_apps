<?php

use yii\db\Migration;

/**
 * Handles adding QR code and check-in columns to table `{{%guest}}`.
 */
class m251226_000000_add_qr_code_and_checkin_to_guest_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%guest}}', 'qr_code', $this->string(255)->after('token')->comment('QR Code unique identifier for check-in'));
        $this->addColumn('{{%guest}}', 'checked_in_at', $this->integer()->null()->after('qr_code')->comment('Check-in timestamp'));
        $this->addColumn('{{%guest}}', 'checked_in_by', $this->integer()->null()->after('checked_in_at')->comment('User ID who checked in the guest'));
        
        // Add index for QR code lookups
        $this->createIndex(
            'idx-guest-qr_code',
            '{{%guest}}',
            'qr_code',
            true // unique
        );
        
        // Add index for check-in queries
        $this->createIndex(
            'idx-guest-checked_in_at',
            '{{%guest}}',
            'checked_in_at'
        );
        
        // Foreign key for checked_in_by
        $this->addForeignKey(
            'fk-guest-checked_in_by',
            '{{%guest}}',
            'checked_in_by',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-guest-checked_in_by', '{{%guest}}');
        $this->dropIndex('idx-guest-checked_in_at', '{{%guest}}');
        $this->dropIndex('idx-guest-qr_code', '{{%guest}}');
        $this->dropColumn('{{%guest}}', 'checked_in_by');
        $this->dropColumn('{{%guest}}', 'checked_in_at');
        $this->dropColumn('{{%guest}}', 'qr_code');
    }
}
