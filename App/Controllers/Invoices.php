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
    $invoice = new Invoice($period);
    $invoices = $invoice->getIncomeInvoicesFromDB();
    View::renderTemplate('Invoices/incomeInvoices.html', [
      'invoices' => $invoices]); 
  }
    /**
     * Show expense invoices
     * @return void
     */
  public function expenseInvoicesAction(){
    $period = Date::getThisMonth();
    $invoice = new Invoice($period);
    $invoices = $invoice->getExpenseInvoicesFromDB();
    View::renderTemplate('Invoices/expenseInvoices.html', [
      'invoices' => $invoices]); 
  }

    public function showThisWeekIncomeInvoicesAjax(){
      $period = Date::getThisWeek();
      $invoice = new Invoice($period);
      $invoices = $invoice->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showThisMonthIncomeInvoicesAjax(){
      $period = Date::getThisMonth();
      $invoice = new Invoice($period);
      $invoices = $invoice->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showLastMonthIncomeInvoicesAjax(){
      $period = Date::getLastMonth();
      $invoice = new Invoice($period);
      $invoices = $invoice->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }
      
    public function showChosenPeriodIncomeInvoicesAjax(){
      $period = Date::getChosenPeriod($_POST);
      $invoice = new Invoice($period);
      $invoices = $invoice->getIncomeInvoicesFromDB();
      echo json_encode($invoices);
    }

    /**
     * Show expense invoices
     * @return void
     */
    public function showThisWeekExpenseInvoicesAjax(){
      $period = Date::getThisWeek();
      $invoice = new Invoice($period);
      $invoices = $invoice->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showThisMonthExpenseInvoicesAjax(){
      $period = Date::getThisMonth();
      $invoice = new Invoice($period);
      $invoices = $invoice->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showLastMonthExpenseInvoicesAjax(){
      $period = Date::getLastMonth();
      $invoice = new Invoice($period);
      $invoices = $invoice->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function showChosenPeriodExpenseInvoicesAjax(){
      $period = Date::getChosenPeriod($_POST);
      $invoice = new Invoice($period);
      $invoices = $invoice->getExpenseInvoicesFromDB();
      echo json_encode($invoices);
    }

    public function editIncomeInvoiceAjaxAction(){
      $invoice = new Invoice($_POST);
      $invoices = $invoice->editIncomeInvoice();
      echo json_encode($invoices);
    }

    public function editExpenseInvoiceAjax(){
      $invoice = new Invoice($_POST);
      $invoices = $invoice->editExpenseInvoice();
      echo json_encode($invoices);
    }

    public function deleteIncomeInvoiceAjax(){
      $invoice = new Invoice($_POST);
      $invoices = $invoice->deleteIncomeInvoice();
      echo json_encode($result);
    }

    public function deleteExpenseInvoiceAjax(){
      $invoice = new Invoice($_POST);
      $invoices = $invoice->deleteExpenseInvoice();
      echo json_encode($result);
    }
}

?>