<?php

namespace App\Models;
use PDO;
use \Core\View;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Income extends \Core\Model{
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
    }
    /**
     * Save the income model with the current property values
     * @return void
     */
    public function save(){
        $this->validate();
        if(empty($this->errors)){
            $sql = "INSERT INTO incomes VALUES ('', :user_id, :money, :date,
                (SELECT id FROM income_categories WHERE name=:category AND user_id=:user_id), :comment, :invoice_id)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':money', $this->money);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);            
            $stmt->bindValue(':category', $this->category);
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

    public static function getIncomesFromDB(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT i.date, i.money, ic.name, i.comment 
            FROM incomes AS i INNER JOIN income_categories AS ic WHERE i.user_id=:user_id 
            AND i.user_id = ic.user_id AND i.category_id=ic.id AND date BETWEEN :startingDate 
            AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public static function getIncomesSumFromDB(){
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

    public static function getSumsByCategory($period){
        $user_id = $_SESSION['user_id'];
        $sql ='SELECT ic.name, ROUND(SUM(i.money),2) FROM incomes 
            AS i INNER JOIN income_categories AS ic WHERE 
            i.category_id=ic.id AND ic.user_id=i.user_id 
            AND ic.user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate GROUP BY ic.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
}