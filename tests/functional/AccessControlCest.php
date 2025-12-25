<?php

namespace tests\functional;

use app\models\User;
use tests\FunctionalTester;

class AccessControlCest
{
    /**
     * Test super user can access all pages
     */
    public function testSuperUserAccess(FunctionalTester $I)
    {
        // Login as super user
        $I->amLoggedInAs(User::findOne(['role' => User::ROLE_SUPER_USER]));
        
        // Super user can access all admin pages
        $I->amOnPage('/admin-user/index');
        $I->seeResponseCodeIs(200);
        
        $I->amOnPage('/admin-invitation/index');
        $I->seeResponseCodeIs(200);
        
        $I->amOnPage('/admin-guest/index');
        $I->seeResponseCodeIs(200);
        
        $I->amOnPage('/admin-gallery/index');
        $I->seeResponseCodeIs(200);
        
        $I->amOnPage('/admin-rsvp/index');
        $I->seeResponseCodeIs(200);
    }
    
    /**
     * Test client user access restrictions
     */
    public function testClientUserAccess(FunctionalTester $I)
    {
        // Login as client user
        $I->amLoggedInAs(User::findOne(['role' => User::ROLE_CLIENT]));
        
        // Client user CANNOT access user management
        $I->amOnPage('/admin-user/index');
        $I->seeResponseCodeIs(403); // Forbidden
        
        // Client CAN access own invitation management
        $I->amOnPage('/admin-invitation/index');
        $I->seeResponseCodeIs(200);
        
        // Client CAN access guest management
        $I->amOnPage('/admin-guest/index');
        $I->seeResponseCodeIs(200);
        
        // Client CAN access gallery
        $I->amOnPage('/admin-gallery/index');
        $I->seeResponseCodeIs(200);
        
        // Client CAN access RSVP
        $I->amOnPage('/admin-rsvp/index');
        $I->seeResponseCodeIs(200);
    }
    
    /**
     * Test guest (not logged in) redirected to login
     */
    public function testGuestRedirectToLogin(FunctionalTester $I)
    {
        // Try to access admin pages without login
        $I->amOnPage('/admin-user/index');
        $I->seeInCurrentUrl('/site/login');
        
        $I->amOnPage('/admin-invitation/index');
        $I->seeInCurrentUrl('/site/login');
        
        $I->amOnPage('/admin-guest/index');
        $I->seeInCurrentUrl('/site/login');
    }
    
    /**
     * Test inactive user cannot login
     */
    public function testInactiveUserCannotLogin(FunctionalTester $I)
    {
        // Find a client user and make them inactive
        $user = User::findOne(['role' => User::ROLE_CLIENT]);
        if ($user) {
            $user->is_active = false;
            $user->save(false);
            
            // Try to login
            $I->amOnPage('/site/login');
            $I->fillField('LoginForm[username]', $user->username);
            $I->fillField('LoginForm[password]', 'password');
            $I->click('Login');
            
            // Should see error message
            $I->see('Akun Anda tidak aktif');
            
            // Restore user status
            $user->is_active = true;
            $user->save(false);
        }
    }
    
    /**
     * Test super user not affected by status
     */
    public function testSuperUserNotAffectedByStatus(FunctionalTester $I)
    {
        $user = User::findOne(['role' => User::ROLE_SUPER_USER]);
        if ($user) {
            // Even if set to inactive, super user should still work
            $user->is_active = false;
            $user->save(false);
            
            // Should still be able to login
            $I->amLoggedInAs($user);
            $I->amOnPage('/admin-user/index');
            $I->seeResponseCodeIs(200);
            
            // Restore
            $user->is_active = true;
            $user->save(false);
        }
    }
}
