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
  public function incomeInvoicesAction(){
    $period = Date::getThisMonth();
    $invoices = new Invoice($period);
    $invoices->getIncomeInvoicesFromDB();
    View::renderTemplate('Invoices/incomeInvoices.html', [
      'invoices' => $invoices]); 
  }
    /**
     * Show expense invoices
     * @return void
     */
  public function expenseInvoicesAction(){
    $period = Date::getThisMonth();
    $invoices = new Invoice($period);
    $invoices->getExpenseInvoicesFromDB();
    View::renderTemplate('Invoices/expenseInvoices.html', [
      'invoices' => $invoices]); 
  }

    public function showThisWeekIncomeInvoicesAjax(){
      $period = Date::getThisWeek();
      $invoices = new Invoice($period);
      $invoices->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showThisMonthIncomeInvoicesAjax(){
      $period = Date::getThisMonth();
      $invoices = new Invoice($period);
      $invoices->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showLastMonthIncomeInvoicesAjax(){
      $period = Date::getLastMonth();
      $invoices = new Invoice($period);
      $invoices->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }
      
    public function showChosenPeriodIncomeInvoicesAjax(){
      $period = Date::getChosenPeriod($_POST);
      $invoices = new Invoice($period);
      $invoices->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    /**
     * Show expense invoices
     * @return void
     */
    public function showThisWeekExpenseInvoicesAjax(){
      $period = Date::getThisWeek();
      $invoices = new Invoice($period);
      $invoices->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showThisMonthExpenseInvoicesAjax(){
      $period = Date::getThisMonth();
      $invoices = new Invoice($period);
      $invoices->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showLastMonthExpenseInvoicesAjax(){
      $period = Date::getLastMonth($period);
      $invoices = new Invoice($period);
      $invoices->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showChosenPeriodExpenseInvoicesAjax(){
      $period = Date::getChosenPeriod($_POST);
      $invoices = new Invoice($period);
      $invoices->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }
}

?>