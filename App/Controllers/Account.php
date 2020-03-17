<?php
    namespace App\Controllers;
    use App\Models\User;
    use App\Auth;    
    use App\Flash;
    /**
     * Account controller
     * PHP version 7.4.2
     */
    class Account extends \Core\Controller{
        /**
         * Validate if email is avaible (AJAX)
         * @return void
         */
        public function validateEmailAction(){
            $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
            header('Content-Type: application/json');
            echo json_encode($is_valid);
        }

        /**
         * Delete user
         * @return void
         */
        public function deleteAccountAction(){
            $user = new User();
            if($user->delete()){
                Auth::logout();
                Flash::addMessage('Twoje konto zostało usunięte.');
            } else {
                Flash::addMessage('Coś poszło nie tak. Twoje konto nie zostało usunięte.');
            }
            $this->redirect('/');
        }
    }
?>