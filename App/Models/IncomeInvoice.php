<?php

namespace App\Models;
use PDO;
use \Core\View;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Invoice extends \Core\Model{
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
            $user_id= $_SESSION['user_id'];
        };
    }

    /**
     * Save the invoice model with the current property values
     * @return void
     */
    public function save(){
        if(empty($this->errors)){
            $sql = "INSERT INTO income_invoices VALUES ('', :user_id, :number, :payment_date, :contractor)";  
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':number', $this->invoiceNumber, PDO::PARAM_STR);
            $stmt->bindValue(':payment_date', $this->invoicePayDate, PDO::PARAM_STR);            
            $stmt->bindValue(':contractor', $this->contractor, PDO::PARAM_STR);
            $stmt->execute();
            return $db->lastInsertId();
        }
        return false;
    }

    public function validate(){
        if($this->invoiceNumber == ''){
            $this->errors[] = 'Należy podać numer faktury.';
        }
        if(strlen($this->invoiceNumber) > 16){
            $this->errors[] = 'Ilość znaków nie może przekraczać 16.';
        }
        if(strpos($this->invoiceNumber, '/') === true){
            $this->$number = preg_filter('/\//', '/\\//', $number);
        } 
        if($this->invoicePayDate == ''){
            $this->errors[] = 'Należy podać datę uzyskania przychodu.';
        }
        if($this->contractor == ''){
            $this->errors[] = "Należy podać nazwę kontrahenta.";
        }
        if(strlen($this->contractor) > 256){
            $this->errors[] = "Nazwa kontrahenta musi się zawierać w 256 znakach.";
        }
        var_dump($this->errors);
    }

    public static function getInvoicesFromDB($period){
        $sql ='SELECT iv.number, ic.money, tc.date, iv.payment_date, iv.contractor, ic.comment 
        FROM income_invoices AS iv INNER JOIN incomes AS ic 
        ON iv.id = tc.invoice_id 
        WHERE iv.user_id=:user_id AND date BETWEEN';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    
    public static function getThisWeek($period){
    }
}

//INSERT INTO incomes VALUES ('', 1, 430.30, '07-01-2020', (SELECT id FROM income_categories WHERE category_name='Wynagrodzenie' AND user_id=1), '', 1) 