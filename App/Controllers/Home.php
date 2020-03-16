<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Invoice;
use \App\Models\Date;
use \App\Models\Category;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends Authenticated
{
    /**
     * Show the index page
     * @return void
     */
    public function indexAction()
    {
        $period = Date::getThisMonth();
        $data = $this->getData($period);
        View::renderTemplate('Home/index.html', $data);
    }

    private function getData($period){
        $data = [];
        $income = new Income($period);
        $expense = new Expense($period);
        $invoice = new Invoice();
        $incomesSum = $income->getIncomesSumFromDB();
        $expensesSum = $expense->getExpensesSumFromDB();
        $balance = $incomesSum-$expensesSum;
        $data['invoices'] = $invoice->getDueInvoices();
        $data['incomesSum'] = number_format($incomesSum, 2, ',', ' ');
        $data['expensesSum'] = number_format($expensesSum, 2, ',', ' ');
        $data['balance'] = number_format($balance, 2, ',', ' ');
     //   $data['expenseLimits'] = $expense->getLimitedExpenseCategories();
      //  $data['paymentLimits'] = $expense->getLimitedPaymentMethods();
        $data['lastIncomes'] = $income->getLastTransactionPerCategory();
        $data['lastExpenses'] = $expense->getLastTransactionPerCategory();
        return $data;
    }

    public function getIncomeCategoriesSumsAjax(){
        $period = Date::getThisMonth();
        $income = new Income($period);
        $expense = new Expense($period);
        $incomeCategoriesSums = $income->getSumsByCategory();
        $expenseCategoriesSums = $expense->getSumsByCategory();
        $expenseLimits = $expense->getLimitedExpenseCategories();
        $paymentLimits = $expense->getLimitedPaymentMethods();
        $response = array(
            'incomeCategoriesSums' => $incomeCategoriesSums,
            'expenseCategoriesSums' => $expenseCategoriesSums,
            'expenseLimits' => $expenseLimits,
            'paymentLimits' => $paymentLimits,
        );
        echo json_encode($response);
    }

}
