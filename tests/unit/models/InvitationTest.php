<?php

namespace tests\unit\models;

use app\models\Invitation;
use app\models\User;

class InvitationTest extends \Codeception\Test\Unit
{
    /**
     * Test invitation validation rules
     */
    public function testInvitationValidation()
    {
        $invitation = new Invitation();
        
        // Empty model should fail validation
        verify($invitation->validate())->false();
        
        // Required fields validation
        verify($invitation->hasErrors('user_id'))->true();
        verify($invitation->hasErrors('title'))->true();
        verify($invitation->hasErrors('bride_name'))->true();
        verify($invitation->hasErrors('groom_name'))->true();
        verify($invitation->hasErrors('event_date'))->true();
    }
    
    /**
     * Test valid invitation creation
     */
    public function testValidInvitation()
    {
        $invitation = new Invitation([
            'user_id' => 1,
            'title' => 'Test Wedding',
            'bride_name' => 'Jane',
            'groom_name' => 'John',
            'bride_nickname' => 'Jany',
            'groom_nickname' => 'Johnny',
            'event_date' => '2026-12-31',
            'event_time' => '14:00',
            'location' => 'Test Location',
            'is_active' => true,
        ]);
        
        verify($invitation->validate())->true();
        verify($invitation->hasErrors())->false();
    }
    
    /**
     * Test slug auto-generation
     */
    public function testSlugGeneration()
    {
        $invitation = new Invitation([
            'user_id' => 1,
            'title' => 'Beautiful Wedding Ceremony',
            'bride_name' => 'Sarah',
            'groom_name' => 'Michael',
            'event_date' => '2026-06-15',
        ]);
        
        $invitation->validate();
        
        // Slug should be auto-generated from title
        verify($invitation->slug)->notEmpty();
        verify($invitation->slug)->contains('beautiful-wedding-ceremony');
    }
    
    /**
     * Test invitation belongs to user
     */
    public function testInvitationBelongsToUser()
    {
        // Assuming user with id=1 exists from seed data
        $invitation = Invitation::findOne(['user_id' => 1]);
        
        if ($invitation) {
            verify($invitation->user)->notEmpty();
            verify($invitation->user)->isInstanceOf(User::class);
            verify($invitation->user->id)->equals(1);
        }
    }
    
    /**
     * Test email validation
     */
    public function testEmailValidation()
    {
        $invitation = new Invitation([
            'user_id' => 1,
            'title' => 'Test Wedding',
            'bride_name' => 'Jane',
            'groom_name' => 'John',
            'event_date' => '2026-12-31',
            'bride_email' => 'invalid-email',
            'groom_email' => 'also-invalid',
        ]);
        
        verify($invitation->validate())->false();
        verify($invitation->hasErrors('bride_email'))->true();
        verify($invitation->hasErrors('groom_email'))->true();
        
        // Valid emails
        $invitation->bride_email = 'jane@example.com';
        $invitation->groom_email = 'john@example.com';
        verify($invitation->validate(['bride_email', 'groom_email']))->true();
    }
}
