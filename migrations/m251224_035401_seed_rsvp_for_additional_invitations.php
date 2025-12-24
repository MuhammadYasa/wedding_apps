<?php

use yii\db\Migration;

/**
 * Class m251224_035401_seed_rsvp_for_additional_invitations
 * Seed RSVP data for additional invitations (Andi & Sari, Budi & Dewi)
 * Each invitation gets 40 attending + 20 not attending = 60 RSVPs
 */
class m251224_035401_seed_rsvp_for_additional_invitations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Get invitation IDs
        $invitation2 = $this->db->createCommand("SELECT id FROM invitation WHERE slug = 'andi-sari'")->queryScalar();
        $invitation3 = $this->db->createCommand("SELECT id FROM invitation WHERE slug = 'budi-dewi'")->queryScalar();

        if (!$invitation2 || !$invitation3) {
            echo "Error: Additional invitations not found!\n";
            return false;
        }

        echo "Seeding RSVPs for Invitation ID $invitation2 (Andi & Sari) and $invitation3 (Budi & Dewi)...\n";

        // Indonesian names pool
        $firstNames = [
            'Agus', 'Andi', 'Bayu', 'Budi', 'Citra', 'Dani', 'Dewi', 'Eka', 'Eni', 'Fajar',
            'Fitri', 'Gita', 'Hadi', 'Indra', 'Joko', 'Kartika', 'Linda', 'Maya', 'Nina', 'Putra',
            'Rina', 'Sari', 'Tono', 'Usman', 'Vina', 'Wati', 'Yanti', 'Zainal', 'Ayu', 'Bambang',
            'Candra', 'Diah', 'Eko', 'Farida', 'Gunawan', 'Hendra', 'Intan', 'Juli', 'Kurnia', 'Lestari'
        ];
        
        $lastNames = [
            'Santoso', 'Wijaya', 'Kurniawan', 'Pratama', 'Susanto', 'Budiman', 'Saputra', 'Gunawan',
            'Permata', 'Utama', 'Kusuma', 'Hakim', 'Rahman', 'Hidayat', 'Ramadan', 'Purnomo',
            'Setiawan', 'Anggraini', 'Rahayu', 'Suharto', 'Wibowo', 'Firmansyah', 'Nugraha', 'Lestari',
            'Putri', 'Cahyadi', 'Mahardika', 'Suryanto', 'Nurdin', 'Kartika'
        ];

        $data = [];
        
        // Generate RSVPs for both invitations
        foreach ([$invitation2, $invitation3] as $invitationId) {
            // 40 attending
            for ($i = 0; $i < 40; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $name = $firstName . ' ' . $lastName;
                $email = strtolower(str_replace(' ', '.', $name)) . rand(1, 999) . '@example.com';
                $phone = '08' . rand(1, 9) . rand(100000000, 999999999);
                $randomDate = strtotime('2025-12-' . rand(2, 23) . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':00');
                $guestsCount = rand(1, 4);
                
                $data[] = [
                    $invitationId,
                    $name,
                    $email,
                    $phone,
                    'attending',
                    $guestsCount,
                    $randomDate,
                ];
            }
            
            // 20 not attending
            for ($i = 0; $i < 20; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $name = $firstName . ' ' . $lastName;
                $email = strtolower(str_replace(' ', '.', $name)) . rand(1, 999) . '@example.com';
                $phone = rand(0, 1) === 1 ? ('08' . rand(1, 9) . rand(100000000, 999999999)) : null;
                $randomDate = strtotime('2025-12-' . rand(2, 23) . ' ' . rand(0, 23) . ':' . rand(0, 59) . ':00');
                
                $data[] = [
                    $invitationId,
                    $name,
                    $email,
                    $phone,
                    'not_attending',
                    0,
                    $randomDate,
                ];
            }
        }

        // Insert all data
        $this->batchInsert('rsvp', 
            ['invitation_id', 'name', 'email', 'phone', 'attendance', 'guests_count', 'created_at'],
            $data
        );

        echo "Successfully seeded 120 RSVPs (60 per invitation: 40 attending, 20 not attending)\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Get invitation IDs
        $invitation2 = $this->db->createCommand("SELECT id FROM invitation WHERE slug = 'andi-sari'")->queryScalar();
        $invitation3 = $this->db->createCommand("SELECT id FROM invitation WHERE slug = 'budi-dewi'")->queryScalar();

        if ($invitation2) {
            $this->delete('rsvp', ['invitation_id' => $invitation2]);
        }
        if ($invitation3) {
            $this->delete('rsvp', ['invitation_id' => $invitation3]);
        }

        echo "RSVPs for additional invitations removed.\n";
    }
}
