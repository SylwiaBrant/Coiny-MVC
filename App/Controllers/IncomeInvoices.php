<?php
namespace App\Controllers;
use \Core\View;
use \App\Models\Invoice;
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
      $this->invoices = IncomeInvoice::getThisWeekInvoicesFromDB();
      $this->indexAction();
    }

    public function showThisMonthAction(){
      $this->invoices = IncomeInvoice::getThisMonthInvoicesFromDB();
      $this->indexAction();
    }

    public function showLastMonthAction(){
        $this->invoices = IncomeInvoice::getLastMonthInvoicesFromDB();
        $this->indexAction();
    }

    public function showChosenPeriodAction(){
      $this->invoices = IncomeInvoice::getChosenPeriodInvoicesFromDB();
      $this->indexAction();
  }
}

?>