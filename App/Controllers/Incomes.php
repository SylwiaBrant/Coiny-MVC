<?php
namespace App\Controllers;
use \Core\View;
use \App\Flash;
use \App\Models\Income;
use \App\Models\IncomeWithInvoice;
use \App\Models\Account;
/**
 * Incomes controller
 */
class Incomes extends Authenticated{
    
    private $incomes = [];
    /**
     * Incomes index
     * @return void
     */
    private $sum;
    public function indexAction(){
        View::renderTemplate('Incomes/index.html', []);
    }

    public function showThisWeekAction(){
        $this->incomes = $this->getIncomeEntries();
        $this->sum = Income::getIncomesSumFromDB();
        View::renderTemplate('Incomes/index.html', [
            'incomeCategories' => Account::getIncomeCategories(),
            'incomes' => $this->incomes,
            'totalAmount' => $this->sum]);
       // var_dump($this->incomes);
    }

    public function createAction(){

        if ($remember_me = isset($_POST['incomeInvoice'])){
            $income = new IncomeWithInvoice($_POST);
        } else {
            $income = new Income($_POST);
        }
        if($income->save()){
            Flash::addMessage('Przychód dodany pomyślnie.'); 
            View::renderTemplate('Incomes/index.html');
        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', 'WARNING');
            View::renderTemplate('Incomes/index.html');
        }
    }  

    protected function getIncomeEntries(){
        return $incomes = Income::getIncomesFromDB();
    }

    /**
     * Incomes - add entry
     * @return void
     */
    public function newAction(){
        View::renderTemplate('Incomes/new.html', [
            'incomeCategories' => Account::getIncomeCategories()]);
       // var_dump($this->incomes);
    }
}
?>