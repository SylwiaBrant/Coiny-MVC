<?php 
namespace App\Controllers;
use \Core\View;
use \App\Auth;
/**
 * Profile controller
 * PHP version 7.4.2
 */
class Profile extends Authenticated{
    /**
     * Show the profile
     * @return void
     */
    public function showSettingsAction(){
        View::renderTemplate('Profile/settings.html', [
            'user' => Auth::getUser()
        ]);
    }
}