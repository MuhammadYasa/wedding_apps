<?php

use yii\db\Migration;

class m251224_063555_seed_chat_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $invitationId = 1; // Yasa and Devi wedding
        $baseTime = time() - 3600; // 1 hour ago
        
        $messages = [
            ['Ibu Siti Nurhaliza', 'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah mawaddah warahmah 💕', $baseTime],
            ['Bapak Ahmad', 'MasyaAllah, barakallah! Semoga langgeng sampai kakek nenek 👴👵', $baseTime + 300],
            ['Tante Dewi', 'Congratulations! Wish you both a lifetime of love and happiness! 🎉', $baseTime + 600],
            ['Kak Rini', 'Selamat ya! Udah ditunggu-tunggu nih moment nya 😊', $baseTime + 900],
            ['Om Budi', 'Selamat menempuh babak baru kehidupan. Semoga bahagia selalu! 🎊', $baseTime + 1200],
            ['Mbak Sari', 'Wah akhirnya resmi! Selamat ya lovebirds 💑', $baseTime + 1500],
        ];
        
        foreach ($messages as $msg) {
            $this->insert('{{%chat}}', [
                'invitation_id' => $invitationId,
                'guest_name' => $msg[0],
                'message' => $msg[1],
                'created_at' => $msg[2],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%chat}}', ['invitation_id' => 1]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_063555_seed_chat_messages cannot be reverted.\n";

        return false;
    }
    */
}
