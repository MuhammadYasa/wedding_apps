<?php

use yii\db\Migration;

class m251224_095614_add_nickname_to_invitation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%invitation}}', 'bride_nickname', $this->string(100)->after('bride_name'));
        $this->addColumn('{{%invitation}}', 'groom_nickname', $this->string(100)->after('groom_name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%invitation}}', 'groom_nickname');
        $this->dropColumn('{{%invitation}}', 'bride_nickname');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_095614_add_nickname_to_invitation_table cannot be reverted.\n";

        return false;
    }
    */
}
