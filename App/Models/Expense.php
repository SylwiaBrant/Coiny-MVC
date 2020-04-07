<?php
namespace App\Models;
use PDO;
use \Core\View;

/**
 * Expense controller
 * PHP version 7.0
 */
class Expense extends \Core\Model{
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
        $this->user_id = $_SESSION['user_id'];
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
        if(empty($_POST['comment'])){
            $this->comment = NULL;
        }else{
            $this->comment = $_POST['comment'];
        }
    }

    /**
     * Save the income model with the current property values
     * @return void
     */
    public function save(){
        $this->validate();
        if(empty($this->errors)){
            $sql = "INSERT INTO expenses VALUES (:id, :user_id, :money, :date, 
            (SELECT id FROM payment_methods WHERE name=:payment_method AND user_id=:user_id), 
            (SELECT id FROM expense_categories WHERE name=:category AND user_id=:user_id), :comment, :invoice_id)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', NULL, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':money', $this->money);
            $stmt->bindValue(':date', $this->expenseDate, PDO::PARAM_STR); 
            $stmt->bindValue(':payment_method', $this->paymentMethod, PDO::PARAM_STR);  
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);          
            $stmt->bindValue(':category', $this->expenseCategory, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
            $stmt->bindValue(':invoice_id', NULL, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public function validate(){
        if($this->money == ''){
            $this->errors[] = 'Należy podać wysokość przychodu.';
        }
        if(preg_match('/.*\d+.*/i', $this->money) == 0) {
            $this->errors[] = 'W tym polu można wpisać jedynie wartości numeryczne, np. 100, 100.50.';
        }
        if($this->expenseDate == ''){
            $this->errors[] = 'Należy podać datę uzyskania przychodu.';
        }
        if($this->expenseCategory == ''){
            $this->errors[] = 'Należy podać kategorię przychodu.';
        }
        if($this->paymentMethod == ''){
            $this->errors[] = 'Należy podać metodę płatności.';
        }
        if($this->comment != NULL){
            if(strlen($this->comment) > 400){
                $this->errors[] = "Pole może zawierać maksymalnie 400 znaków.";
            }
        }
    }

    public function editExpense(){
        $sql ='UPDATE expenses SET money = :money, date = :date, 
        category_id = (SELECT id FROM expense_categories WHERE name=:category AND user_id=:user_id),
        payment_method_id = (SELECT id FROM payment_methods WHERE name=:payment AND user_id=:user_id),
        comment = :comment WHERE id=:transactionId'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT); 
        $stmt->bindValue(':money', $this->money);
        $stmt->bindValue(':date', $this->expenseDate, PDO::PARAM_STR);
        $stmt->bindValue(':category', $this->expenseCategory, PDO::PARAM_STR);
        $stmt->bindValue(':payment', $this->paymentMethod, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);           
        $stmt->bindValue(':transactionId', $this->id, PDO::PARAM_INT); 
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteExpense(){
        $sql ='DELETE FROM expenses WHERE id=:id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getExpensesFromDB(){
        $sql = 'SELECT e.id, e.date, e.money, ep.name AS method , ec.name AS category, e.comment 
        FROM expenses AS e INNER JOIN expense_categories AS ec ON e.user_id = ec.user_id 
        AND e.category_id=ec.id INNER JOIN payment_methods AS ep ON e.user_id = ep.user_id 
        AND e.payment_method_id = ep.id WHERE e.user_id=:user_id AND date BETWEEN
            :startingDate AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function getExpensesSumFromDB(){
        $sql = 'SELECT ROUND(SUM(money),2) AS totalAmount FROM expenses 
            WHERE user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['totalAmount'];
    }   

    public function getSumsByCategory(){
        $sql ='SELECT ec.name, ROUND(SUM(e.money),2) AS money FROM expenses 
            AS e INNER JOIN expense_categories AS ec WHERE 
            e.category_id=ec.id AND ec.user_id=e.user_id 
            AND ec.user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate GROUP BY ec.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getLimitedExpenseCategories(){
        $sql = 'SELECT ec.name, ec.blocked_funds, ROUND(SUM(e.money),2) AS money, 
        ROUND(money*100/ec.blocked_funds, 0) AS percentage FROM expenses AS e 
        INNER JOIN expense_categories AS ec WHERE ec.blocked_funds is not NULL 
        AND e.category_id=ec.id AND ec.user_id=e.user_id AND ec.user_id=:user_id 
        AND date BETWEEN :startingDate AND :endingDate GROUP BY ec.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLimitedPaymentMethods(){
        $sql = 'SELECT pm.name, pm.blocked_funds, ROUND(SUM(e.money),2) AS money,
        ROUND(money*100/pm.blocked_funds, 0) AS percentage FROM expenses AS e 
        INNER JOIN payment_methods AS pm WHERE pm.blocked_funds is not NULL 
        AND e.category_id=pm.id AND pm.user_id=e.user_id AND pm.user_id=:user_id 
        AND date BETWEEN :startingDate AND :endingDate GROUP BY pm.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastTransactionPerCategory(){
        $sql ='SELECT ec.name, e.money, MAX(e.date) AS date, e.comment FROM expenses AS e 
            INNER JOIN expense_categories AS ec WHERE e.category_id=ec.id AND 
            ec.user_id=e.user_id AND ec.user_id=:user_id GROUP BY ec.name'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }

    public function getExpensesByCategory(){
        $sql ='SELECT e.id AS transactionId, ec.id AS categoryId, e.date, e.money, ep.name AS method, 
        ec.name AS category, e.comment FROM expenses AS e INNER JOIN expense_categories AS ec 
        ON e.user_id = ec.user_id AND e.category_id=ec.id INNER JOIN payment_methods AS ep 
        ON e.user_id = ep.user_id AND e.payment_method_id = ep.id WHERE ec.id=:categoryId AND e.category_id=ec.id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
    
    public function getExpensesByPayments(){
        $sql ='SELECT e.id AS transactionId, ep.id AS categoryId, e.date, e.money, ep.name AS method, 
        ec.name AS category, e.comment FROM expenses AS e INNER JOIN expense_categories AS ec 
        ON e.user_id = ec.user_id AND e.category_id=ec.id INNER JOIN payment_methods AS ep 
        ON e.user_id = ep.user_id AND e.payment_method_id = ep.id WHERE ep.id=:categoryId 
        AND e.payment_method_id=ep.id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function editExpenseCategory(){
        $sql ='UPDATE expenses SET category_id = (SELECT id FROM expense_categories 
        WHERE name=:category AND user_id=:user_id) WHERE id=:transactionId'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT); 
        $stmt->bindValue(':transactionId', $this->id, PDO::PARAM_INT);           
        $stmt->bindValue(':category', $this->name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function editExpensePayment(){
        $sql ='UPDATE expenses SET payment_method_id = (SELECT id FROM payment_methods 
        WHERE name=:category AND user_id=:user_id) WHERE id=:transactionId'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT); 
        $stmt->bindValue(':transactionId', $this->id, PDO::PARAM_INT);           
        $stmt->bindValue(':category', $this->name, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    public function getThisMonthExpenseSum(){
        $sql ='SELECT ROUND(SUM(e.money),2) AS money FROM expenses 
            AS e INNER JOIN expense_categories AS ec WHERE e.user_id=:user_id AND ec.name=:category
            AND e.category_id=ec.id AND date BETWEEN DATE_SUB(CURDATE(), 
            INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW()) GROUP BY ec.name'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);      
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['money'];
    }

    public function getThisMonthPaymentSum(){
        $sql ='SELECT ROUND(SUM(e.money),2) AS money FROM expenses 
            AS e INNER JOIN payment_methods AS pm WHERE e.user_id=:user_id AND pm.name=:category
            AND e.payment_method_id=pm.id AND date BETWEEN DATE_SUB(CURDATE(), 
            INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW()) GROUP BY pm.name'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);      
        $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['money'];
    }
}