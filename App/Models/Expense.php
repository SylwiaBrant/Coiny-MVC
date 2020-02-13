<?php

namespace App\Models;
use PDO;
use \Core\View;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Expense extends \Core\Models{
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
            $sql = "INSERT INTO incomes VALUES ('', :user_id, :money, :date,
            (SELECT id FROM income_categories WHERE category_name=:category AND user_id=:user_id), :comment, :invoice_id)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':money', $this->money);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);            
            $stmt->bindValue(':category', $this->category);
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
        if(strlen($this->comment) > 400){
            $this->errors[] = "Pole może zawierać maksymalnie 400 znaków.";
        }
    }

    public static function getExpensesFromDB($period){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT e.date, e.money, ep.name, ec.name, e.comment 
            FROM expenses AS e INNER JOIN expense_categories AS ec ON e.user_id = ec.user_id 
            AND e.expense_type_id=ec.id INNER JOIN payment_methods AS ep ON e.user_id = ep.user_id 
            AND e.pay_method_id = ep.id WHERE e.user_id=31 AND date BETWEEN
            :startingDate AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public static function getExpensesSumFromDB(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT ROUND(SUM(money),2) as totalAmount FROM incomes 
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
}

//INSERT INTO incomes VALUES ('', 1, 430.30, '07-01-2020', (SELECT id FROM income_categories WHERE category_name='Wynagrodzenie' AND user_id=1), '', 1) 