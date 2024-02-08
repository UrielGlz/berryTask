<?php
class SpecialOrderBuscarForm extends Form {    

    public function __construct() {
        $this->setName('search_special_order');
        $this->setActionForm('SpecialOrder.php');
        /*En View_SpecialOrderListProduction lo cambiamos a list-production*/
        $this->setActionController('list');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {       
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores()
         ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'customer',
            'label'=>'Cliente',
            'required'=>false
        )); 

        $this->addElement(array(
            'type' => 'select',
            'name' => 'home_service',
            'label'=>'Servicio a domicilio',
            'multiOptions'=>array('0'=>'Seleccionar una opcion...','Si'=>'Si','No'=>'No'),
            'required'=>true
        ));

        $this->addElement(array(
            'type' => 'select',
            'name' => 'status',
            'label'=>'Status',
            'multiOptions'=>array('0'=>'Seleccionar una opcion...','1'=>'Activa','2'=>'Cancelada'),
            'required'=>true
        )); 

        $this->addElement(array(
            'type' => 'select',
            'name' => 'status_production',
            'label'=>'Status produccion',
            'multiOptions'=>array('0'=>'Seleccionar una opcion...','1'=>'Pendiente','2'=>'Terminada'),
            'required'=>true
        ));          

        $this->addElement(array(
            'type' => 'text',
            'name' => 'startDate',
            'label'=>'Fecha inicio',
            'validators'=>array('date'),
            'required'=>false
        ));

        $this->addElement(array(
            'type' => 'text',
            'name' => 'endDate',
            'label'=>'Fecha fin',
            'validators'=>array('date'),
        ));  

        $this->addElement(array(
            'type'=>'submit',
            'name'=>'search',
            'label'=>'',
            'value'=>$this->_getTranslation('Buscar'),
            'class'=>'btn btn-primary'
        ));
  
        $this->addElement(array(
            'type' => 'button',
            'name' => 'cerrar',
            'value'=> $this->_getTranslation('Cerrar'),
            'class'=>'btn btn-default',
            'optionals'=>array(
                'data-dismiss'=>'modal',
                'aria-hidden'=>'true',
                )
        ));

}
    public function getListCustomers(){
        $repository = new CustomerRepository();
        $result = $repository->getListSelectCustomers();

        $array = array('0' => 'Seleccionar una opcion...');
        if ($result) {      
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
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
}