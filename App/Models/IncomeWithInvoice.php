<?php

namespace App\Models;
use PDO;
use \Core\View;
use \App\Models\Income;
use \App\Models\Invoice;
use \App\Flash;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class IncomeWithInvoice extends \Core\Model{
    /**
     * Error messages
     * @var array
     */
    public $errors = [];
    public $income;
    public $invoice;
    /**
     * Class constructor
     * @param array $data Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
        $this->income = new Income([
            'money' => $_POST['money'], 
            'date' => $_POST['date'], 
            'category' => $_POST['category'], 
            'comment' => $_POST['comment']
        ]);
        $this->invoice= new Invoice([
            'invoiceNumber' => $_POST['invoiceNumber'],
            'invoicePayDate' => $_POST['invoicePayDate'],
            'contractor' => $_POST['contractor']
        ]);
    }
    /**
     * Save the income model with the current property values
     * @return void
     */
    public function save(){
            $lel = $this->income->validate();
            var_dump($lel);
            $this->invoice->validate();
            if(empty($this->income->errors) && empty($this->invoice->errors)){
                $last_id = $this->invoice->save();
                if($last_id){
                    $user_id= $_SESSION['user_id'];
                    $sql = "INSERT INTO incomes VALUES ('', :user_id, :money, :date,
                    (SELECT id FROM income_categories WHERE category_name=:category AND user_id=:user_id), :comment, :last_id)";  
                    $db = static::getDB();
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindValue(':money', $this->income->money);
                    $stmt->bindValue(':date', $this->income->date, PDO::PARAM_STR);            
                    $stmt->bindValue(':category', $this->income->category, PDO::PARAM_STR);
                    $stmt->bindValue(':comment', $this->income->comment, PDO::PARAM_STR);
                    $stmt->bindValue(':last_id', $last_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                
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
        $sql = 'SELECT i.date, i.money, ic.category_name, i.comment 
        FROM incomes AS i INNER JOIN income_categories AS ic WHERE i.user_id=:user_id 
        AND i.user_id = ic.user_id AND i.category_id=ic.id AND date BETWEEN DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), 
        INTERVAL 1 DAY), INTERVAL - 2 MONTH) AND DATE_ADD(LAST_DAY(CURDATE()),INTERVAL - 1 MONTH)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
       // $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public static function getIncomesSumFromDB(){
        $user_id = $_SESSION['user_id'];
        $sql = 'SELECT ROUND(SUM(money),2) as totalAmount FROM incomes 
        WHERE user_id=:user_id AND date BETWEEN DATE_ADD(DATE_ADD(LAST_DAY(CURDATE()), 
        INTERVAL 1 DAY), INTERVAL - 2 MONTH) AND DATE_ADD(LAST_DAY(CURDATE()),INTERVAL - 1 MONTH)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['totalAmount'];
    }   
}

//INSERT INTO incomes VALUES ('', 1, 430.30, '07-01-2020', (SELECT id FROM income_categories WHERE category_name='Wynagrodzenie' AND user_id=1), '', 1) 