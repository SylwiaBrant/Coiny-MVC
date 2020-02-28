<?php
    namespace App\Controllers;
    use \Core\View;
    use \App\Auth;
    use \App\Models\Account;
    /**
     * Account controller
     * PHP version 7.4.2
     */
    class Settings extends Authenticated{
        /**
         * Validate if email is avaible (AJAX)
         * @return void
         */
        public function indexAction(){
        View::renderTemplate('settings/settings.html');

        }
/** Functions fetching all user assigned categories 
 *  @return array
*/
        public function getIncomeCategories(){
            $categories = Account::getIncomeCategories();
            echo json_encode($categories);
        }

        public function getExpenseCategories(){
            $categories = Account::getExpenseCategories();
            echo json_encode($categories);
        }

        public function getPaymentMethods(){
            $methods = Account::getPaymentMethods();
            echo json_encode($methods);
        }
/** Functions deleting single user category
 * @return int - number of affected rows
 */
        public function removeIncomeCategory(){
            $account =  new Account();
            $result = $account->removeIncomeCategory();  
            echo json_encode($result);
        }

        public function removeExpenseCategory(){
            $account =  new Account();
            $result = $account->removeExpenseCategory();  
            echo json_encode($result);
        }

        public function removePaymentMethod(){
            $account =  new Account();
            $result = $account->removePaymentMethod();  
            echo json_encode($result);
        }
/** Functions updating single user category 
 *  @return int - number of affected rows
*/
        public function editExpenseCategory(){
            $account =  new Account();
            $result = $account->editExpenseCategory();  
            echo json_encode($result);
        }

        public function editPaymentMethod(){
            $account =  new Account();
            $result = $account->editPaymentMethod();  
            echo json_encode($result);
        }
/** Functions adding single new user category
 *  @return int - number of affected rows
 */
        public function addIncomeCategory(){
            $account =  new Account();
            $result = $account->addIncomeCategory();  
            echo json_encode($result);
        }

        public function addExpenseCategory(){
            $account =  new Account();
            $result = $account->addExpenseCategory();  
            echo json_encode($result);
        }

        public function addPaymentMethod(){
            $account =  new Account();
            $result = $account->addPaymentMethod();  
            echo json_encode($result);
        }
    }

?>