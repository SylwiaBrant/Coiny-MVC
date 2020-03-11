<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
use \App\Models\ExpenseWithInvoice;
use \App\Models\Category;
use \App\Models\Date;

/**
 * Incomes controller
 */
class Expenses extends Authenticated{
    /**
     * Show incomes
     * @return void
     */
    public function indexAction(){
        $period = Date::getThisMonth();
        $expenses = new Expense();
        View::renderTemplate('Expenses/index.html',[
            'expenses' => $expenses->getExpensesFromDB($period)]);
    }
    /**
     * Show expenses
     * @return void
     */
    public function showThisWeekExpensesAjax(){
        $period = Date::getThisWeek();
        $expense = new Expense();
        $entries = $expense->getExpensesFromDB($period);
        echo json_encode($entries);
      }

    public function showThisMonthExpensesAjax(){
        $period = Date::getThisWeek();
        $expense = new Expense();
        $entries = $expense->getExpensesFromDB($period);
        echo json_encode($entries);
    }

    public function showLastMonthExpensesAjax(){
        $period = Date::getLastMonth();
        $expense = new Expense();
        $entries = $expense->getExpensesFromDB($period);
        echo json_encode($entries);
    }

    public function showChosenPeriodExpensesAjax(){
        $period = Date::getChosenPeriod($_POST);
        if($period){
            $expenses = new Expense();
            $entries = $expense->getExpensesFromDB($period);
            echo json_encode($entries);
        } else {
            Flash::addMessage('Proszę wpisać obie daty w formacie YYYY-MM-DD.', Flash::WARNING);
            View::renderTemplate('Expenses/chosenPeriod.html',[]);
        } 
    }

    public function addExpenseAjax(){
        if (isset($_POST['invoiceCheckbox'])){
            $expense = new ExpenseWithInvoice($_POST);
        } else {
            $expense = new Expense($_POST);
        }
        $result = $expense->save();
        echo json_encode($result);
    }

    /**
     * Expense - add entry
     * @return void
     */
    public function newAction(){
        View::renderTemplate('Expenses/new.html', [
            'expenseCategories' => Category::getExpenseCategories(),
            'paymentMethods' => Category::getPaymentMethods()]);
    }

    public function getExpensesByCategoryAjax(){
        $expense = new Expense($_POST);
        $result = $expense->getExpensesByCategory();
        echo json_encode($result);
    }

    public function getExpensesByPaymentsAjax(){
        $expense = new Expense($_POST);
        $result = $expense->getExpensesByPayments();
        echo json_encode($result);
    }

    public function editExpenseCategoryAjax(){
        $expense = new Expense($_POST);
        $result = $expense->editExpenseCategory();
        echo json_encode($result);
    }

    public function deleteEntryAjax(){
        $expense = new Expense($_POST);
        $result = $expense->deleteEntry();
        echo json_encode($result);
    }
}
?>