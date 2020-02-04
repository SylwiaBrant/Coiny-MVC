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
     * Remember the originally-requested page in the session
     * @return void
     */
    public static function rememberRequestedPage(){
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally requested page to return to after requiring login or default to the homepage
     *@return void;
     */
    public static function getReturnToPage(){
        return $_SESSION['return_to'] ?? '/';
    }
    
    /**
     * Get the current logged in user, from the session or 
     * the remember me cookie
     * @return mixed
     */
    public static function getUser(){
        if(isset($_SESSION['user_id'])){
            return User::findByID($_SESSION['user_id']);
        }
    }
}
?>