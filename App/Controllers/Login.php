<?php
    namespace App\Controllers;
    
    use \Core\View;
    use \App\Models\User;
    use \App\Auth;
    use \App\Flash;
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
            $remember_me = isset($_POST['remember_me']);
            if($user){
                Auth::login($user, $remember_me);
                //Remember the login here
                $this->redirect(Auth::getReturnToPage());
                exit;
            } else {
                Flash::addMessage('Logowanie nie powiodło się. Spróbuj ponownie.', Flash::WARNING);
                View::renderTemplate('Login/new.html', [
                    'email' => $_POST['email'],
                    'remember_me' => $remember_me]);
            }
        }

        /**
         * Log out a user
         * @return void
         */
        public function destroyAction(){
            Auth::logout();
            $this->redirect('/login/show-logout-message');
        }
        
        /**
         * Show a "logged out" flash message and redirect to homepage. 
         * Necessary to use flash messages as they use the session and 
         * at the end of the logout method (destroyAction) the session 
         * is destroyed so a new session needs to be called in order 
         * to use the session.
         * @return void
         */
        public function showLogoutMessage(){
            Flash::addMessage('Wylogowano pomyślnie.');
            $this->redirect('/');
        }
    }
?>