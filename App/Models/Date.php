<?php
namespace App\Models;
class Date extends \Core\Model{
    public $errors =[];

    public function __construct($data=[]){
        foreach($data as $key=>$value){
            $this->$key = $value;
        }
    }
    public static function validate(){
        if($_POST['startingDate'] == ''){
            $errors[] = 'Należy podać datę początkową.';
        }
        if($_POST['endingDate'] == ''){
            $error[] = 'Należy podać datę końcową.';
        }
        if($_POST['startingDate'] < $_POST['endingDate']){
            $startingDate = $_POST['startingDate'];
            $endingDate = $_POST['endingDate'];
        }
        else{
            $startingDate = $_POST['endingDate'];
            $endingDate = $_POST['startingDate'];
        }
    }
    public static function getThisWeek(){
        $period=[];
        $period['startingDate'] = date('Y-m-d', strtotime('monday this week'));
        $period['endingDate'] = date('Y-m-d', strtotime('sunday this week'));
        return $period;
    }
    public static function getThisMonth(){
        $period=[];
        $period['startingDate'] = date('Y-m-d', strtotime('first day of this month'));
        $period['endingDate'] = date('Y-m-d', strtotime('last day of this month'));
        return $period;
    }
    public static function getLastMonth(){
        $period=[];
        $period['startingDate'] = date('Y-m-d', strtotime("first day of last month"));
        $period['endingDate'] = date('Y-m-d', strtotime("last day of last month"));
        return $period;
    }
}



?>
