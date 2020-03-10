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
            $sql = "INSERT INTO incomes VALUES ('', :user_id, :money, :date,
                (SELECT id FROM income_categories WHERE name=:category AND user_id=:user_id), :comment, :invoice_id)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':money', $this->money);
            $stmt->bindValue(':date', $this->incomeDate, PDO::PARAM_STR);            
            $stmt->bindValue(':category', $this->incomeCategory, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
            $stmt->bindValue(':invoice_id', NULL, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    public function validate(){
        if($this->money == ''){
            $this->errors[] = 'Należy podać wysokość przychodu.';
        }
        if(preg_match('/.*\d+.*/i', $this->money) == 0) {
            $this->errors[] = 'W tym polu można wpisać jedynie wartości numeryczne, np. 100, 100.50.';
        }
        if($this->incomeDate == ''){
            $this->errors[] = 'Należy podać datę uzyskania przychodu.';
        }
        if($this->incomeCategory == ''){
            $this->errors[] = 'Należy podać kategorię przychodu.';
        }
        if($this->comment != NULL){
            if(strlen($this->comment) > 400){
                $this->errors[] = "Pole może zawierać maksymalnie 400 znaków.";
            }
        }
    }

    public function getIncomesFromDB(){
        $sql = 'SELECT i.date, i.money, ic.name, i.comment 
            FROM incomes AS i INNER JOIN income_categories AS ic WHERE i.user_id=:user_id 
            AND i.user_id = ic.user_id AND i.category_id=ic.id AND date BETWEEN :startingDate 
            AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function getIncomesSumFromDB($period){
        $sql = 'SELECT ROUND(SUM(money),2) as totalAmount FROM incomes 
            WHERE user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['totalAmount'];
    }

    public function getSumsByCategory($period){
        $sql ='SELECT ic.name, ROUND(SUM(i.money),2) AS money FROM incomes 
            AS i INNER JOIN income_categories AS ic WHERE 
            i.category_id=ic.id AND ic.user_id=i.user_id 
            AND ic.user_id=:user_id AND date BETWEEN :startingDate 
            AND :endingDate GROUP BY ic.name DESC'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_STR);
        $stmt->bindValue(':startingDate', $period['startingDate'], PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $period['endingDate'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function getLastTransactionPerCategory(){
        $user_id = $_SESSION['user_id'];
        $sql ='SELECT ic.name,i.money, MAX(i.date) AS date, i.comment FROM incomes AS i 
            INNER JOIN income_categories AS ic WHERE i.category_id=ic.id 
            AND ic.user_id=i.user_id AND ic.user_id=:user_id GROUP BY ic.name'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }

    public function getIncomesByCategory(){
        $user_id = $_SESSION['user_id'];
        $sql ='SELECT i.id AS transactionId, ic.id AS categoryId, ic.name, i.date, i.money, i.comment FROM incomes AS i 
                INNER JOIN income_categories AS ic WHERE ic.id=:categoryId AND i.category_id=ic.id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $this->categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    public function editIncomeEntry(){
        $sql ='UPDATE income_categories SET money=:money, date=:date,
            category_id = (SELECT id FROM income_categories WHERE name=:category AND user_id=:user_id), 
            comment=:comment WHERE id=:income_id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':income_id', $this->incomeId, PDO::PARAM_INT);
        $stmt->bindValue(':money', $this->money);
        $stmt->bindValue(':date', $this->incomeDate, PDO::PARAM_STR);            
        $stmt->bindValue(':category', $this->incomeCategory, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteEntry(){
        $sql ='DELETE FROM incomes WHERE id=:id AND user_id=:user_id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}