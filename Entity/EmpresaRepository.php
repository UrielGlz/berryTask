<?php
class EmpresaRepository extends EntityRepository {

    private $table = 'empresa';
    private $options = array(
        'nombre'=>null,
        'calle'=>null,
        'numero'=>null,
        'colonia'=>null,
        'ciudad'=>null,
        'estado'=>null,        
        'codigopostal'=>null,
        'telefono'=>null,
        'email'=>null
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
}