<?php
class  SettingsRepository extends EntityRepository {

    private $table = 'settings';
    private $options = array();
    
    public function __construct(){
        $this->setOptions($this->getListSettings());
    }
    
    public function setOptions($options){
        $this->options = $options;
    }
    
    public function _get($setting){
        if(isset($this->options[$setting])){
            return $this->options[$setting];
        }
        return null;
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
    
    public function getListSettings(){
        $query = "SELECT * FROM settings";
        $result = $this->query($query);
        
        if($result->num_rows > 0){
            $result = $this->resultToArray($result);
            $settings = array();
            foreach($result as $row){
                $settings[$row['key']] = $row['value'];
            }
            
            return $settings;
        }
    }
}