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
        verify($invitation->hasErrors('title'))->true();
        verify($invitation->hasErrors('bride_name'))->true();
        verify($invitation->hasErrors('bride_nickname'))->true();
        verify($invitation->hasErrors('groom_name'))->true();
        verify($invitation->hasErrors('groom_nickname'))->true();
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
            'venue' => 'Test Venue',
            'venue_address' => 'Test Location Address',
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
            'bride_nickname' => 'Sara',
            'groom_name' => 'Michael',
            'groom_nickname' => 'Mike',
            'event_date' => '2026-06-15',
        ]);
        
        $invitation->validate();
        
        // Slug should be auto-generated from nicknames
        verify($invitation->slug)->notEmpty();
        verify(strpos($invitation->slug, 'sara') !== false || strpos($invitation->slug, 'mike') !== false)->true();
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
     * Test invitation relationship with user
     */
    public function testInvitationUserRelationship()
    {
        $invitation = Invitation::findOne(['user_id' => 1]);
        
        if ($invitation) {
            verify($invitation->user)->notEmpty();
            verify($invitation->user)->isInstanceOf(User::class);
            verify($invitation->user_id)->equals(1);
        }
    }
}
