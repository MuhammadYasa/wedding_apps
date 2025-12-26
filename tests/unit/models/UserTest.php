<?php

namespace tests\unit\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        verify($user = User::findIdentity(1))->notEmpty();
        verify($user->username)->equals('nearhxh');

        verify(User::findIdentity(999))->empty();
    }

    public function testFindUserByAccessToken()
    {
        // Get user and test with their actual auth_key
        $user = User::findOne(1);
        
        if ($user) {
            verify(User::findIdentityByAccessToken($user->auth_key))->notEmpty();
        }

        verify(User::findIdentityByAccessToken('non-existing'))->empty();        
    }

    public function testFindUserByUsername()
    {
        verify($user = User::findByUsername('nearhxh'))->notEmpty();
        verify(User::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        $user = User::findByUsername('nearhxh');
        
        if ($user) {
            // Test with actual auth_key from DB
            verify($user->validateAuthKey($user->auth_key))->notEmpty();
            verify($user->validateAuthKey('invalid-key'))->empty();

            // Password is 'yasak123' from seed data
            verify($user->validatePassword('yasak123'))->notEmpty();
            verify($user->validatePassword('wrongpassword'))->empty();
        }
    }

}
