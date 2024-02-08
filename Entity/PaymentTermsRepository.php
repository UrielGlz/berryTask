<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PaymentTermsRepository extends EntityRepository {

    private $table = 'payment_terms';
    private $options = array(
        'name'=>null,
        'days'=>null,
    );
    
    public function setOptions($data){
      foreach ($this->options as $option => $value){
          if(isset($data[$option])){
            $this->options[$option] = $data[$option];
          }
      }
    }
  
    public function getOptions(){
        return $this->options;
    }
    
    public function save(array $data, $table = null) {
        return parent::save($data, $this->table);
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }

    public function update($id, $data, $table = null) {     
        return parent::update($id, $data, $this->table);
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {
        return parent::isUsedInRecord($id, array('customer' => 'payment_terms_id'));
    }
    
    public function getListSelectPaymentTerms($idsPaymentTerms = null){
        if($idsPaymentTerms !== null){ $idsPaymentTerms = " AND find_in_set(id,'$idsPaymentTerms')";}
        
        $query = "SELECT * FROM $this->table WHERE 1 = 1 $idsPaymentTerms ORDER BY days ASC";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $array = array();
            while($row = $result->fetch_object()){
                $array[$row->id] = $row->name; 
            }
            
            return $array;
        }
        
        return null;
    }
    
    public function getListPaymentTerms(){
        $query = "SELECT * FROM $this->table ORDER BY days ASC";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result);
        }
        
        return null;
    }
    
    public function getDueDate($date,$creditDays){
        $query = "SELECT DATE_ADD('{$date}',INTERVAL $creditDays DAY)as due_date";
        $result = $this->query($query);
        
        if($result){
            $result = $result->fetch_object();
            return $result->due_date;
        }
        return '';
    }
}