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
  public function indexAction(){
    $period = Date::getThisMonth();
    $invoices = new Invoice();
    View::renderTemplate('Invoices/index.html', [
      'invoices' => $invoices->getIncomeInvoicesFromDB($period)]); 
  }

    /**
     * Show income invoices
     * @return void
     */
    public function showThisWeekIncomeInvoicesAction(){
      $period = Date::getThisWeek();
      $this->invoices->getIncomeInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }

    public function showThisMonthIncomeInvoicesAction(){
      $period = Date::getThisMonth();
      return Invoice::getIncomeInvoicesFromDB($period);
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
    public function showThisWeekExpenseInvoicesAction(){
      $period = Date::getThisWeek();
      $this->invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }

    public function showThisMonthExpenseInvoicesAction(){
      $period = Date::getThisMonth();
      $this->invoices->getExpenseInvoicesFromDB($period);
      $this->indexAction(['invoices' => $this->invoices]);
    }

    public function showLastMonthExpenseInvoicesAction(){
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