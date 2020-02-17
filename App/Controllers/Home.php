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
        $data['invoices'] = $invoice->getDueInvoices();
        $data['incomesSum'] = $income->getIncomesSumFromDB($period);
        $data['expensesSum'] = $expense->getExpensesSumFromDB($period);
        $data['incomeCategoriesSums'] = $income->getSumsByCategory($period);
        $data['expenseCategoriesSums'] = $expense->getSumsByCategory($period);
        $data['lastIncomes'] = $income->getLastTransactionPerCategory();
        $data['lastExpenses'] = $expense->getLastTransactionPerCategory();
        return $data;
    }

}
