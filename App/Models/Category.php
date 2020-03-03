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
        return $this->deleteCategoryFromDB($_POST['id'], 'income_categories');
    }

    public function removeExpenseCategory(){
        return $this->deleteCategoryFromDB($_POST['id'], 'expense_categories');
    }

    public function removePaymentMethod(){
        return $this->deleteCategoryFromDB($_POST['id'], 'payment_methods');
    }

    public function deleteCategoryFromDB($category_id, $table){
        $user_id = $_SESSION['user_id'];
        $sql = 'DELETE FROM '.$table.' WHERE id = :id AND user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

/** Functions to sedit user's single category in database
 * @return int - number of affected rows 
 */

    public function editExpenseCategory(){
        return $this->updateCategoryInDB($_POST, 'expense_categories');
    }

    public function editPaymentMethod(){
        return $this->updateCategoryInDB($_POST, 'payment_methods');
    }

    public function updateCategoryInDB($data, $table){
        $user_id = $_SESSION['user_id'];
        $category_id = $data['id'];
        $category_name = $data['name'];
        $blocked_funds = $data['blockedFunds'];

        $sql = 'UPDATE '.$table.' SET name = :name, blocked_funds = :blocked_funds 
                WHERE id = :id AND user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
        $stmt->bindValue(':blocked_funds', $blocked_funds);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $category_id, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->rowCount();
    }

/** Functions to add a single new category in database
 * addIncomeCategory separate - lack of blocked funds option
 * @return int - last inserted id. 
 */
    public function addIncomeCategory(){
        $user_id = $_SESSION['user_id'];
        $category_name = $data['name'];

        $sql = 'INSERT INTO '.$table.' (user_id, name) 
                VALUES (:user_id, :name)'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
        
        $stmt->execute();
        $result = intval($db->lastInsertId());
        return $result;
    }

    public function addExpenseCategory(){
        return $this->insertCategoryIntoDB($_POST, 'expense_categories');
    }

    public function addPaymentMethod(){
        return $this->insertCategoryIntoDB($_POST, 'payment_methods');
    }

    public function insertCategoryIntoDB($data, $table){
        $user_id = $_SESSION['user_id'];
        $category_name = $data['name'];
        $blocked_funds = $data['blockedFunds'];

        $sql = 'INSERT INTO '.$table.' (user_id, name, blocked_funds) 
                VALUES (:user_id, :name, :blocked_funds)'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $category_name, PDO::PARAM_STR);
        $stmt->bindValue(':blocked_funds', $blocked_funds);
        
        $stmt->execute();
        $result = intval($db->lastInsertId());
        return $result;
    }
}

?>
