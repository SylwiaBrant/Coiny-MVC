<?php
namespace App\Controllers;
use \Core\View;

/**
 * Incomes controller
 */
class Incomes extends Authenticated{
    /**
     * Incomes index
     * @return void
     */
    public function indexAction(){
        View::renderTemplate('Incomes/index.html');
    }
}
?>