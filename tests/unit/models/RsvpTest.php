<?php

namespace tests\unit\models;

use app\models\Rsvp;
use app\models\Guest;
use app\models\Invitation;

class RsvpTest extends \Codeception\Test\Unit
{
    /**
     * Test RSVP validation rules
     */
    public function testRsvpValidation()
    {
        $rsvp = new Rsvp();
        
        // Empty model should fail validation
        verify($rsvp->validate())->false();
        
        // Required fields
        verify($rsvp->hasErrors('guest_id'))->true();
        verify($rsvp->hasErrors('invitation_id'))->true();
        verify($rsvp->hasErrors('will_attend'))->true();
    }
    
    /**
     * Test valid RSVP creation
     */
    public function testValidRsvpCreation()
    {
        $rsvp = new Rsvp([
            'guest_id' => 1,
            'invitation_id' => 1,
            'will_attend' => 1,
            'number_of_guests' => 2,
            'message' => 'Looking forward to celebrate with you!',
        ]);
        
        verify($rsvp->validate())->true();
        verify($rsvp->will_attend)->equals(1);
        verify($rsvp->number_of_guests)->equals(2);
    }
    
    /**
     * Test RSVP decline
     */
    public function testRsvpDecline()
    {
        $rsvp = new Rsvp([
            'guest_id' => 1,
            'invitation_id' => 1,
            'will_attend' => 0,
            'message' => 'Unfortunately cannot make it',
        ]);
        
        verify($rsvp->validate())->true();
        verify($rsvp->will_attend)->equals(0);
    }
    
    /**
     * Test RSVP belongs to guest
     */
    public function testRsvpBelongsToGuest()
    {
        $guest = Guest::findOne(1);
        
        if ($guest) {
            $rsvp = new Rsvp([
                'guest_id' => $guest->id,
                'invitation_id' => 1,
                'will_attend' => 1,
            ]);
            
            verify($rsvp->validate())->true();
            verify($rsvp->guest_id)->equals($guest->id);
        }
    }
    
    /**
     * Test RSVP belongs to invitation
     */
    public function testRsvpBelongsToInvitation()
    {
        $invitation = Invitation::findOne(1);
        
        if ($invitation) {
            $rsvp = new Rsvp([
                'guest_id' => 1,
                'invitation_id' => $invitation->id,
                'will_attend' => 1,
            ]);
            
            verify($rsvp->validate())->true();
            verify($rsvp->invitation_id)->equals($invitation->id);
        }
    }
    
    /**
     * Test number of guests validation
     */
    public function testNumberOfGuestsValidation()
    {
        $rsvp = new Rsvp([
            'guest_id' => 1,
            'invitation_id' => 1,
            'will_attend' => 1,
            'number_of_guests' => -1, // Invalid
        ]);
        
        verify($rsvp->validate())->false();
        verify($rsvp->hasErrors('number_of_guests'))->true();
        
        // Valid number
        $rsvp->number_of_guests = 3;
        verify($rsvp->validate())->true();
    }
}
