<?php

use yii\db\Migration;

/**
 * Class m251224_035129_insert_additional_invitations
 * Insert 2 additional wedding invitation dummy data
 */
class m251224_035129_insert_additional_invitations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Insert additional wedding invitations
        $this->batchInsert('invitation', 
            ['title', 'slug', 'bride_name', 'groom_name', 'event_date', 'event_time', 'venue', 'venue_address', 'description', 'cover_image', 'theme', 'is_active', 'created_at', 'updated_at'],
            [
                [
                    'Pernikahan Andi & Sari',
                    'andi-sari',
                    'Sari Anggraini',
                    'Andi Pratama',
                    strtotime('2025-02-14 10:00:00'),
                    '10:00 WIB',
                    'Gedung Sasana Kriya',
                    'Jl. Taman Mini Indonesia Indah, Jakarta Selatan',
                    'Dengan memohon Rahmat dan Ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami.',
                    'wedding2.jpg',
                    'default',
                    1,
                    time(),
                    time(),
                ],
                [
                    'Pernikahan Budi & Dewi',
                    'budi-dewi',
                    'Dewi Kusuma',
                    'Budi Santoso',
                    strtotime('2025-03-21 09:00:00'),
                    '09:00 WIB',
                    'Hotel Grand Mercure',
                    'Jl. Merdeka No. 2, Bandung',
                    'Dengan memohon Rahmat dan Ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri acara pernikahan kami.',
                    'wedding3.jpg',
                    'default',
                    1,
                    time(),
                    time(),
                ],
            ]
        );

        echo "2 additional wedding invitations inserted successfully.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Delete the inserted invitations
        $this->delete('invitation', ['title' => [
            'Pernikahan Andi & Sari',
            'Pernikahan Budi & Dewi',
        ]]);

        echo "Additional invitations removed.\n";
    }
}
