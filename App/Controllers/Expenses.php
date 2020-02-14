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
            'expenses' => $expenses->getIncomesFromDB($period),
            'totalAmount' => $expenses->getIncomesSumFromDB($period)]);
      }

    public function showThisMonthExpensesAction(){
        $period = Date::getThisMonth();
        $expenses = new Expense();
        View::renderTemplate('Expenses/thisMonth.html',[
            'expenses' => $expenses->getIncomesFromDB($period),
            'totalAmount' => $expenses->getIncomesSumFromDB($period)]);
    }

    public function showLastMonthExpensesAction(){
        $period = Date::getLastMonth();
        $expenses = new Expense();
        View::renderTemplate('Expenses/lastMonth.html',[
            'expenses' => $expenses->getIncomesFromDB($period),
            'totalAmount' => $expenses->getIncomesSumFromDB($period)]);
    }

    public function showChosenPeriodExpensesAction(){
    $period = Date::getChosenPeriod($_POST);
    $expenses = new Expense();
    View::renderTemplate('Expenses/chosenPeriod.html',[
        'expenses' => $expenses->getIncomesFromDB($period),
        'totalAmount' => $expenses->getIncomesSumFromDB($period)]);
    }

    public function createAction(){

        if ($remember_me = isset($_POST['expenseInvoice'])){
            $expense = new ExpenseWithInvoice($_POST);
        } else {
            $expense = new Expense($_POST);
        }
        if($expense->save()){
            Flash::addMessage('Przychód dodany pomyślnie.'); 
            View::renderTemplate('Expenses/index.html');
        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', 'WARNING');
            View::renderTemplate('Expenses/addExpense.html');
        }
    }  

    /**
     * Expense - add entry
     * @return void
     */
    public function newAction(){
        View::renderTemplate('Expenses/new.html', [
            'expenseCategories' => Account::getExpenseCategories(),
            'paymentMethods' => Account::getPaymentMethods()]);
       // var_dump($this->incomes);
    }
}
?>