<?php
class InvoiceForm extends Form {
    public function __construct() {
        $this->setName('invoice'); 
        $this->setActionForm('Invoice.php');
        $this->setEnctype('multipart/form-data');
        $this->setMethod('post');
        $this->setClass('form_invoice');/*No usar invoice porque se usa en AdminLTE*/
        parent::__construct();
        $this->init();
    }

    public function init() {                
        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');       
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'type',
        ));
        
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'sales_order_id',
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'status',
        ));
        
        $this->addElement(array(
            'type' => 'hidden',
            'name' => 'descuento_items',
            'required'=>false,
            'validators'=>array('double')
        ));    
        
         $this->addElement(array(
            'type' => 'hidden',
            'name' => 'discount_general_amount',
            'required'=>false,
            'validators'=>array('double')
        ));    
        
        $this->addElement(array(
            'type' => 'text',
            'label'=>'Status',
            'name' => 'statusName',
            'optionals'=>array('readOnly'=>'readOnly')
        ));
        
         $this->addElement(array(
            'type' => 'text',
            'name' => 'subtotal',
            'value'=>'0'
        )); 
         
        $this->addElement(array(
            'type' => 'text',
            'name' => 'total',
            'value'=>'0'
        ));         
        
        $settings = new SettingsRepository();
        if($settings->_get('manual_number_invoice') == '1'){
            $this->addElement(array(
                'type' => 'text',
                'label'=>'Factura #',
                'name' => 'invoice_num',
                'required'=>true
            ));
        }
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'date',
            'label'=>'Fecha',
            'value'=>date('m/d/Y'),
            'validators'=>array('date'),
            'required'=>true
        ));
        
        $append = "<span class = 'btn input-group-addon _addCustomer'><i class='fa fa-plus'></i></span>";
        $append .= "<span class = 'btn input-group-addon _editCustomer'><i class='fa fa-pencil'></i></span>";
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'id_customer',
            'label'=>'Cliente',
            'multiOptions'=>$this->listClientes(),
            'required'=>true,
            //'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_select
        ));
        
         $this->addElement(array(
            'type' => 'select',
            'name' => 'billed_to_store',
            'label'=>'Sucursal',
            'multiOptions'=>$this->getListStores(),
            'required'=>true
         ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'shipping_address',
            'label'=>'Direccion de entrega',            
            'class'=>'_selectWith2Rows',
            'required'=>false
        ));        
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'customer_po',
            'label'=>'OC cliente',
            'required'=>false
        ));
        
        $append = "<span class = 'btn input-group-addon _addPaymentTerms'><i class='fa fa-plus'></i></span>";
        $this->addElement(array(
            'type' => 'select',
            'name' => 'payment_terms_id',
            'label'=>'Terminos',
             'multiOptions'=>$this->getListPaymentTerms(),
            'required'=>true,
            'append'=>$append,
            'wrapper_attributes'=>$attributes_wrapper_append_select
        )); 
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'due_date',
            'label'=>'Fecha de pago',
            'optionals'=>array('readOnly'=>true),
            'required'=>true,
        )); 
        
        $this->addElement(array(
            'type' => 'textarea',
            'name' => 'comments',
            'label'=>'Comentarios',
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
            'name' => "customer_po_file",
            'label' => 'OC de cliente',
            'class' => 'file upload',
            'required' => false,
            'optionals' => array(
                'title' => 'OC de cliente',
                'data-show-preview'=>false,
                'data-show-upload'=>false
            )
        ));     
        
         $this->addElement(array(
            'type' => 'textarea',
            'name' => 'message_on_invoice',
            'label'=>'Mensaje en factura',
            'optionals'=>array('placeholder'=>'Este mensaje se mostrara en la factura.')
        ));
        
        $this->addElement(array(
            'type' => 'select',
            'name' => 'discount_general_type',
            'multiOptions'=>array('amount'=>'Descuento por monto','percent'=>'Descuento por porcentaje'),
            'class'=>'form-control',
        )); 
        
        $this->addElement(array(
            'type' => 'text',
            'name' => 'discount_general',
            'value'=>'0',
            'class'=>'text-right form-control _maskDouble',
            'optionals'=>array('style'=>'width:70px;padding:5px')
        )); 
        
         /*Auxiliares */        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'agregar_producto',
            'value'=>$this->_getTranslation('Agregar producto'),
            'class'=>"btn btn-default",
            'optionals'=>array(
                'data-mixto'=>'0',
                'data-controller'=>'Invoice',
                'data-type'=>'produce')
        )); 
        
        $this->addElement(array(
            'type'=>'button',
            'name'=>'terminar',
            'value'=>$this->_getTranslation('Guardar'),
            'class'=>'btn btn-primary',
            'optionals'=>array("onClick"=>"submit('invoice')")
        ));    
        
         $this->addElement(array(
            'type'=>'button',
            'name'=>'agregar_producto_o_servicio',
            'value'=>$this->_getTranslation('Agregar detalle'),
            'class'=>"btn btn-default", 
             'optionals'=>array(
                'data-type'=>'product')
        )); 
    }
    
     public function getListStores(){
        $repo = new StoreRepository();
        $result = $repo->getListSelectStores();
        
        if(count($result)>1){ $array = array(''=>'Seleccionar una opcion...');}
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }        
    }
    
    public function listClientes(){
        $repository = new CustomerRepository();
        $result = $repository->getListSelectCustomersForBill();
        
         if ($result) {
            $array = array();
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }
            return $array;
        }
        /*
        $settings = new SettingsRepository();
        $customer_id = $settings->_get('customer_id_for_parisbakery');
        
        $customeRepo = new CustomerRepository();
        $customerData = $customeRepo->getById($customer_id);
        
        if($customerData){
            $array = array($customerData['id'] => $customerData['name']);
        }else{
            $array = array(null => 'Seleccionar una opcion...');
        }
        
         return $array;*/
       
    }
    
    public function getListPaymentTerms(){
        $repository = new PaymentTermsRepository();
        $result = $repository->getListSelectPaymentTerms();        
        
        $array = array(''=>'Seleccionar una opcion...');        
        if ($result) {               
            foreach ($result as $key => $value) {
                $array[$key] = $value;
            }           
        }
        return $array;
    }    
    
    public function getListProductos(){
        $repository = new ProductRepository();
        $productos = $repository->getListSelectProducts();
        
        $array = array(''=>'Seleccionar una opcion...');
        foreach($productos as $key => $value){            
            $array[$key] = $value;
        }
        
        return $array;
    }
    
    public function populate($data) {
        $tools = new Tools();
        if(isset($data['date']) && $tools->isValidaDateYYYMMDD($data['date'])){
            $data['date'] = $tools->setFormatDateToForm($data['date']);
        }
        
        if(isset($data['due_date']) && $tools->isValidaDateYYYMMDD($data['due_date'])){
            $data['due_date'] = $tools->setFormatDateToForm($data['due_date']);
        }
       
        parent::populate($data);
    }
    
     public function isValid() {
        $repo = new InvoiceRepository();
        $inputs = $repo->inputs_double;
        $this->_rawNumber($inputs);
        
        $valid = parent::isValid(); 
        
        $data = $this->formatDouble($this->getNameValuesElements(), $repo->inputs_double);
        $this->populate($data);
        
        return $valid;
    }
}