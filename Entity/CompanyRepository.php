<?php
class CompanyRepository extends EntityRepository {

    private $table = 'company';
    private $flashmessenger = null;    
    private $options = array(
        'name'=>null,       
        'address'=>null,
        'city'=>null,
        'state'=>null,
        'country'=>'USA',
        'zipcode'=>null,
        'contact_name'=>null,
        'phone'=>null,
        'phone_1'=>null,
        'fax'=>null,
        'email'=>null,
        'email_1'=>null,
        'webpage'=>null,
        'status'=>null,
    );
    
    public function __construct() {
        if(!$this->flashmessenger instanceof FlashMessenger){
            $this->flashmessenger = new FlashMessenger();
        }
    }
    
    public function _getTranslation($text){
        $translator = new Translator();
        return $translator->_getTranslation($text);
    }
    
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
    
    public function getTable(){
        return $this->table;
    }
    
    public function getName(){
        return $this->options['name'];
    }
    
    public function getAddress(){
        return $this->options['address'];
    }
    
    public function getCity(){
        return $this->options['city'];
    }
    
    public function getState(){
        return $this->options['state'];
    }
    
    public function getZipCode(){
        return $this->options['zipcode'];
    }
    
     public function getPhone(){
        return $this->options['phone'];
    }

    public function save(array $data, $table = null) {           
        if(is_null($data['status'])){$data['status'] = '1';}      

        $this->startTransaction();
        $rs = parent::save($data, $this->table);
        
        if(!$rs){
            $this->rollback();
            return null;
        }
        $this->commit();
        return true;        
    }
    
    public function delete($id, $table = null) {
        return parent::delete($id, $this->table);
    }
    
    public function update($id, $data, $table = null) {           
        return parent::update($id, $data, $this->table);        
    }

    public function getById($id, $table = null,$selectAux = null) {
        $query = "SELECT * FROM $this->table WHERE id = '$id'";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            return $this->resultToArray($result)[0];
        }
        return null;
    }

    public function isUsedInRecord($id, array $buscarEn = null,$andWhere = null) {        
        return true;
    } 
}