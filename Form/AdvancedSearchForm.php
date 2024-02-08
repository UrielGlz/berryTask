<?php
class AdvancedSearchForm extends Form {

    public function __construct($controller) {      
        $this->setName('advanced_search');
        $this->setActionForm($controller.".php");
        switch($controller){
            case 'User':
            case 'SalesRecord':
                $actionController = '';
                break;
            default:
                $actionController = 'list';
                break;
        }
        $this->setActionController($actionController);
        $this->setMethod('post');
        $this->setDefaultFormLabelsColSize('4');
        $this->setDefaultFormElementsColSize('8');
        parent::__construct();
        $this->init();
    }

    public function init() {         
        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
        $this->addElement(array(
            'type' => 'text',
            'name' => 'startDate',
            'label'=>'Fecha inicio',
            'required'=>false,
            'append'=>$append
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'endDate',
            'label'=>'Fecha fin',
            'required'=>false,
            'append'=>$append
        ));               
        
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'status_payment',
            'label'=>'Status de pago',
            'multiOptions'=>array('1'=>'Pendiente','2'=>'Pagada'),
            'required'=>false,
        ));
        
       
        
        $this->addElement(array(
            'type' => 'submit',
            'name' => 'search',
            'value'=> $this->_getTranslation('Buscar'),
            'class'=>'btn btn-primary'
        ));
    }
    
    public function getPurchaseStatus(){
        $manifestRepo = new ManifestRepository();
        $purchaseRepo = new PurchaseGoodAndServiceRepository();
        
        $manifestStatus = $manifestRepo->getListStatus();
        $purchaseStatus = $purchaseRepo->getListStatus();
        
        $array = array();
        foreach($manifestStatus as $idStatus => $descriptionStatus){
            $array[$idStatus] = $descriptionStatus.' - '.$this->_getTranslation('Producto');
        }
        
        foreach($purchaseStatus as $idStatus => $descriptionStatus){
            $idStatus += 20;
            $array[$idStatus] = $descriptionStatus.' - '.$this->_getTranslation('Bienes y servcios');
        }
        
        return $array;
        
    }
    
    public function getLisUserStatus(){
        $shipingRequestRepo = new UserRepository();        
        $salesOrderStatus = $shipingRequestRepo->getListStatus();
        
        $array = array();
        foreach($salesOrderStatus as $idStatus => $descriptionStatus){
            $array[$idStatus] = $descriptionStatus;
        }    
        
        return $array;        
    }
    
     public function getListStoreStatus(){
        $storeRepo = new StoreRepository();        
        $storeStatus = $storeRepo->getListStatus();
        
        $array = array();
        foreach($storeStatus as $idStatus => $descriptionStatus){
            $array[$idStatus] = $descriptionStatus;
        }    
        
        return $array;        
    }
    
    public function getPaymentStatus(){
        $repository = new PagoRepository();        
        $status = $repository->getListStatus();
        
        $array = array();
        foreach($status as $idStatus => $descriptionStatus){
            $array[$idStatus] = $descriptionStatus;
        }    
        
        return $array;        
    }
    
    public function getStringFiltersForm($controller){
        $formString = '';
        switch($controller){   
            case 'User':
                $userForm = new UserForm();            
                
                $this->addElement(array('type' => 'text','name' => 'user','label'=>'Usuario','required'=>false,));
                
                $this->addElement(array('type'=>'select multiple','name'=>'role','label'=>'Rol'));
                $arrayUsers = $userForm->getListRoles();
                if(key_exists('', $arrayUsers)){unset($arrayUsers['']);}
                $this->setPropiedad('role', array('multiOptions'=>$arrayUsers));    
                
                $this->addElement(array('type'=>'select multiple','name'=>'store','label'=>'Sucursal'));
                $arrayStores = $userForm->getListStores();
                if(key_exists('', $arrayStores)){unset($arrayStores['']);}
                $this->setPropiedad('store', array('multiOptions'=>$arrayStores));    
                

                $this->addElement(array('type' => 'select multiple','name' => 'status','label'=>'Status','multiOptions'=>$this->getLisUserStatus(),'required'=>false,));                
                
                $formString .= $this->getElementString('user');  
                $formString .= $this->getElementString('store');     
                $formString .= $this->getElementString('role');        
                $formString .= $this->getElementString('status');                                     
                
                break;
                
            case 'SalesRecord':
            case 'Deposit':
                $salesRecordForm = new SalesRecordForm();            
                
                $this->addElement(array('type'=>'select multiple','name'=>'store_id','label'=>'Sucursal'));
                $arrayStores = $salesRecordForm->getListStores();
                if(key_exists('', $arrayStores)){unset($arrayStores['']);}
                $this->setPropiedad('store_id', array('multiOptions'=>$arrayStores));                    

                $this->addElement(array('type' => 'select multiple','name' => 'status','label'=>'Status','multiOptions'=>$this->getListStoreStatus(),'required'=>false,));                
                
                $formString .= $this->getElementString('store_id');  
                $formString .= $this->getElementString('startDate');     
                $formString .= $this->getElementString('endDate');        
                $formString .= $this->getElementString('status');                                     
                
                break;
        }       
        
        return $formString;
    }

}
