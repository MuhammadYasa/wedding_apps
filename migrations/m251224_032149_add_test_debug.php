<?php

use yii\db\Migration;

class m251224_032149_add_test_debug extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251224_032149_add_test_debug cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251224_032149_add_test_debug cannot be reverted.\n";

        return false;
    }
    */
}
