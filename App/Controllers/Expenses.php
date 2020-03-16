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
        $expenses = new Expense($period);
        View::renderTemplate('Expenses/index.html',[
            'expenses' => $expenses->getExpensesFromDB()]);
    }
    /**
     * Show expenses
     * @return void
     */
    public function showThisWeekExpensesAjax(){
        $period = Date::getThisWeek();
        $expense = new Expense($period);
        $entries = $expense->getExpensesFromDB();
        echo json_encode($entries);
    }

    public function showThisMonthExpensesAjax(){
        $period = Date::getThisWeek();
        $expense = new Expense($period);
        $entries = $expense->getExpensesFromDB();
        echo json_encode($entries);
    }

    public function showLastMonthExpensesAjax(){
        $period = Date::getLastMonth();
        $expense = new Expense($period);
        $entries = $expense->getExpensesFromDB();
        echo json_encode($entries);
    }

    public function showChosenPeriodExpensesAjax(){
        $period = Date::getChosenPeriod($_POST);
        $expenses = new Expense($period);
        $entries = $expense->getExpensesFromDB();
        echo json_encode($entries);
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

    public function getLimitedExpenseCategories($period){
        $expense = new Expense($period);
        $entries = $expense->getLimitedExpenseCategories();
        echo json_encode($entries);
    }

    public function getLimitedPaymentMethods($period){
        $expense = new Expense($period);
        $entries = $expense->getLimitedPaymentMethods();
        echo json_encode($entries);
    }

    public function editExpenseCategoryAjax(){
        $expense = new Expense($_POST);
        $result = $expense->editExpenseCategory();
        echo json_encode($result);
    }

    public function editExpensePaymentAjax(){
        $expense = new Expense($_POST);
        $result = $expense->editExpensePayment();
        echo json_encode($result);
    }

    public function editExpenseAjax(){
        $expense = new Expense($_POST);
        $result = $expense->editExpense();
        echo json_encode($result);
    }

    public function deleteExpenseAjax(){
        $expense = new Expense($_POST);
        $result = $expense->deleteExpense();
        echo json_encode($result);
    }
}
?>