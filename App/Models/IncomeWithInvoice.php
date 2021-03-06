<?php
namespace App\Models;
use PDO;
use \Core\View;
use \App\Models\Income;
use \App\Models\Invoice;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class IncomeWithInvoice extends \Core\Model{
    private $user_id;
    public $income;
    public $invoice;
    /**
     * Class constructor
     * @param array $data Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
        $this->user_id = $_SESSION['user_id'];
        $this->income = new Income([
            'money' => $_POST['money'], 
            'incomeDate' => $_POST['incomeDate'], 
            'incomeCategory' => $_POST['incomeCategory'], 
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
        $this->income->validate();
        if(empty($this->invoice->errors) && empty($this->income->errors)){
            //try inserting into db invoice, getting back id of inserted invoice and insert income with invoice's id
            try {
                $user_id = $_SESSION['user_id'];
                $db = static::getDB();
                $db->beginTransaction();

                $sql_add_invoice = "INSERT INTO income_invoices VALUES (:i_id, :user_id, :number, :payment_date, :contractor)";  
                $stmt = $db->prepare($sql_add_invoice);
                $stmt->bindValue(':i_id', NULL, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':number', $this->invoice->invoiceNumber, PDO::PARAM_STR);
                $stmt->bindValue(':payment_date', $this->invoice->invoicePayDate, PDO::PARAM_STR);            
                $stmt->bindValue(':contractor', $this->invoice->contractor, PDO::PARAM_STR);
                $stmt->execute();

                $invoice_id = $db->lastInsertId();

                $sql_add_income = "INSERT INTO incomes VALUES (:id, :user_id, :money, :date,
                    (SELECT id FROM income_categories WHERE name=:category AND user_id=:user_id), :comment, :invoice_id)";  
                $stmt = $db->prepare($sql_add_income);
                $stmt->bindValue(':id', NULL, PDO::PARAM_INT);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':money', $this->income->money);
                $stmt->bindValue(':date', $this->income->incomeDate, PDO::PARAM_STR);            
                $stmt->bindValue(':category', $this->income->incomeCategory);
                $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
                $stmt->bindValue(':comment', $this->income->comment, PDO::PARAM_STR);
                $stmt->bindValue(':invoice_id', $invoice_id, PDO::PARAM_STR);
                $stmt->execute();
                if($db->commit())
                    return true;
            }
            //in case of fail cancel both inserts
            catch (PDOException $e) { 
                $db->rollback();
            }
            return false;
        }
    }   
}