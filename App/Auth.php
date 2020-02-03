<?php
namespace App;
/**
 * Authentication 
 * PHP version 7.4.2
 */
class Auth{
    /**
     * Login the user
     * @param User $user The user model
     * @return void
     */
    public static function login($user){
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->user_id;
    }
    /**
     * Log out the user
     * @return void
     */
    public static function logout(){
        //Unset all of the session variables
        $_SESSION = [];
        //Delete the session cookie
        if (ini_get('session.use_cookies')){
            $params = session_get_cookie_params();  
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['httponly']
            );
        }
        //Destroy the session
        session_destroy();
        //Redirect to login page
    }
    /**
     * Return indicator of whether a user is logged in
     */
    public static function isLoggedIn(){
        return isset($_SESSION['user_id']);
    }
}
?>