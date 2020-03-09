<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\Invoice;
use \App\Models\Date;
/**
 * Invoices controller
 */
class Invoices extends Authenticated{
  /**
   * Show income invoices
   * @return void
   */
  public function indexAction($invoices){
    $period = Date::getThisMonth();
    View::renderTemplate('Invoices/index.html', [
      'invoices' => $invoices]); 
  }

    /**
     * Show income invoices
     * @return void
     */
    public function showThisWeekIncomeInvoicesAction(){
      $period = Date::getThisWeek();
      $invoices = new Invoice();
      $invoices->getIncomeInvoicesFromDB($period);
      $this->indexAction($invoices);
    }

    public function showThisMonthIncomeInvoicesAction(){
      $period = Date::getThisMonth();
      $invoices = new Invoice();
      $invoices->getIncomeInvoicesFromDB($period);
      $this->indexAction($invoices);
    }

    public function showLastMonthIncomeInvoicesAction(){
      $period = Date::getLastMonth();
      $this->invoices->getIncomeInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }
      
    public function showChosenPeriodIncomeInvoicesAction(){
      $period = Date::getLastMonth();
      $this->invoices->getIncomeInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }

    /**
     * Show expense invoices
     * @return void
     */
    public function showThisWeekExpenseInvoicesAjax(){
      $period = Date::getThisWeek();
      $invoices = new Invoice();
      $invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction($invoices);
    }

    public function showThisMonthExpenseInvoicesAction(){
      $period = Date::getThisMonth();
      $invoices = new Invoice();
      $invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction($invoices);
    }

    public function showLastMonthExpenseInvoicesAjax(){
      $period = Date::getLastMonth();
      $this->invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }

    public function showChosenPeriodExpenseInvoicesAction(){
      $period = Date::getLastMonth();
      $this->invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }
}

?>