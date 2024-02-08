<?php
class Translator extends EntityRepository {
    
   public function __construct() {
       
   }
   
   public function _getTranslation($key){
       $keyOrigin = $key;
       $key = strtolower($key);
       $key = str_replace(" ", "", $key);
       $key = trim($key);
       
       if(isset($_SESSION['Wtranslator'][$key])){
           return ($_SESSION['Wtranslator'][$key]);
       }else{
           return $keyOrigin;
       }
   }
   
   public function setLenguage($lenguage){
       $query = "SELECT id,$lenguage FROM translator";
       $result = $this->query($query);
       
       if($result->num_rows > 0){
           $array = array();
          while($row = $result->fetch_object()){
              $array[$row->id] = $row->$lenguage;
          }
          
          $_SESSION['Wtranslator'] =  $array;
       }
       return array();
   }
}