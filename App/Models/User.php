<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{
    /**
     * Error messages
     * @var array
     */
    public $errors = [];
     
    /**
     * Class constructor
     * @param array $data Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return void
     */
    public function save()
    {
        $this->validate();
        if(empty($this->errors)){
            $user_id=0;
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();
            $db = static::getDB();
            //try saving a user and populating their categories with default values in a transaction
            try {
                $db->beginTransaction();
                $sql_add_user = $db->prepare(
                    'INSERT INTO users (email, password, activation_hash) 
                    VALUES (:email, :password_hash, :activation_hash)');
                $sql_add_user->bindValue(':email', $this->email, PDO::PARAM_STR); 
                $sql_add_user->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                $sql_add_user->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
                $sql_add_user ->execute();     
                $user_id = $db->lastInsertId(); 
                $sql_populate_income_categories = $db->prepare(
                    "INSERT INTO income_categories (user_id, name) 
                    SELECT $user_id, name FROM default_income_categories");
                $sql_populate_expense_categories= $db->prepare(
                    "INSERT INTO expense_categories (user_id, name) 
                    SELECT $user_id, name FROM default_expense_categories");
                $sql_populate_payment_methods = $db->prepare(
                    "INSERT INTO payment_methods (user_id, name) 
                    SELECT $user_id, name FROM default_payment_methods");
                $sql_populate_income_categories->execute();
                $sql_populate_expense_categories->execute();
                $sql_populate_payment_methods->execute();
                $db->commit();
                return true;
            }
            catch (PDOException $e) { 
                $db->rollback();
            }
        }
        return false;
    }

    /**
     * Validate current property values, adding validation error messages to the errors array property
     */
    public function validate(){
        //checking email
        if($this->email == ''){
            $this->errors[] = 'Należy podać email.';
        }
        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Niepoprawny adres email.';
        }
        if(static::emailExists($this->email, $this->user_id ?? null)){
            $this->errors[] = 'Podany email jest już zarejestrowany w serwisie.';
        }
        //checking password
        if(strlen($this->password)<6){
            $this->errors[] = 'Hasło musi mieć przynajmniej 6 znaków.';
        }
        if(preg_match('/.*[a-z]+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło musi zawierać przynajmniej 1 literę.';
        }
        if(preg_match('/.*\d+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło musi zawierać przynajmniej 1 cyfrę.';
        }
    }

    /**
     * See if a user record already exists with the specified email
     * @param string $email email address to search for
     * @return boolean True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignored_id = null){
        $user = static::findByEmail($email);
        if($user){
            if($user->user_id != $ignored_id){
                return true;
            }
        }
        return false;
    }

    /**
     * Find a user model by email address
     * @param string $email email address to search for
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email){
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
    /**
     * Authenticate a user by email and password.
     * @param string $email
     * @param string $password
     * @return mixed The user object or false if authentication fails
     */
    public static function authenticate($email, $password){
        $user = static::findByEmail($email);
        if ($user && $user->is_active) {
            if (password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;
    }
    public static function findByID($id){
        $sql = 'SELECT * FROM users WHERE user_id = :id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /** Remember the login by inserting a new unique token into the
     * remembered_logins table for this user record
     * @return boolean True if the login was remembered successfully,
     * false otherwise
     */
    public function rememberLogin(){
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();
        $this->expiry_timestamp = time()+60*60*24*30; // 30 days from now
        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) 
        VALUES (:token_hash, :user_id, :expires_at)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
        return $stmt->execute();
    }
    public static function sendPasswordReset($email){
        $user = static::findByEmail($email);
        if($user) {
            if($user->startPasswordReset()){
                $user->sendPasswordResetEmail();
            }
        }
    }
    protected function startPasswordReset(){
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 *2; //2 hours from now
        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                password_reset_expires_at = :expires_at
                WHERE user_id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    /**
     * Send password reset instructions in an email to the user
     * @return void
     */
    protected function sendPasswordResetEmail(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;
        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
        
        Mail::send($this->email, 'Resetowanie hasła', $text, $html);
    }

    /**
     * Find a user model by password reset token
     * @param string $token Password reset token
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    public static function findByPasswordReset($token){
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql='SELECT * FROM users
            WHERE password_reset_hash = :token_hash';

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();
            $user = $stmt->fetch();
            if($user){
                if(strtotime($user->password_reset_expires_at) > time()){
                    return $user;
                }            
            }
    }

    /**
     * Reset the password
     * @param string $password The new password
     * @return boolean True if the password was updated succesfully, false otherwise
     */
    public function resetPassword($password){
        $this->password = $password;
        $this->validate();
        if(empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql = 'UPDATE users
                SET password=:password_hash,
                    password_reset_hash = NULL,
                    password_reset_expires_at = NULL
                WHERE user_id=:id';
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':id', $this->user_id, PDO::PARAM_STR);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                return $stmt->execute();
        }
        return false;
    }

    /**
     * Send an email to the user containing the activation link
     * @return void
     */
    public function sendActivationEmail(){
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;
        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
        
        Mail::send($this->email, 'Aktywacja konta', $text, $html);
    }
    /**
     * Activate the user account
     * @param string $value Activation token from the URL
     * @return void
     */
    public static function activate($value){
        $token = new Token($value);
        $hashed_token = $token->getHash();
        $sql = 'UPDATE users
                SET is_active = 1,
                activation_hash = NULL
                WHERE activation_hash = :hashed_token';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);   
        $stmt->execute();
    } 
}
