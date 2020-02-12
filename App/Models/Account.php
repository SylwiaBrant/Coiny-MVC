<?php 
namespace App\Models;

use PDO;
//use \App\Models\User;
/** 
 * Account model
 * PHP version 7.4.2
 */
class Account extends \Core\Model{
    /**
     * Get income categories associated with user from DB
     * @return mixed Array of income categories if found, false otherwise
     */
    public static function getIncomeCategories(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT category_name FROM income_categories WHERE user_id = user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Get expense categories associated with user from DB
     * @return mixed Array of expense categories if found, false otherwise
     */
    public static function getExpenseCategories(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT category_name FROM expense_categories WHERE user_id = user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Get payment methods associated with user from DB
     * @return mixed Array of expense methods if found, false otherwise
     */
    public static function getPaymentMethods(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT method_name FROM payment_methods WHERE user_id = user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Get the user model associated with this remembered login
     * @return User the user model 
     */
    public function getUser(){
        return User::findByID($this->user_id);
    }

    /**
     * See if the remember token has expired or not based on the current system time
     * @return boolean True if the token has expired, false otherwise
     */
    public function hasExpired(){
        return strtotime($this->expires_at) < time();
    }

    /**
     * Delete this model
     * @return void
     */
    public function delete(){
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);
        $stmt->execute();
        
    }
}

?>
