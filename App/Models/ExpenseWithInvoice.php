<?php

namespace App\Models;
use PDO;
use \Core\View;
use \App\Models\Expense;
use \App\Models\Invoice;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class ExpenseWithInvoice extends \Core\Model{
    private $user_id;
    public $expense;
    public $invoice;
    /**
     * Class constructor
     * @param array $data Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
        $this->user_id = $_SESSION['user_id'];
        $this->expense= new Expense([
            'money' => $_POST['money'], 
            'expenseDate' => $_POST['expenseDate'], 
            'expenseCategory' => $_POST['expenseCategory'],
            'paymentMethod' => $_POST['paymentMethod'], 
            'comment' => $_POST['comment']
        ]);
        $this->invoice= new Invoice([
            'invoiceNumber' => $_POST['invoiceNumber'],
            'invoicePayDate' => $_POST['invoicePayDate'],
            'contractor' => $_POST['contractor']
        ]);
    }
    /**
     * Save the invoice model and income model with the current property values
     * @return void
     */
    public function save(){
        $this->invoice->validate();
        $this->expense->validate();
        if(empty($this->invoice->errors) && empty($this->expense->errors)){
            //try inserting into db invoice, getting back id of inserted invoice and insert income with invoice's id
            try {
                $user_id = $_SESSION['user_id'];
                $db = static::getDB();
                $db->beginTransaction();

                $sql_add_invoice = "INSERT INTO expense_invoices VALUES ('', :user_id, :number, :payment_date, :contractor)";  
                $stmt = $db->prepare($sql_add_invoice);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':number', $this->invoice->invoiceNumber, PDO::PARAM_STR);
                $stmt->bindValue(':payment_date', $this->invoice->invoicePayDate, PDO::PARAM_STR);            
                $stmt->bindValue(':contractor', $this->invoice->contractor, PDO::PARAM_STR);
                $stmt->execute();

                $invoice_id = $db->lastInsertId();

                $sql_add_expense = "INSERT INTO expenses VALUES ('', :user_id, :money, :date, 
                    (SELECT id FROM payment_methods WHERE user_id=:user_id AND name=:payment_method), 
                    (SELECT id FROM expense_categories WHERE user_id=:user_id AND name=:category), :comment, :invoice_id)";  
                $stmt = $db->prepare($sql_add_expense);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':money', $this->expense->money);
                $stmt->bindValue(':date', $this->expense->expenseDate, PDO::PARAM_STR);   
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':payment_method', $this->expense->paymentMethod, PDO::PARAM_STR);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);         
                $stmt->bindValue(':category', $this->expense->expenseCategory, PDO::PARAM_STR);
                $stmt->bindValue(':comment', $this->expense->comment, PDO::PARAM_STR);
                $stmt->bindValue(':invoice_id', $invoice_id, PDO::PARAM_STR);
                $stmt->execute();
                $result = $db->commit();
                return $result;
            }
            //in case of fail cancel both inserts
            catch (PDOException $e) { 
                $db->rollback();
                return false;
            } 
        }
    }   
}