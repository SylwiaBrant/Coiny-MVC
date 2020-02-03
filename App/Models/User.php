<?php

namespace App\Models;

use PDO;

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
        }
    }
    /**
     * Validate current property values, adding validation error messages to the errors array property
     */
    public function validate(){
        //checking email
        if($this->email == ''){
            $this->errors[] = 'Należy podać email.';
            $_SESSION['e_email'] = "To pole musi być wypełnione.";
        }
        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false){
            $this->errors[] = 'Niepoprawny adres email.';
            $_SESSION['e_email'] = 'Niepoprawny adres email.';
        }
        if(static::emailExists($this->email)){
            $this->errors[] = 'Podany email jest już zarejestrowany w serwisie.';
            $_SESSION['e_email'] = "Niepoprawny adres email.";
        }
        //checking password
        if(strlen($this->password)<6){
            $this->errors[] = 'Hasło musi mieć przynajmniej 6 znaków.';
            $_SESSION['e_password'] = "To pole musi być wypełnione.";
        }
        if(preg_match('/.*[a-z]+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło musi zawierać przynajmniej 1 literę.';
            $_SESSION['e_password'] = 'Hasło musi zawierać przynajmniej 1 literę.';
        }
        if(preg_match('/.*\d+.*/i', $this->password) == 0){
            $this->errors[] = 'Hasło musi zawierać przynajmniej 1 cyfrę.';
            $_SESSION['e_password'] = 'Hasło musi zawierać przynajmniej 1 cyfrę.';
        }
    }
    /**
     * Save the user model with the current propery values
     *
     * @return void
     */
    public function save()
    {
        $this->validate();
        if(empty($this->errors)){
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            $sql_add_user = 'INSERT INTO users (email, password) 
            VALUES (:email, :password_hash)';
    
            $db = static::getDB();
            $stmt = $db->prepare($sql_add_user);
    
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR); 
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);  

            return $stmt->execute();
        }
        return false;
    }
    /**
     * See if a user record already exists with the specified email
     * @param string $email email address to search for
     * @return boolean True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email){
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email){
        $sql_check_email_existence = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt= $db->prepare($sql_check_email_existence);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
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
        if ($user) {
            if (password_verify($password, $user->password)){
                return $user;
            }
        }
        return false;
    }
}
