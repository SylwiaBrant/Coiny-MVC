<?php
namespace App\Controllers;
use \Core\View;
use \App\Auth;
/**
 * Incomes controller
 */
class Incomes extends \Core\Controller{
    /**
     * Incomes index
     * @return void
     */
    public function indexAction(){
        if(! Auth::isLoggedIn()){
            exit("Access denied.");
        }
        View::renderTemplate('Incomes/index.html');
    }
}
?>