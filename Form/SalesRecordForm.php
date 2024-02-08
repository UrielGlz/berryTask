<?php
class SalesRecordForm extends Form {

    public function __construct() {
        $this->setActionForm('SalesRecord.php');
        $this->setName('salesrecord');
        $this->setClass('salesrecord');
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize(6);
        $this->setDefaultFormElementsColSize(6);
        parent::__construct();
        $this->init();
    }

    public function init() {  
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'allow_edit'
         ));
         
        $this->addElement(array(
            'type' => 'select',
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=>true
         ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'date',
            'label'=>'Fecha',
            'required'=> true,
            //'optionals'=>array('readOnly'=>'readOnly'),
            'value'=>date('m/d/Y h:i A'),
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'initial_cash',
            'label'=>'Efectivo inicial',
            'required'=> false,
            'class'=>'text-right'
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'final_cash',
            'label'=>'Efectivo final',
            'required'=> true,
            'class'=>'text-right _sumSales'
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'debit_card',
            'label'=>'Tarjeta debito',
            'required'=> false,
            'class'=>'text-right _sumSales'
        ));
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'credit_card',
            'label'=>'Tarjeta credito',
            'required'=> false,
            'class'=>'text-right _sumSales'
        ));
        
         $this->addElement(array(
            'type' => 'text',
            'name' => 'check',
            'label'=>'Cheques',
            'required'=> false,
            'class'=>'text-right _sumSales'
        ));
          
        $this->addElement(array(
            'type' => 'text',
            'name' => 'stamp',
            'label'=>'Estampillas',
            'required'=> false,
            'class'=>'text-right _sumSales'
        ));
           
        $this->addElement(array(
            'type' => 'text',
            'name' => 'withdrawal',
            'label'=>'Retiros de efectivo',
            'required'=> false,
            'class'=>'text-right'
        ));
        
         $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Detalles',
            'required'=>false,
            'class'=>'form-control',
            'optionals'=>array('style'=>'min-height:120px')
            
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
        if(isset($data['date']) && substr_count($data['date'], '-') > 0){
            $data['date'] = $tools->setFormatDateTimeToForm($data['date']);
        }
        parent::populate($data);
    } 
}
