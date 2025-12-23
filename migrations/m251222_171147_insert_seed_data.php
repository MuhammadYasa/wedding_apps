<?php

use yii\db\Migration;

class m251222_171147_insert_seed_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $time = time();

        // Insert admin user (password: yasak123 - ganti setelah testing)
        $this->insert('{{%user}}', [
            'username' => 'nearhxh',
            'email' => 'nearhxh@example.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('yasak123'),
            'auth_key' => Yii::$app->security->generateRandomString(32),
            'role' => 'admin',
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        // Insert sample invitations
        $invitationId = 1;
        $this->insert('{{%invitation}}', [
            'id' => $invitationId,
            'title' => 'Pernikahan Yasa & Devi',
            'slug' => 'yasa-and-devi',
            'groom_name' => 'Muhammad Yasa',
            'bride_name' => 'Devi Meildawati',
            'groom_father' => 'Muntaham',
            'groom_mother' => 'Siti Aisyah',
            'bride_father' => 'Bapak e wong wedok',
            'bride_mother' => 'Ibuk e wong wedok',
            'event_date' => strtotime('2026-03-29 14:00:00'),
            'event_time' => '14:00 - 17:00 WIB',
            'venue' => 'Resto Dito',
            'venue_address' => 'Jl. Raya Kenjeran No. 123, Surabaya, Jawa Timur 60117',
            'venue_map_url' => 'https://maps.google.com/?q=-7.2575,112.7521',
            'venue_lat' => -7.2575,
            'venue_lng' => 112.7521,
            'story' => 'Kami pertama kali bertemu di kampus pada tahun 2020. Dari pertemanan yang sederhana, Allah SWT mempertemukan kami dalam ikatan yang lebih dalam. Kami bersyukur atas semua momen indah yang telah kami lalui bersama dan sangat berbahagia dapat melanjutkan perjalanan hidup kami bersama.',
            'description' => 'Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i untuk hadir pada acara pernikahan kami.',
            'theme' => 'default',
            'is_active' => true,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        // Insert sample guests dengan token untuk WhatsApp sharing
        $guests = [
            ['name' => 'Bapak Ahmad & Keluarga', 'phone' => '081234567890', 'whatsapp' => '081234567890'],
            ['name' => 'Ibu Siti Nurhaliza', 'phone' => '082345678901', 'whatsapp' => '082345678901'],
            ['name' => 'Keluarga Besar Pak Budi', 'phone' => '083456789012', 'whatsapp' => '083456789012'],
            ['name' => 'Saudara Andi Wijaya', 'phone' => '084567890123', 'whatsapp' => '084567890123'],
            ['name' => 'Ibu Ratna & Suami', 'phone' => '085678901234', 'whatsapp' => '085678901234'],
        ];

        foreach ($guests as $guest) {
            $this->insert('{{%guest}}', [
                'invitation_id' => $invitationId,
                'name' => $guest['name'],
                'email' => null,
                'phone' => $guest['phone'],
                'whatsapp' => $guest['whatsapp'],
                'token' => Yii::$app->security->generateRandomString(32),
                'token_created_at' => $time,
                'created_at' => $time,
            ]);
        }

        // Insert sample RSVP data
        $this->insert('{{%rsvp}}', [
            'invitation_id' => $invitationId,
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'attendance' => 'attending',
            'guests_count' => 2,
            'message' => 'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah.',
            'created_at' => $time,
        ]);

        $this->insert('{{%rsvp}}', [
            'invitation_id' => $invitationId,
            'name' => 'Ani Widya',
            'email' => 'ani@example.com',
            'phone' => '082345678901',
            'attendance' => 'attending',
            'guests_count' => 1,
            'message' => 'Barakallahu lakuma wa baraka alaikuma. Selamat ya!',
            'created_at' => $time - 86400,
        ]);
    }

    public function safeDown()
    {
        // Remove sample data in reverse order
        $this->delete('{{%rsvp}}', ['invitation_id' => 1]);
        $this->delete('{{%guest}}', ['invitation_id' => 1]);
        $this->delete('{{%invitation}}', ['slug' => 'yasa-and-devi']);
        $this->delete('{{%user}}', ['username' => 'nearhxh']);
    }
}