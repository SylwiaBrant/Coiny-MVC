<?php
namespace App\Controllers;
use \Core\View;
use \App\Flash;
use \App\Models\Expense;
use \App\Models\ExpenseWithInvoice;
use \App\Models\Account;
use \App\Models\Date;

/**
 * Incomes controller
 */
class Expenses extends Authenticated{
    
    /**
     * Show expenses
     * @return void
     */
    public function showThisWeekExpensesAction(){
        $period = Date::getThisWeek();
        $expenses = new Expense();
        View::renderTemplate('Expenses/thisWeek.html',[
            'expenses' => $expenses->getExpensesFromDB($period),
            'totalAmount' => $expenses->getExpensesSumFromDB($period)]);
      }

    public function showThisMonthExpensesAction(){
        $period = Date::getThisMonth();
        $expenses = new Expense();
        View::renderTemplate('Expenses/thisMonth.html',[
            'expenses' => $expenses->getExpensesFromDB($period),
            'totalAmount' => $expenses->getExpensesSumFromDB($period)]);
    }

    public function showLastMonthExpensesAction(){
        $period = Date::getLastMonth();
        $expenses = new Expense();
        View::renderTemplate('Expenses/lastMonth.html',[
            'expenses' => $expenses->getExpensesFromDB($period),
            'totalAmount' => $expenses->getExpensesSumFromDB($period)]);
    }

    public function showChosenPeriodExpensesAction(){
    $period = Date::getChosenPeriod($_POST);
    if($period){
        $expenses = new Expense();
        View::renderTemplate('Expenses/chosenPeriod.html',[
            'expenses' => $expenses->getExpensesFromDB($period),
            'totalAmount' => $expenses->getExpensesSumFromDB($period)]);
        } else {
            Flash::addMessage('Proszę wpisać obie daty w formacie YYYY-MM-DD.', Flash::WARNING);
            View::renderTemplate('Expenses/chosenPeriod.html',[]);
        } 
    }

    public function createAction(){

        if (isset($_POST['expenseInvoice'])){
            $expense = new ExpenseWithInvoice($_POST);
        } else {
            $expense = new Expense($_POST);
        }
        if($expense->save()){
            Flash::addMessage('Przychód dodany pomyślnie.'); 
        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', 'WARNING');
        }
        $this->newAction();
    }  

    public function addExpense(){
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
            'expenseCategories' => Account::getExpenseCategories(),
            'paymentMethods' => Account::getPaymentMethods()]);
    }
}
?>