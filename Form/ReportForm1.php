<?php
/**
 * Description of LoginForm
 * @author carlos augusto vazquez lara
 */
class ReportForm extends Form {
    public function __construct() {
        $this->setActionForm('Reports.php');
        $this->setActionController('create');
        $this->setName('reportes');
        $this->setMethod('post');
        $this->setTarget('_report');
        $this->setDefaultFormLabelsColSize('4');
        $this->setDefaultFormElementsColSize('7');
        parent::__construct();
        $this->init();
        
    }

    public function init() {
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'report',
            'required'=>true
        ));   
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'output',
            'label'=>'Bajar reporte',
            'required'=> true
        )); 
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'optionsFromGet',
            'required'=>true
        ));   
        
        $attributes_wrapper_append_date = array('class'=>'date','id'=>'fechaDatePicker');
        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'startDate',
            'label'=>'Fecha inicio',
            'required'=>false,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'endDate',
            'label'=>'Fecha fin',
            'required'=>false,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'dueStartDate',
            'label'=>'Fecha pago inicio',
            'required'=>false,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));
                
        $this->addElement(array(
            'type' => 'text',
            'name' => 'dueStartDate',
            'label'=>'Fecha pago fin',
            'required'=>false,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_date
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'store_id',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=> true
        ));       
        
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'customer_id',
            'label'=>'Cliente',
            'multiOptions'=>$this->getListCustomer(),
            'required'=> true
        ));       
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'area_id',
            'label'=>'Area',
            'multiOptions'=>$this->getListAreas()
         ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'area_bakery_production_id',
            'label'=>'Area de produccion',
            'multiOptions'=>$this->getListAreasProduccionPanaderia()
         ));
        
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'user_id',
            'label'=>'Usuario',
            'multiOptions' => $this->getListUsuarios(),
            'required'=> true,
        ));   
        
        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarPresentacion'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'masa',
            'label'=>'Tipo de masa',
            'multiOptions'=>$this->listMasas(),
            'required'=>true,
        ));        
        
        $this->addElement(array(
            'type'=>'submit',
            'name'=>'enviar',
            'value'=>'Enviar',
            'class'=>'btn btn-primary'
        ));
        
        $this->addElement(array(
            'type' => 'select multiple',
            'name' => 'to',
            'label'=>'Para',
            'multiOptions'=>array(),
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'cc',
            'label'=>'Copia'
        ));
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'subject',
            'label'=>'Titulo'
        ));
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'message',
            'label'=>'Mensaje'
        ));
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'sendMail',
            'label'=>'',
            'value'=>$this->_getTranslation('Enviar mail'),
            'class'=>'btn btn-primary',
            'optionals'=>array('onclick'=>'sendReportToMail()')
        ));
        
        $this->addElement(array(
            'type' => 'button',
            'name' => 'cerrar',
            'value'=> $this->_getTranslation('Cerrar'),
            'class'=>'btn btn-default',
            'optionals'=>array(
                'data-dismiss'=>'modal',
                'aria-hidden'=>'true',
                ),
            'col-size-element'=>'12',
        ));
    }
    
    public function getListCustomer(){
        $repository = new CustomerRepository();
        $result = $repository->getListSelectCustomersForBill();
        
         if ($result) {
            $array = array();
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
    
     public function getListUsuarios(){
        $repository = new UserRepository();
        $list = $repository->getListSelectUsers();
         
         $array = array(''=>''); #Para poder aplicar "placeholder"  en select2 en vista
        foreach($list as $key => $value){
            $array[$key] = $value;
        }
        return $array;
    }
    
    public function getListAreas(){
        $repo = new AreaRepository();
        $result = $repo->getListSelectAreas();
        $array = array(''=>'Seleccionar una opcion...');
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
        }   
        
        return $array;
    }
    
    public function listMasas(){
        $repository = new ProductRepository();
        $result = $repository->getListSelectMasas();
        
         $array = array('' => 'Seleccionar una opcion...');
        if ($result) {           
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }   
    
    public function getListAreasProduccionPanaderia(){
        $repository = new AreaRepository();
        $result = $repository->getListSelectAreasProduccionPanaderia();
        
         $array = array('' => 'Seleccionar una opcion...');
        if ($result) {           
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
    }
    
    public function getStringFiltersForm($report){
        $formString = '';
        $login = new Login();
        
        switch($report){            
             case 'time_clock':
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));
                $this->setAsMultipleSelect('store_id');
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('user_id');
                $formString .= $this->getElementString('output');             
                
                break;
            
            case 'review_payroll':
                $this->setPropiedad('output', array('multiOptions'=>array('screen'=>'Pantalla')));
                $this->setAsMultipleSelect('store_id');
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('user_id');
                $formString .= $this->getElementString('output');             
                
                break;
            
            case 'store_request':
                $login = new Login();
                $this->setPropiedad('output', array('multiOptions'=>array('pdf'=>'PDF')));
                
                
                $areaRepo = new AreaRepository();
                if($login->getRole() != '1'){
                    $areaData = $areaRepo->getByRoleId($login->getRole());
                    $array = array($areaData['id']=>$areaData['name']);
                    $this->addProperties('area_id', array('multiOptions'=>$array));           
                }                   
                
                $formString .= $this->getElementString('area_id');
                $formString .= $this->getElementString('masa');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
            
            case 'sales':
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));                
                if($login->getRole() === '1'){$this->setAsMultipleSelect('store_id');}
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
                
             case 'sales_to_store':
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));                
                if($login->getRole() === '1'){$this->setAsMultipleSelect('store_id');}
                
                $this->addElement(array('type'=>'select','name'=>'store_id','label'=>'Sucursal'));
                $storeRepo = new StoreRepository();        
                $this->setPropiedad('store_id', array('multiOptions'=>$storeRepo->getListSelectStores()));                     
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
                
            case 'special_orders':
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));                
                if($login->getRole() === '1'){$this->setAsMultipleSelect('store_id');}
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
                
            case 'physical_inventory':
                $this->setPropiedad('output', array('multiOptions'=>array('screen'=>'Pantalla')));                
                if($login->getRole() === '1'){$this->setAsMultipleSelect('store_id');}
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
                
            case 'bakery_production':
                $login = new Login();
                $this->setPropiedad('output', array('multiOptions'=>array('pdf'=>'PDF')));       
                $this->addProperties('startDate', array('label'=>'Fecha de entrega'));        
                
                $areaRepo = new AreaRepository();
                if($login->getRole() != '1'){
                    $areaBakeryProduction = $login->getAreaBakeryProduction();
                    $areaData = $areaRepo->getListSelectAreasProduccionPanaderia();
                    $array = array($areaBakeryProduction=>$areaData[$areaBakeryProduction]);
                    $this->addProperties('area_bakery_production_id', array('multiOptions'=>$array));           
                }                   
                
                $formString .= $this->getElementString('area_bakery_production_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('output');             
                
                break;
                
            case 'detailed_bakery_orders':
                $login = new Login();
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));                        
                
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
            
            case 'sales_by_store':
                $login = new Login();
                $this->setPropiedad('output', array('multiOptions'=>array('screen'=>'Pantalla','excel'=>'Excel')));                        
                
                $this->setAsMultipleSelect('store_id');
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('output');             
                
                break;
            
            case 'invoices':
                $this->setPropiedad('output', array('multiOptions'=>array('excel'=>'Excel')));                
                if($login->getRole() === '1'){$this->setAsMultipleSelect('store_id');}         
                
                $formString .= $this->getElementString('customer_id');
                $formString .= $this->getElementString('store_id');
                $formString .= $this->getElementString('startDate');
                $formString .= $this->getElementString('endDate');
                $formString .= $this->getElementString('dueStartDate');
                $formString .= $this->getElementString('duEndDate');
                $formString .= $this->getElementString('output');             
                
                break;
        }
        
        return $formString;
    }
}