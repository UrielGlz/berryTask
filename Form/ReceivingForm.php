<?php
class ReceivingForm extends Form {

    public function __construct() {
        $this->setActionForm('Receiving.php');
        $this->setName('receiving');
        $this->setClass('receiving');
        $this->setMethod('post');
        parent::__construct();
        $this->init();
    }

    public function init() {    
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'status'
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'type'
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'reference_id'
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'store_id_of_document'
        ));
                
        $this->addElement(array(
            'type' => 'select',
            'name' => 'document_reference',
            'multiOptions'=>$this->getListPurchases(),
            'label'=>'Referencia',
            'required'=> true
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'date_time',
            'label'=>'Fecha de recibo',
            'required'=> true
        ));
        
         $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Notas',
            'required'=>false
        ));          
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'agregar_producto',
            'value'=>$this->_getTranslation('Agregar producto'),
            'class'=>"btn btn-default",
        )); 
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Terminar'),
            'class'=>'btn btn-primary',
        ));        

    }
    
    public function getListStatus(){
        $repository = new ReceivingRepository();
        $list = $repository->getListStatus();
         
        $array = array();
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
    
    public function getListPurchases(){
        $repository = new ReceivingRepository();
        $list = $repository->getListDocumentsPendingToReceieve();
         
        $array = array(''=>'Seleccionar compra a recibir...');
        if($list){
            foreach($list as $key => $value){
                $array[$key] = $value;
            }
        }       
        return $array;            
    }    
    
     public function populate($data) { 
        $tools = new Tools();
        if(isset($data['date_time']) && substr_count($data['date_time'], '-') > 0){
            $data['date_time'] = $tools->setFormatDateTimeToForm($data['date_time']);
        }
        
        if($this->getActionController() == 'edit'){
            /*
            $repository = new ReceivingRepository();
            $list = $repository->getListDocumentsPendingToReceieve($data['type'],$data['reference_id']);
            $array = array(''=>'Seleccionar compra a recibir...');
            if($list){
                foreach($list as $key => $value){
                    $array[$key] = $value;
                }
            }
            
            $this->addProperties('document_reference', array('multiOptions'=>$array));
             * */
            
            if($this->getActionController() == 'edit'){
            $repo = new ReceivingRepository();
            $currentData = $repo->getById($this->getId());
            
            if($currentData['type'] == 'purchase'){
                $purchaseRepo = new PurchaseRepository();
                $documentReference = $purchaseRepo->getById($currentData['reference_id']);
                $reference = 'Compra #'.$documentReference['id'].' - '.$documentReference['vendorName'];
            }
            elseif($currentData['type'] == 'transfer'){
                $transferRepo = new TransferRepository();
                $documentReference = $transferRepo->getById($currentData['reference_id']);
                $reference = 'Traspaso #'.$documentReference['id'].' - '.$documentReference['fromStoreName'];
            }
            
            
            $options[$currentData['type'].'-'.$currentData['reference_id']] = $reference;            
            $this->setPropiedad('document_reference', array('multiOptions'=>$options));    
        }
        }
        
        parent::populate($data);
    } 
}
