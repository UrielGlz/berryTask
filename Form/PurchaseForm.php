<?php

class PurchaseForm extends Form {    

    public function __construct() {

        $this->setName('purchase');

        $this->setActionForm('Purchase.php');

        $this->setEnctype('multipart/form-data');

        $this->setMethod('post');

        parent::__construct();

        $this->init();

    }



    public function init() {      

        $attributes_wrapper_append_select = array('class'=>'select2-bootstrap-append');        

        $attributes_wrapper_append_date = array('id'=>'dateDatePicker');

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'total',

            //'label'=>'Total',

            'required'=>false,

            'validators'=>array('double'),

            'class'=>'text-right',

            'col-size-element'=>'12',

            'optionals'=>array('style'=>"height:30px;margin-bottom:0px")

        ));    

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'store_id',

            'label'=>'Sucursal',

            'multiOptions'=>$this->getListStores(),

            'required'=>true

         ));

        

         $this->addElement(array(

            'type' => 'checkbox',

            'name' => 'status_approval',

            'label'=>'Aprobar',

            'value'=>'3',

            'class'=>'switch',

        ));           

         

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'status',

            'required'=>false

        ));

         

        $this->addElement(array(

            'type' => 'text',

            'name' => 'compra_descuento',

            'required'=>true,

            'validators'=>array('double'),

            'class'=>'text-right',

            'col-size-element'=>'12',

            'optionals'=>array('style'=>"height:30px;margin-bottom:0px")

        ));   

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'compra_subtotal',

            'required'=>true,

            'validators'=>array('double'),

            'class'=>'text-right',

            'col-size-element'=>'12',

            'optionals'=>array('style'=>"height:30px;margin-bottom:0px")

        ));   

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'compra_iva',

            'required'=>true,

            'validators'=>array('double'),

            'class'=>'text-right',

            'col-size-element'=>'12',

            'optionals'=>array('style'=>"height:30px;margin-bottom:0px")

        ));   

        

         $this->addElement(array(

            'type' => 'text',

            'name' => 'compra_ieps',

            'validators'=>array('double'),

            'required'=>true,

            'class'=>'text-right',

            'col-size-element'=>'12',

             'optionals'=>array('style'=>"height:30px;margin-bottom:0px")

        ));   

         

        

        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";

        $this->addElement(array(

            'type' => 'text',

            'name' => 'date',

            'label'=>'Fecha',

            'validators'=>array('date'),

            'required'=>true,

            'value'=>date('d/m/Y'),

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

        ));

        

        $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProveedor'><i class='fa fa-plus'></i></span>";     

        $this->addElement(array(

            'type' => 'select',

            'name' => 'vendor',

            'label'=>'Proveedor',

            'multiOptions'=>$this->listVendor(),

            'required'=>true,

            'class'=>'selectProveedor', // Clase para selecto cuando se agrega un nuevo proveedor y se recarga lista.

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_select

        ));  

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'reference',

            'label'=>'Factura #',

            'required'=>true

        ));               

                

        $this->addElement(array(

            'type' => 'text',

            'name' => 'lot',

            'label'=>'Lote',

            'required'=>false

        ));

        

        $this->addElement(array(

            'type' => 'select',

            'name' => 'method_payment',

            'label'=>'Forma de pago',

            'multiOptions'=>$this->getListMetodoPago(),

            'required'=>true,

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'credit_days',

            'label'=>'Dias de credito',

            //'required'=>true,

        )); 

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'due_date',

            'label'=>'Fecha de pago',

            //'optionals'=>array('readOnly'=>true),

            'required'=>true,

        )); 

        

        $append = "<div class='input-group-btn descuento_tipo'>

                    <select class='form-control' id='discount_general_type' name='discount_general_type'>                     

                      <option value='porcentaje'>Porcentaje</option>

                    </select>

                  </div>";

        

       $this->addElement(array(

            'type' => 'text',

            'name' => 'discount_general',

            'label'=>'Descuento',

            'required'=>false,

            'append'=>$append,

            'class'=>'numPad'

        ));

       

       $this->addElement(array(

            'type' => 'text',

            'name' => 'requested_by',

            'label'=>'Requerido por',

            'required'=>true

        ));

       

        $this->addElement(array(

            'type' => 'text',

            'name' => 'approved_by',

            'label'=>'Aprobado por',

            'required'=>false,

            'optionals'=>array('readOnly'=>'readOnly')

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

            'name' => "invoice_file",

            'label' => 'Factura de compra',

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

            'name' => 'comments',

            'label'=>'Notas',

            'required'=>false

        ));          

         

         /*Se utiliza para consulta si existe en la tabla de comprasdetalles_X, si existe se actualiza registro.*/

         $this->addElement(array(

            'type' => 'hidden',

            'name' => 'idDetailTemp',

            'required'=>false

        ));      

         

        $this->addElement(array(

            'type' => 'hidden',

            'name' => 'id_product',

        ));

         

         $append = "<span class = 'btn input-group-addon' data-toggle='modal' data-target='#modalAgregarProducto'><i class='fa fa-plus'></i></span>";

         $this->addElement(array(

            'type' => 'text',

            'name' => 'product',

            'label'=>'Producto',

            'optionals'=>array('onKeyPress'=>'onEnterPurchase(event.keyCode,this)','placeholder'=>'Teclea o escanea producto.'),

            'append'=>$append

         ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'quantity',

            'label'=>'Cantidad',

            'validators'=>array('double'),

            'required'=>false,

        ));

        

        $this->addElement(array(

            'type' => 'text',

            'name' => 'cost',

            'label'=>'Costo',

            'validators'=>array('double'),

            'required'=>false,

        ));

        

        $append = "<div class='input-group-btn descuento_tipo'>

                    <select class='form-control' name='discount_type'>                    

                      <option value='monto'>Monto</option>

                      <option value='porcentaje'>Porcentaje</option>

                    </select>

                  </div>";

        

       $this->addElement(array(

            'type' => 'text',

            'name' => 'discount',

            'label'=>'Descuento',

           'append'=>$append,

            'required'=>false

        ));

       

       $this->addElement(array(

            'type' => 'select',

            'name' => 'taxes',

            'label'=>'Impuestos',

            'multiOptions'=>$this->listaImpuestos(),

        ));

       

        $this->addElement(array(

            'type' => 'select',

            'name' => 'taxes_included',

            'label'=>'Impuestos incluidos',

            'multiOptions'=>array('no'=>'No','si'=>'Si'),

        ));

        

        $attributes_wrapper_append_date = array('id'=>'expirationDatePicker');

        $append = "<span class = 'btn input-group-addon'><i class='fa fa-calendar'></i></span>";

        $this->addElement(array(

            'type' => 'text',

            'name' => 'expiration_date',

            'label'=>'Expiracion',

            'validators'=>array('date'),

            'required'=>false,

            'append'=>$append,

            'wrapper_attributes'=>$attributes_wrapper_append_date

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

            'optionals'=>array("onClick"=>"submit('purchase')")

        ));        

    }

    

    public function listVendor(){

        $repository = new VendorRepository();

        $result = $repository->getListSelectVendors();

        

        $array = array('' => 'Seleccionar una opcion...');

        if ($result) {            

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }           

        }

         return $array;

    }

    

    public function getListMetodoPago(){

        $repository = new PurchaseRepository();

        $result = $repository->getListMetodoPago();        



        if(count($result)>1){ $array = array(''=>'Seleccionar una opcion...');}

        if ($result) {               

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

            return $array;

        }

    }    

    

    public function getListProducts(){

        $repository = new SupplieRepository();

        #1 = status activo

        $productos = $repository->getListSupplies();

        

        $array = array('0'=>'Seleccionar una opcion...');        

        foreach($productos as $producto){       

            $array[$producto['id']] = $producto['description']." (".$producto['code'].")";

        }

        

        $list= array();

        foreach ($array as $key => $value) {

            $list[$key] = $value;

        }

        return $list;

    }

    

    public function listaImpuestos(){            

        $repository = new ProductRepository();

        $result = $repository->getListaSelectImpuestos();

        

        $array = array();

        if ($result) {            

            foreach ($result as $key => $value) {

                $array[$key] = $value;

            }

            return $array;

        }

    }       

    

    public function populate($data) { 

        $tools = new Tools();

        if(isset($data['date']) && $tools->isValidaDateYYYMMDD($data['date'])){

            $data['date'] = $tools->setFormatDateToForm($data['date']);

        }

        

        if(isset($data['due_date']) && $tools->isValidaDateYYYMMDD($data['due_date'])){

            $data['due_date'] = $tools->setFormatDateToForm($data['due_date']);

        }        

        

        if(isset($data['status_approval']) && $data['status_approval'] !== '0'){

            $this->addOptionals('status_approval', array('checked'=>true));

        }

        

        /*Consultamos para obtener status_approval, porque cuando $data viene de $post, no tenemos status_approval

           y no lo ponemos hiden en el formulario, para evitar que lo puedan modificar desde el codigo fuente*/

        $purchaseRepo = new PurchaseRepository();

        $currentData =  $purchaseRepo->getById($this->getId());

        if($this->getActionController() == 'edit' && $currentData['status_approval'] != '0'){

            $repo = new StoreRepository();

            $storeData = $repo->getById($data['store_id']);



            $options[$storeData['id']] = $storeData['name'];            

            $this->setPropiedad('store_id', array('multiOptions'=>$options));            

        }

       

        

        parent::populate($data);

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