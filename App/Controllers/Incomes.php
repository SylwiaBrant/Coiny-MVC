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
     * @return table of incomes
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
        $incomes = new Income($period);
        $entries = $incomes->getIncomesFromDB();
        echo json_encode($entries);
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

    public function editIncomeAjax(){
        $income = new Income($_POST);
        $result = $income->editIncome();
        echo json_encode($result);
    }

    public function deleteIncomeAjax(){
        $income = new Income($_POST);
        $result = $income->deleteIncome();
        echo json_encode($result);
    }

    public function getIncomesByCategoryAjax(){
        $income = new Income($_POST);
        $result = $income->getIncomesByCategory();
        echo json_encode($result);
    }

    public function editIncomeCategoryAjax(){
        $income = new Income($_POST);
        $result = $income->editIncomeCategory();
        echo json_encode($result);
    }
}
?>