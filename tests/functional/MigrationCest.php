<?php

namespace tests\functional;

use tests\FunctionalTester;

class MigrationCest
{
    /**
     * Test that all tables exist after migration
     */
    public function testTablesExist(FunctionalTester $I)
    {
        // Check if main tables exist
        $I->seeInDatabase('user', []);
        $I->seeInDatabase('invitation', []);
        $I->seeInDatabase('guest', []);
        $I->seeInDatabase('rsvp', []);
        $I->seeInDatabase('gallery', []);
    }
    
    /**
     * Test that seed data was inserted
     */
    public function testSeedDataExists(FunctionalTester $I)
    {
        // Check super user exists
        $I->seeInDatabase('user', [
            'role' => 'super_user',
            'username' => 'nearhxh'
        ]);
        
        // Check that at least one invitation exists
        $I->seeNumRecords(1, 'invitation');
    }
    
    /**
     * Test user table structure
     */
    public function testUserTableStructure(FunctionalTester $I)
    {
        // Check that user table has required columns
        $I->seeInDatabase('user', []);
        
        $user = $I->grabFromDatabase('user', 'id, username, email, role, google_id, is_active');
        $I->assertArrayHasKey('id', $user);
        $I->assertArrayHasKey('username', $user);
        $I->assertArrayHasKey('email', $user);
        $I->assertArrayHasKey('role', $user);
        $I->assertArrayHasKey('google_id', $user);
        $I->assertArrayHasKey('is_active', $user);
    }
    
    /**
     * Test invitation table structure
     */
    public function testInvitationTableStructure(FunctionalTester $I)
    {
        $invitation = $I->grabFromDatabase('invitation', 'id, user_id, title, slug, bride_name, groom_name, bride_nickname, groom_nickname, is_active');
        
        $I->assertArrayHasKey('id', $invitation);
        $I->assertArrayHasKey('user_id', $invitation);
        $I->assertArrayHasKey('title', $invitation);
        $I->assertArrayHasKey('slug', $invitation);
        $I->assertArrayHasKey('bride_name', $invitation);
        $I->assertArrayHasKey('groom_name', $invitation);
        $I->assertArrayHasKey('bride_nickname', $invitation);
        $I->assertArrayHasKey('groom_nickname', $invitation);
        $I->assertArrayHasKey('is_active', $invitation);
    }
}
