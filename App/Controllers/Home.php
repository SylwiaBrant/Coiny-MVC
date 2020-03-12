<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Invoice;
use \App\Models\Date;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends Authenticated
{

    /**
     * Show the index page
     *
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
        $income = new Income();
        $expense = new Expense();
        $invoice = new Invoice();
        $incomesSum = $income->getIncomesSumFromDB($period);
        $expensesSum = $expense->getExpensesSumFromDB($period);
        $balance = $incomesSum-$expensesSum;
        $data['invoices'] = $invoice->getDueInvoices();
        $data['incomesSum'] = number_format($incomesSum, 2, ',', ' ');
        $data['expensesSum'] = number_format($expensesSum, 2, ',', ' ');
        $data['balance'] = number_format($balance, 2, ',', ' ');
        $data['lastIncomes'] = $income->getLastTransactionPerCategory();
        $data['lastExpenses'] = $expense->getLastTransactionPerCategory();
        return $data;
    }

    public function getIncomeCategoriesSums(){
        $period = Date::getThisMonth();
        $income = new Income();
        $expense = new Expense();
        $incomeCategoriesSums = $income->getSumsByCategory($period);
        $expenseCategoriesSums = $expense->getSumsByCategory($period);
        $response = array(
            'incomeCategoriesSums' => $incomeCategoriesSums,
            'expenseCategoriesSums' => $expenseCategoriesSums,
        );
        echo json_encode($response);
    }

}
