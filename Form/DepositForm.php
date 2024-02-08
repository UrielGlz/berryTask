<?php
class DepositForm extends Form {    
    public function __construct() {
        $this->setName('deposit');
        $this->setActionForm('Deposit.php');
        $this->setEnctype('multipart/form-data');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {      
        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');        
        $attributes_wrapper_append_date = array('id'=>'dateDatePicker');    
         
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'total',
            'required'=>false,
            'validators'=>array('double')
        ));        
         
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'status',
            'required'=>false
        ));                
        
        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'date',
            'label'=>'Fecha',
            'validators'=>array('date'),
            'required'=>true,
            'value'=>date('m/d/Y'),
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));        
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=>true
         ));                   
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Notas',
            'required'=>false
        ));          
        
        $this->addElement(array(
            'id'=> 'attachments',
            'type' => 'file',
            'name' => "attachments[]",
            'label' => 'Documentos adjuntos',
            'class' => 'file upload',
            'required' => false,
            'optionals' => array(
                'title' => 'Documentos adjuntos',
                'multiple'=>'',
                'data-show-preview'=>false,
                'data-show-upload'=>false
            )
        ));       
        
        $this->addElement(array(
            'id'=> 'attachments',
            'type' => 'file',
            'name' => "deposit_file",
            'label' => 'Comprobante deposito',
            'class' => 'file upload',
            'required' => false,
            'optionals' => array(
                'title' => 'Comprobante deposito',
                'data-show-preview'=>false,
                'data-show-upload'=>false
            )
        ));     
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'addDepositDetail',
            'value'=>$this->_getTranslation('Agregar detalle'),
            'class'=>'btn btn-default _addDepositDetail'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
            'optionals'=>array("onClick"=>"submit('deposit')")
        ));        
    }
    
     public function getListStores(){
        $repo = new StoreRepository();
        $result = $repo->getListSelectStores();
        
        $login = new Login();
        if($login->getRole()!='1'){
            $storesArray = explode(',', $login->getStoreId());
            if(count($storesArray) > 1){$array = array(''=>'Seleccionar una opcion...');}
            if ($result) {
                $array = array();
                foreach ($result as $key => $value) {
                    if(in_array($key, $storesArray)){$array[$key] = $value;}
                }
            }   
        }else{
            $array = array(''=>'Seleccionar una opcion...');
            if ($result) {               
                foreach ($result as $key => $value) {
                    $array[$key] = $value;
                }
            }   
        } 
        return $array;
    }
    
    
    public function populate($data) { 
        $tools = new Tools();
        if(isset($data['date']) && $tools->isValidaDateYYYMMDD($data['date'])){
            $data['date'] = $tools->setFormatDateToForm($data['date']);
        }  
        
        parent::populate($data);
    } 
}