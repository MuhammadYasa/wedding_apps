<?php

namespace tests\unit\models;

use app\models\Guest;
use app\models\Invitation;

class GuestTest extends \Codeception\Test\Unit
{
    /**
     * Test guest validation rules
     */
    public function testGuestValidation()
    {
        $guest = new Guest();
        
        // Empty model should fail validation
        verify($guest->validate())->false();
        
        // Required fields
        verify($guest->hasErrors('invitation_id'))->true();
        verify($guest->hasErrors('name'))->true();
        verify($guest->hasErrors('phone'))->true();
    }
    
    /**
     * Test valid guest creation
     */
    public function testValidGuestCreation()
    {
        $guest = new Guest([
            'invitation_id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+6281234567890',
        ]);
        
        verify($guest->validate())->true();
        verify($guest->name)->equals('John Doe');
    }
    
    /**
     * Test email validation
     */
    public function testEmailValidation()
    {
        $guest = new Guest([
            'invitation_id' => 1,
            'name' => 'Jane Doe',
            'phone' => '081234567890',
            'email' => 'invalid-email',
        ]);
        
        verify($guest->validate())->false();
        verify($guest->hasErrors('email'))->true();
        
        // Valid email
        $guest->email = 'jane@example.com';
        verify($guest->validate())->true();
    }
    
    /**
     * Test guest belongs to invitation
     */
    public function testGuestBelongsToInvitation()
    {
        $invitation = Invitation::findOne(1);
        
        if ($invitation) {
            $guest = new Guest([
                'invitation_id' => $invitation->id,
                'name' => 'Test Guest',
                'phone' => '081234567890',
            ]);
            
            verify($guest->validate())->true();
            verify($guest->invitation_id)->equals($invitation->id);
        }
    }
    
    /**
     * Test token generation on save
     */
    public function testTokenGeneration()
    {
        $guest = new Guest([
            'invitation_id' => 1,
            'name' => 'Token Test Guest',
            'phone' => '081234567899',
        ]);
        
        // Token should be empty before save
        verify($guest->token)->null();
        
        // After calling beforeSave (simulating save), token should be generated
        $guest->beforeSave(true);
        verify($guest->token)->notEmpty();
        verify(strlen($guest->token))->equals(32);
    }
}
