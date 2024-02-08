<?php
class  HistoryRepository extends EntityRepository {

    private $table = 'history';
    private $options = array();    
    private $special_history = array(
        'purchases'=>'_purchaseHistory'
    );
    
    public function __construct($table,$options){   
        if(key_exists($table, $this->special_history)){
            $sp = $this->special_history[$table];
            $this->$sp($options);
        }
        
        return true;
    }
    
    public function setOptions($options){
        
    }

    public function save(array $data, $table = null) {
        return parent::save($data, $this->table);
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }

    public function update($id, $data, $table = null) {
    }

    public function getById($id, $table = null,$selectAux = null) {
        return parent::getById($id, $this->table,$selectAux);
    }    
    
    public function _purchaseHistory($options){
        $purchaseRepo = new PurchaseRepository();
        return $purchaseRepo->_history($options);
    }
}