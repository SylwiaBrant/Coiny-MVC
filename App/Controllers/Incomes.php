<?php
namespace App\Controllers;
use \Core\View;
use \App\Models\Income;
use \App\Models\IncomeWithInvoice;
use \App\Models\Category;
use \App\Models\Date;
/**
 * Incomes controller
 */
class Incomes extends Authenticated{
    
      /**
       * Show incomes
       * @return void
       */
  
    public function indexAction(){
        $period = Date::getThisMonth();
        $incomes = new Income($period);
        View::renderTemplate('Incomes/index.html',[
            'incomes' => $incomes->getIncomesFromDB()]);
    }

    public function showThisWeekIncomesAjax(){
        $period = Date::getThisWeek();
        $income = new Income($period);
        $entries = $income->getIncomesFromDB();
        echo json_encode($entries);
    }

    public function showThisMonthIncomesAjax(){
        $period = Date::getThisMonth();
        $incomes = new Income($period);
        $entries = $incomes->getIncomesFromDB();
        echo json_encode($entries);
    }

    public function showLastMonthIncomesAjax(){
        $period = Date::getLastMonth();
        $incomes = new Income($period);
        $entries = $incomes->getIncomesFromDB();
        echo json_encode($entries);
    }
        
    public function showChosenPeriodIncomesAjax(){
        $period = Date::getChosenPeriod($_POST);
        if($period){
            $incomes = new Income($period);
            $entries = $incomes->getIncomesFromDB();
            echo json_encode($entries);
        }else {
            Flash::addMessage('Proszę wpisać obie daty w formacie YYYY-MM-DD.', Flash::WARNING);
            View::renderTemplate('Incomes/chosenPeriod.html',[]);
        }
    }
    
    public function createAction(){

        if ($remember_me = isset($_POST['incomeInvoice'])){
            $income = new IncomeWithInvoice($_POST);
        } else {
            $income = new Income($_POST);
        }
        if($income->save()){
            Flash::addMessage('Przychód dodany pomyślnie.'); 

        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', 'WARNING');
        }
        $this->newAction();
    }  

    public function addIncomeAjax(){
        if (isset($_POST['invoiceCheckbox'])){
            $income = new IncomeWithInvoice($_POST);
        } else {
            $income = new Income($_POST);
        }
        $result = $income->save();
        echo json_encode($result);
    }

    /**
     * Incomes - add entry
     * @return void
     */
    public function newAction(){
        $category = new Category();
        $categories = $category->getIncomeCategories();
        View::renderTemplate('Incomes/new.html', [
            'incomeCategories' => $categories]);
    }

    public function deleteEntryAjax(){
        $income = new Income($_POST);
        $result = $income->deleteEntry();
        echo json_encode($result);
    }

    public function getIncomesByCategoryAjax(){
        $income = new Income($_POST);
        $result = $income->getIncomesByCategory();
        echo json_encode($result);
    }
}
?>