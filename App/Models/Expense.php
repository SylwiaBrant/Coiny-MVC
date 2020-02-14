<?php

namespace App\Models;
use PDO;
use \Core\View;

/**
 * Signup controller
 *
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
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
        $user_id= $_SESSION['user_id'];
    }
    /**
     * Save the income model with the current property values
     * @return void
     */
    public function save(){
        $this->validate();
        if(empty($this->errors)){
            $user_id= $_SESSION['user_id'];
            $sql = "INSERT INTO expenses VALUES ('', :user_id, :money, :date, 
            (SELECT id FROM payment_methods WHERE name=:payment_method AND user_id=:user_id), 
            (SELECT id FROM expense_categories WHERE name=:category AND user_id=:user_id), :comment, :invoice_id)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':money', $this->money);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR); 
            $stmt->bindValue(':payment_method', $this->payment_method, PDO::PARAM_STR);  
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);          
            $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
        if($this->date == ''){
            $this->errors[] = 'Należy podać datę uzyskania przychodu.';
        }
        if($this->category == ''){
            $this->errors[] = 'Należy podać kategorię przychodu.';
        }
        if($this->payment_method == ''){
            $this->errors[] = 'Należy podać metodę płatności.';
        }
        if(strlen($this->comment) > 400){
            $this->errors[] = "Pole może zawierać maksymalnie 400 znaków.";
        }
    }

    public static function getExpensesFromDB($period){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT e.date, e.money, ep.name AS method , ec.name AS category, e.comment 
        FROM expenses AS e INNER JOIN expense_categories AS ec ON e.user_id = ec.user_id 
        AND e.category_id=ec.id INNER JOIN payment_methods AS ep ON e.user_id = ep.user_id 
        AND e.payment_method_id = ep.id WHERE e.user_id=:user_id AND date BETWEEN
            :startingDate AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public static function getExpensesSumFromDB($period){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT ROUND(SUM(money),2) as totalAmount FROM expenses 
            WHERE user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['totalAmount'];
    }   

    public static function getSumsByCategory($period){
        $user_id = $_SESSION['user_id'];
        $sql ='SELECT ec.name, ROUND(SUM(e.money),2) FROM expenses 
            AS e INNER JOIN expense_categories AS ec WHERE 
            e.category_id=ec.id AND ec.user_id=e.user_id 
            AND ec.user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate GROUP BY ec.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
}