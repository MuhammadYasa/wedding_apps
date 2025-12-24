<?php

use yii\db\Migration;

/**
 * Class m251224_035912_update_guests_count_max_2
 * Update existing RSVP data to enforce max 2 guests per invitation
 */
class m251224_035912_update_guests_count_max_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Update all RSVPs with guests_count > 2 to be 2
        $this->update('rsvp', ['guests_count' => 2], 'guests_count > 2');
        
        echo "Updated all RSVPs to have maximum 2 guests.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Cannot revert this migration as original guests_count values are lost.\n";
        return true;
    }
}
