<?php

use yii\db\Migration;

class m251224_025425_insert_dummy_rsvp_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Get first invitation ID
        $invitation = $this->db->createCommand('SELECT id FROM invitation LIMIT 1')->queryOne();
        $invitationId = $invitation ? $invitation['id'] : 1;

        $baseTime = time();
        
        // Indonesian names for dummy data
        $attendingNames = [
            'Ahmad Fauzi', 'Siti Nurhaliza', 'Budi Prasetyo', 'Dewi Lestari', 'Rizki Ramadhan',
            'Maya Sari', 'Andi Wijaya', 'Putri Ayu', 'Hendra Gunawan', 'Lina Marlina',
            'Fajar Nugroho', 'Ratna Dewi', 'Eko Prasetyo', 'Yuni Astuti', 'Doni Saputra',
            'Wati Kusuma', 'Agus Santoso', 'Sri Wahyuni', 'Bambang Hermawan', 'Fitri Handayani',
            'Teguh Santoso'
        ];
        
        $notAttendingNames = [
            'Rudi Hartono', 'Sari Indah', 'Joko Susilo', 'Rina Permata', 'Adi Nugraha',
            'Diah Puspita', 'Wahyu Pratama', 'Lia Amelia', 'Irfan Hakim', 'Nita Anggraini',
            'Arief Rahman', 'Sinta Dewi', 'Yoga Aditya', 'Dina Mariana', 'Farid Ahmad',
            'Wulan Sari', 'Reza Pahlevi', 'Anggi Rahayu', 'Hendri Kurniawan', 'Mira Lestari',
            'Satria Budi', 'Indri Wulandari', 'Firman Hidayat', 'Laila Fitria'
        ];

        // Insert 21 attending RSVPs
        foreach ($attendingNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            $phone = '08' . rand(100000000, 999999999);
            $guestsCount = rand(1, 5);
            $messages = [
                'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah mawaddah warahmah.',
                'Bahagia selalu untuk kalian berdua. Selamat menjalani hari bahagia!',
                'Semoga pernikahan kalian penuh berkah dan kebahagiaan. Aamiin.',
                'Congratulations! Wishing you a lifetime of love and happiness.',
                'Selamat ya! Semoga langgeng sampai kakek nenek.',
                null, null // Some without messages
            ];
            $message = $messages[array_rand($messages)];
            $token = \Yii::$app->security->generateRandomString(32);
            $createdAt = $baseTime - (rand(1, 30) * 86400) - rand(0, 86400); // Random time in last 30 days

            $this->insert('rsvp', [
                'invitation_id' => $invitationId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'attendance' => 'attending',
                'guests_count' => $guestsCount,
                'message' => $message,
                'token' => $token,
                'created_at' => $createdAt,
            ]);
        }

        // Insert 24 not attending RSVPs
        foreach ($notAttendingNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            $phone = rand(0, 1) ? '08' . rand(100000000, 999999999) : null;
            $messages = [
                'Maaf tidak bisa hadir, tapi doa terbaik selalu untuk kalian.',
                'Mohon maaf belum bisa hadir, semoga acaranya lancar.',
                'Selamat ya! Maaf tidak bisa datang karena ada acara keluarga.',
                'Sorry can\'t make it, but wishing you all the best!',
                null, null, null // Most without messages
            ];
            $message = rand(0, 2) == 0 ? $messages[array_rand($messages)] : null;
            $token = \Yii::$app->security->generateRandomString(32);
            $createdAt = $baseTime - (rand(1, 30) * 86400) - rand(0, 86400); // Random time in last 30 days

            $this->insert('rsvp', [
                'invitation_id' => $invitationId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'attendance' => 'not_attending',
                'guests_count' => 0,
                'message' => $message,
                'token' => $token,
                'created_at' => $createdAt,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('rsvp', ['like', 'email', '@example.com', false]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_025425_insert_dummy_rsvp_data cannot be reverted.\n";

        return false;
    }
    */
}
