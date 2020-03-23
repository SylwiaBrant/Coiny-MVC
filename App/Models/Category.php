<?php 
namespace App\Models;

use PDO;
//use \App\Models\User;
/** 
 * Category model
 * PHP version 7.4.2
 */
class Category extends \Core\Model{
    /**
     * Class constructor
     * @param array $data Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
        $this->user_id = $_SESSION['user_id'];
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
    }
    /**
     * Get income categories associated with user from DB
     * @return mixed Array of income categories if found, false otherwise
     */
    public static function getIncomeCategories(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT id, name, blocked_funds FROM income_categories WHERE user_id = :user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Get expense categories associated with user from DB
     * @return mixed Array of expense categories if found, false otherwise
     */
    public static function getExpenseCategories(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT id, name, blocked_funds FROM expense_categories WHERE user_id = :user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Get payment methods associated with user from DB
     * @return mixed Array of expense methods if found, false otherwise
     */
    public static function getPaymentMethods(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT id, name, blocked_funds FROM payment_methods WHERE user_id = :user_id';
        $db = static::getDB();
        $stmt= $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/** Functions to delete user's single category in database
 * @return int - number of affected rows 
 */
    public function removeIncomeCategory(){
        return $this->deleteCategoryFromDB('income_categories');
    }

    public function removeExpenseCategory(){
        return $this->deleteCategoryFromDB('expense_categories');
    }

    public function removePaymentCategory(){
        return $this->deleteCategoryFromDB('payment_methods');
    }

    public function deleteCategoryFromDB($table){
        $sql = 'DELETE FROM '.$table.' WHERE id = :id AND user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

/** Functions to edit user's single category in database
 * @return int - number of affected rows 
 */

    public function editExpenseCategory(){
        return $this->updateCategoryInDB('expense_categories');
    }

    public function editPaymentCategory(){
        return $this->updateCategoryInDB('payment_methods');
    }

    public function updateCategoryInDB($table){
        if(!isset($this->blockedFunds)){
            $this->blockedFunds = NULL;
        }
        $sql = 'UPDATE '.$table.' SET name = :name, blocked_funds = :blocked_funds 
                WHERE id = :id AND user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':blocked_funds', $this->blockedFunds);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->rowCount();
    }

/** Functions to add a single new category in database
 * addIncomeCategory separate - lack of blocked funds option
 * @return int - last inserted id. 
 */
    public function addIncomeCategory(){
        $sql = 'INSERT INTO income_categories (user_id, name) 
                VALUES (:user_id, :name)'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        
        $stmt->execute();
        $result = intval($db->lastInsertId());
        return $result;
    }

    public function addExpenseCategory(){
        return $this->insertCategoryIntoDB('expense_categories');
    }

    public function addPaymentCategory(){
        return $this->insertCategoryIntoDB('payment_methods');
    }

    public function insertCategoryIntoDB($table){
        if(empty($_POST['blockedFunds'])){
            $this->blockedFunds= NULL;
        }else{
            $this->blockedFunds = $_POST['blockedFunds'];
        }
        $sql = 'INSERT INTO '.$table.' (user_id, name, blocked_funds) 
                VALUES (:user_id, :name, :blocked_funds)'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':blocked_funds', $this->blockedFunds);
        
        $stmt->execute();
        $result = intval($db->lastInsertId());
        return $result;
    }
}

?>
