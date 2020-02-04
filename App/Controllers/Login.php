<?php
    namespace App\Controllers;
    use \Core\View;
    use \App\Models\User;
    use \App\Auth;
    /**
     * Login controller
    * PHP version 7.4.2
    */
    class Login extends \Core\Controller{
        /**
         * Show the login page
         * @return void
         */
        public function newAction(){
            View::renderTemplate('Login/new.html');
        }
        /**
         * log in a user
         * @return void
         */
        public function createAction(){
            $user = User::authenticate($_POST['email'], $_POST['password']);
            if($user){
                Auth::login($user);
                $this->redirect(Auth::getReturnToPage());
                exit;
            } else {
                View::renderTemplate('Login/new.html', ['email' => $_POST['email'],]);
            }
        }
        /**
         * Log out a user
         * @return void
         */
        public function destroyAction(){
            Auth::logout();
            $this->redirect('/login');
        }
    }
?>