<?php
namespace App\Controllers;
use \Core\View;
use \App\Flash;
use \App\Models\Income;
use \App\Models\IncomeWithInvoice;
use \App\Models\Account;
use \App\Models\Date;
/**
 * Incomes controller
 */
class Incomes extends Authenticated{
    
      /**
       * Show incomes
       * @return void
       */
      public function showThisWeekIncomesAction(){
        $period = Date::getThisWeek();
        $incomes = new Income();
        View::renderTemplate('Incomes/thisWeek.html',[
            'incomes' => $incomes->getIncomesFromDB($period),
            'totalAmount' => $incomes->getIncomesSumFromDB($period)]);
      }
  
      public function showThisMonthIncomesAction(){
        $period = Date::getThisMonth();
        $incomes = new Income();
        View::renderTemplate('Incomes/thisMonth.html',[
            'incomes' => $incomes->getIncomesFromDB($period),
            'totalAmount' => $incomes->getIncomesSumFromDB($period)]);
      }
  
      public function showLastMonthIncomesAction(){
        $period = Date::getLastMonth();
        $incomes = new Income();
        View::renderTemplate('Incomes/lastMonth.html',[
            'incomes' => $incomes->getIncomesFromDB($period),
            'totalAmount' => $incomes->getIncomesSumFromDB($period)]);
      }
        
      public function showChosenPeriodIncomesAction(){
        $period = Date::getChosenPeriod($_POST);
        if($period){
            $incomes = new Income();
            View::renderTemplate('Incomes/chosenPeriod.html',[
                'incomes' => $incomes->getIncomesFromDB($period),
                'totalAmount' => $incomes->getIncomesSumFromDB($period)]);
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
            View::renderTemplate('Incomes/new.html');
        } else {
            Flash::addMessage('Coś poszło nie tak. Przychód nie dodany.', 'WARNING');
            View::renderTemplate('Incomes/new.html');
        }
    }  

    /**
     * Incomes - add entry
     * @return void
     */
    public function newAction(){
        View::renderTemplate('Incomes/new.html', [
            'incomeCategories' => Account::getIncomeCategories()]);
    }
}
?>