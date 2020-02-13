<?php
namespace App\Controllers;
use \Core\View;
use \App\Models\Invoice;
/**
 * Invoices controller
 */
class Expenses extends Authenticated{
    
    public $invoices = [];

    public function indexAction(){
      View::renderTemplate('Invoices/income.html', ['invoices' => $this->invoices]); 
    }
    /**
     * Show income invoices
     * @return void
     */
    public function showThisWeekAction(){
      $this->invoices = ExpenseInvoice::getThisWeekInvoicesFromDB();
      $this->indexAction();
    }

    public function showThisMonthAction(){
      $this->invoices = ExpenseInvoice::getThisMonthInvoicesFromDB();
      $this->indexAction();
    }

    public function showLastMonthAction(){
        $this->invoices = ExpenseInvoice::getLastMonthInvoicesFromDB();
        $this->indexAction();
    }

    public function showChosenPeriodAction(){
      $this->invoices = ExpenseInvoice::getChosenPeriodInvoicesFromDB();
      $this->indexAction();
  }
}

?>