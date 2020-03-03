<?php
    namespace App\Controllers;
    use \Core\View;
    use \App\Auth;
    use \App\Models\Category;
    /**
     * Category controller
     * PHP version 7.4.2
     */
    class Settings extends Authenticated{
        /**
         * Validate if email is avaible (AJAX)
         * @return void
         */
        public function indexAction(){
        View::renderTemplate('settings/settings.html', [
            'user' => Auth::getUser(),
            'incomeCategories' => $this->getIncomeCategories(),
            'expenseCategories' => $this->getExpenseCategories(),
            'paymentMethods' => $this->getPaymentMethods()
        ]);

        }
/** Functions fetching all user assigned categories 
 *  @return array
*/
    public function getIncomeCategories(){
        return Category::getIncomeCategories();
    }

    public function getExpenseCategories(){
        return Category::getExpenseCategories();
     //   echo json_encode($categories);
    }

    public function getPaymentMethods(){
        return Category::getPaymentMethods();
     //   echo json_encode($methods);
    }
/** Functions fetching all user assigned categories 
 *  @return array
*/
    public function getIncomeCategoriesAjax(){
        $categories = Category::getIncomeCategories();
        echo json_encode($categories);
    }

    public function getExpenseCategoriesAjax(){
        $categories = Category::getExpenseCategories();
        echo json_encode($categories);
    }

    public function getPaymentMethodsAjax(){
        $methods = Category::getPaymentMethods();
        echo json_encode($methods);
    }
/** Functions deleting single user category
 * @return int - number of affected rows
 */
        public function removeIncomeCategoryAjax(){
            $Category =  new Category();
            $result = $Category->removeIncomeCategory();  
            echo json_encode($result);
        }

        public function removeExpenseCategoryAjax(){
            $Category =  new Category();
            $result = $Category->removeExpenseCategory();  
            echo json_encode($result);
        }

        public function removePaymentMethodAjax(){
            $Category =  new Category();
            $result = $Category->removePaymentMethod();  
            echo json_encode($result);
        }
/** Functions updating single user category 
 *  @return int - number of affected rows
*/
        public function editExpenseCategoryAjax(){
            $Category =  new Category();
            $result = $Category->editExpenseCategory();  
            echo json_encode($result);
        }

        public function editPaymentMethodAjax(){
            $Category =  new Category();
            $result = $Category->editPaymentMethod();  
            echo json_encode($result);
        }
/** Functions adding single new user category
 *  @return int - number of affected rows
 */
        public function addIncomeCategoryAjax(){
            $Category =  new Category();
            $result = $Category->addIncomeCategory();  
            echo json_encode($result);
        }

        public function addExpenseCategoryAjax(){
            $Category =  new Category();
            $result = $Category->addExpenseCategory();  
            echo json_encode($result);
        }

        public function addPaymentMethodAjax(){
            $Category =  new Category();
            $result = $Category->addPaymentMethod();  
            echo json_encode($result);
        }
    }

?>