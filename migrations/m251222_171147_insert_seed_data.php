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

        // Insert admin user (password 'password123' - ganti setelah testing)
        $passwordHash = password_hash('yasak123', PASSWORD_DEFAULT);
        $authKey = bin2hex(random_bytes(16));
        $this->insert('{{%user}}', [
            'username' => 'nearhxh',
            'email' => 'nearhxh@example.com',
            'password_hash' => $passwordHash,
            'auth_key' => $authKey,
            'role' => 'admin',
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        // Insert a sample invitation
        $this->insert('{{%invitation}}', [
            'title' => 'Yasa & Devi  Wedding',
            'slug' => 'yasa-and-devi',
            'groom_name' => 'Yasa',
            'bride_name' => 'Devi',
            'event_date' => strtotime('2026-03-29 14:00:00'), // 29 Maret 2026
            'venue' => 'Jl. Contoh No.1, Surabaya, Indonesia',
            'cover_image' => null,
            'description' => 'Pernikahan Yasa & Devi',
            'created_at' => $time,
            'updated_at' => $time,
        ]);
    }

    public function safeDown()
    {
        // remove sample rsvp records (if any) that reference admin email (defensive)
        $this->delete('{{%rsvp}}', ['email' => 'nearhxh@example.com']);
        // remove sample invitation
        $this->delete('{{%invitation}}', ['slug' => 'yasa-and-devi']);
        // remove admin user
        $this->delete('{{%user}}', ['username' => 'nearhxh']);
    }
}