<?php

namespace App\Controllers;

use \Core\View;
use App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }
    /**
     * Sign up new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);
        if($user->save()){
            $this->redirect('/signup/success');
        }
        else {
            view::renderTemplate('Signup/new.html',[
            'user' => $user]);
        }
    }
    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
            view::renderTemplate('Signup/success.html');
    }

}
