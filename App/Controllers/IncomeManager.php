<?php
namespace App\Controllers;
use \Core\View;
use \App\Flash;
use \App\Models\Income;

/**
 * Incomes controller
 */
class IncomeManager extends Authenticated{
    
    public $incomes = [];
    /**
     * Incomes index
     * @return void
     */
    private $sum;
    public function indexAction(){
        $this->incomes = $this->getIncomeEntries();
        $this->sum = Income::getIncomesSumFromDB();;
        View::renderTemplate('Incomes/index.html', [
            'incomes' => $this->incomes,
            'totalAmount' => $this->sum]);
       // var_dump($this->incomes);
    }
    public function createAction(){
        $income = new Income($_POST);
        if($income->save()){
            Flash::addMessage('Przychód dodany pomyślnie.');
        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', WARNING);
        }
    }  
    protected function getIncomeEntries(){
        return $incomes = Income::getIncomesFromDB();
    }

}
?>