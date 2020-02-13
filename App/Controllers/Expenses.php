<?php
namespace App\Controllers;
use \Core\View;
use \App\Flash;
use \App\Models\Expense;
use \App\Models\ExpenseWithInvoice;
use \App\Models\Account;
/**
 * Incomes controller
 */
class Expenses extends Authenticated{
    
    public $expenses = [];
    /**
     * Expenses index
     * @return void
     */
    private $sum;
    public function indexAction(){
        $this->expenses = Expense::getExpensesFromDB();
        $this->sum = Expense::getExpensesSumFromDB();;
        View::renderTemplate('Expenses/index.html', [
            'expenses' => $this->expenses,
            'totalAmount' => $this->sum]);
       // var_dump($this->incomes);
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