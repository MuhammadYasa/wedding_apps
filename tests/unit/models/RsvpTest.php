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
        verify($rsvp->hasErrors('invitation_id'))->true();
        verify($rsvp->hasErrors('name'))->true();
        verify($rsvp->hasErrors('email'))->true();
        verify($rsvp->hasErrors('attendance'))->true();
    }
    
    /**
     * Test valid RSVP creation
     */
    public function testValidRsvpCreation()
    {
        $rsvp = new Rsvp([
            'invitation_id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'attendance' => Rsvp::ATTENDANCE_ATTENDING,
            'guests_count' => 2,
            'message' => 'Looking forward to celebrate with you!',
        ]);
        
        verify($rsvp->validate())->true();
        verify($rsvp->attendance)->equals(Rsvp::ATTENDANCE_ATTENDING);
        verify($rsvp->guests_count)->equals(2);
    }
    
    /**
     * Test RSVP decline
     */
    public function testRsvpDecline()
    {
        $rsvp = new Rsvp([
            'invitation_id' => 1,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '08123456789',
            'attendance' => Rsvp::ATTENDANCE_NOT_ATTENDING,
            'message' => 'Unfortunately cannot make it',
        ]);
        
        verify($rsvp->validate())->true();
        verify($rsvp->attendance)->equals(Rsvp::ATTENDANCE_NOT_ATTENDING);
    }
    
    /**
     * Test RSVP belongs to invitation
     */
    public function testRsvpBelongsToInvitation()
    {
        $invitation = Invitation::findOne(1);
        
        if ($invitation) {
            $rsvp = new Rsvp([
                'invitation_id' => $invitation->id,
                'name' => 'Test Guest',
                'email' => 'testguest@example.com',
                'phone' => '08123456789',
                'attendance' => Rsvp::ATTENDANCE_ATTENDING,
                'guests_count' => 1,
            ]);
            
            verify($rsvp->validate())->true();
            verify($rsvp->invitation_id)->equals($invitation->id);
        }
    }
    
    /**
     * Test RSVP belongs to invitation (duplicate removed)
     */
    public function testRsvpBelongsToInvitationDuplicate()
    {
        $invitation = Invitation::findOne(1);
        
        if ($invitation) {
            $rsvp = new Rsvp([
                'invitation_id' => $invitation->id,
                'name' => 'Another Guest',
                'email' => 'anotherguest@example.com',
                'phone' => '08123456790',
                'attendance' => Rsvp::ATTENDANCE_ATTENDING,
                'guests_count' => 1,
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
            'invitation_id' => 1,
            'name' => 'Test Guest',
            'email' => 'testvalidation@example.com',
            'phone' => '08123456789',
            'attendance' => Rsvp::ATTENDANCE_ATTENDING,
            'guests_count' => 0, // Invalid (min 1)
        ]);
        
        verify($rsvp->validate())->false();
        verify($rsvp->hasErrors('guests_count'))->true();
        
        // Valid number
        $rsvp->guests_count = 2;
        verify($rsvp->validate())->true();
    }
}
