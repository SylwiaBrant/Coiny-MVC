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
        $this->user_id = $_SESSION['user_id'];
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
    }

    public function validate(){
        if($this->invoiceNumber == ''){
            $this->errors[] = 'Należy podać numer faktury.';
        }
        if(strlen($this->invoiceNumber) > 16){
            $this->errors[] = 'Ilość znaków nie może przekraczać 16.';
        }
        if(strpos($this->invoiceNumber, '/') === true){
            $this->$invoiceNumber = preg_filter('/\//', '/\\//', $invoiceNumber);
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
    }

    public function getIncomeInvoicesFromDB(){
        $sql ='SELECT iv.id, iv.number, ic.money, ic.date, iv.payment_date, iv.contractor, ic.comment 
            FROM income_invoices AS iv INNER JOIN incomes AS ic ON iv.id = ic.invoice_id 
            WHERE iv.user_id=:user_id AND ic.date BETWEEN :startingDate AND :endingDate';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   
    
    public function getExpenseInvoicesFromDB(){
        $sql ='SELECT ev.id, ev.number, ec.money, ec.date, ev.payment_date, ev.contractor, ec.comment 
            FROM expense_invoices AS ev INNER JOIN expenses AS ec ON ev.id = ec.invoice_id 
            WHERE ev.user_id=:user_id AND ec.date BETWEEN :startingDate AND :endingDate';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':startingDate', $this->startingDate, PDO::PARAM_STR);
        $stmt->bindValue(':endingDate', $this->endingDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
         
    public function getDueInvoices(){
        $sql ='SELECT ev.number, ec.money, ev.contractor, ev.payment_date FROM expense_invoices AS ev 
        INNER JOIN expenses AS ec ON ev.id = ec.invoice_id WHERE ev.user_id=:user_id
        AND ev.payment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**
 * Functions to edit single invoice record from DB 
 * @return int - number of affected rows
 */
    public function editIncomeInvoice(){
        return $this->editInvoiceInDB('income_invoices');
    }

    public function editExpenseInvoice(){
        return $this->editInvoiceInDB('expense_invoices');
    }

    private function editInvoiceInDB($table){
        $sql ='UPDATE '.$table.' SET number = :invoiceNumber, payment_date = :invoicePayDate,
        contractor = :contractor WHERE id = :invoiceId'; 

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':invoiceId', $this->invoiceId, PDO::PARAM_INT);
        $stmt->bindValue(':invoiceNumber', $this->invoiceNumber, PDO::PARAM_STR);
        $stmt->bindValue(':invoicePayDate', $this->invoicePayDate, PDO::PARAM_STR);
        $stmt->bindValue(':contractor', $this->contractor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }
/**
 * Functions to delete single invoice record from DB 
 * @return int - number of affected rows
 */
    public function deleteIncomeInvoice(){
        return $this->deleteInvoiceInDB('income_invoices');
    }

    public function deleteExpenseInvoice(){
        return $this->deleteInvoiceInDB('expense_invoices');
    }

    public function deleteInvoiceInDB($table){
        $sql ='DELETE FROM '.$table.' WHERE id=:id AND user_id=:user_id'; 
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->invoiceId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}