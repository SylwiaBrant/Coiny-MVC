<?php
namespace App\Models;
class Date extends \Core\Model{
    public $errors = [];
    public function __construct($data = []){
        foreach ($data as $key =>$value){
            $this->$key = $value;
        };
    }
    private function validate(){
        if($this->startingDate == ''){
            $this->errors[] = 'Należy podać datę początkową.';
        }
        if($this->endingDate == ''){
            $this->error[] = 'Należy podać datę końcową.';
        }
        if(!strtotime($this->startingDate)){
            $this->errors[] = 'Data początkowa musi być w formacie YYYY-MM-DD.';
        }
        if(!strtotime($this->endingDate)){
            $this->errors[] = 'Data końcowa musi być w formacie YYYY-MM-DD.';
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

    public static function getChosenPeriod($period){
        $date = (new self($period));
        $date->validate();
        if(empty($date->errors)){
            return $period;
        }
        return false;
    }
}
?>
