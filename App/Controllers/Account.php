<?php
    namespace App\Controllers;
    use \App\Models\User;
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
            $is_valid = ! User::emailExists($GET['email']);
            header('COntent-Type: application/json');
        }
    }
?>