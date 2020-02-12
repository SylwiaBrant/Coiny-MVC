<?php
namespace App\Controllers;
use \Core\View;
use \App\Models\Income;
use \App\Models\Invoice;
use \App\Models\IncomeWithInvoice;
use \App\Models\Account;
/**
 * Invoices controller
 */
class Invoices extends Authenticated{
    
    public $invoices = [];

    public function incomeIndexAction(){
      View::renderTemplate('Invoices/income.html', ['invoices' => $this->invoices]); 
    }
    /**
     * Show income invoices
     * @return void
     */
    public function showThisWeekAction(){
      $this->invoices = Invoice::getInvoicesFromDB();
      $this->indexAction();
    //  var_dump($this->invoices);
    }

    public function showThisMonthAction(){
      $this->invoices = Invoice::getThisMonthInvoicesFromDB();
      View::renderTemplate('Invoices/income.html', ['invoices' => $this->invoices]);
    }


    /**
     * Show income invoices
     * @return void
     */
    public function showLastMonthAction(){
        $this->invoices = Invoice::getInvoicesFromDB('BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) 
        AND LAST_DAY(NOW())');
        View::renderTemplate('Invoices/income.html', ['invoices' => $this->invoices]);
      //  var_dump($this->invoices);
    }

    /**
     * Show expense invoices
     * @return void
     */
    public function expenseInvoicesAction(){
        $this->invoices = $this->getInvoicesFromDB();
        View::renderTemplate('Invoices/income.html', ['invoices' => $this->invoices]);
      //  var_dump($this->invoices);
    }
}

?>