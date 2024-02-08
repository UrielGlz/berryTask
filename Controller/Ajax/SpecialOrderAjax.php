<?php
class SpecialOrderAjax extends SpecialOrderRepository {
    public $formProduct = null;
    public $formRequisition = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }
    
    public function setSpecialOrderDetailsWizard($options){
        $settings = new SettingsRepository();
        $specialOrderDetailsTempRepo = new SpecialOrderDetailsTempRepository();
        $details = array(
            '1'=>$options['pan'],
            '2'=>$options['relleno'],
            '3'=>$options['decorado'],
        );        
        
        if($options['idDetailTemp'] ==''){
            $multiple = $specialOrderDetailsTempRepo->getNextMultiple($options);
            foreach($details as $category => $idProduct){
                $producto = $this->getProductById($idProduct,$options['type']);
                 $data = array(
                    'token_form'=>$options['token_form'],
                    'idDetailTemp'=>$options['idDetailTemp'],
                    'type'=>$options['type'],
                    'id_product'=>$idProduct,
                    'quantity'=>1,
                    'price'=>$producto['price'],
                    'multiple'=>$multiple);     
               
                if($category == '3' && $idProduct == $settings->_get('id_product_for_special_decorated')){
                    $data['price'] = $options['precio'];
                }
                
                if($category == '1' && $options['number_of_cake'] !== '' && !is_null($options['number_of_cake'])){
                    $data['number_of_cake'] = $options['number_of_cake'];
                    $data['quantity'] = strlen($options['number_of_cake']);
                }

                $this->insertDetalle($data);            
            }          
            
        }else{            
            $currentData = $specialOrderDetailsTempRepo->getById($options['idDetailTemp']);
            $currentDetails = $specialOrderDetailsTempRepo->getByMultiple($currentData['multiple'], $options['token_form']);
            
            foreach($details as $category => $idProduct){
                $producto = $this->getProductById($idProduct,$options['type']);
                $currenDataDetail = $currentDetails[$category];
                
                $data = array(
                    'token_form'=>$options['token_form'],
                    'idDetailTemp'=>$currenDataDetail['id'],
                    'id_product'=>$idProduct,
                    'price'=>$producto['price'],
                    'number_of_cake'=>$options['number_of_cake'],);     

                if($category == '3' && $idProduct == $settings->_get('id_product_for_special_decorated')){
                    $data['price'] = $options['precio'];
                }
                
                if($category == '1' && $options['number_of_cake'] !== '' && !is_null($options['number_of_cake'])){
                    $data['number_of_cake'] = $options['number_of_cake'];
                    $data['quantity'] = strlen($options['number_of_cake']);
                }
                
                $this->insertDetalle($data);            
            }
        }              
        
        $requisitionDetails = $this->getRequisitionDetails($data['token_form']);
        $detalles = $this->listRequisitionDetails($requisitionDetails);
        $json = array(
                'response' => true,
                'requisitionDetails' => $detalles['requisitionDetails'],
                'grandTotal'=>$detalles['total'],
            );
            
            return $json;
       
    }

    public function setSpecialOrderDetails(array $options) {
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }
        
        $options = $data;
        $type = $data['type'];
        
        $producto = $this->getProductById($options['product'],$type);
        if($producto){
            $data = array(
                'token_form'=>$options['token_form'],
                'idDetailTemp'=>$options['idDetailTemp'],
                'type'=>$options['type'],
                'id_product'=>$producto['id'],
                'quantity'=>$options['quantity'],
                'price'=>$options['price'],
                'multiple'=>$options['multiple']);
            
            $this->insertDetalle($data);
            $requisitionDetails = $this->getRequisitionDetails($data['token_form']);
            $detalles = $this->listRequisitionDetails($requisitionDetails);

            $json = array(
                'response' => true,
                'requisitionDetails' => $detalles['requisitionDetails'],
                'grandTotal'=>$detalles['total'],
            );
            
            return $json;
        }else{
            $this->flashmessenger->addMessage(array('info'=>$this->_getTranslation('Producto no registrado.')));
            return $json = array(
                'response'=>null,
                'message'=>$this->flashmessenger->getMessageString());
        }
    }
    
    public function listRequisitionDetails($detalles){
        $settings = new SettingsRepository();
        $extraCategoryId = $settings->_get('id_category_for_extra_cakes');
        
        
        $listDetalles = "";
        $totalTotal = 0;
        
        foreach($detalles as $detalle){
            $total = $detalle['quantity'] * $detalle['price'];
            $totalTotal += $total;
            $forma_nombre = '';
            
            if($detalle['type']=='Line'){$forma_nombre = $detalle['product_name']."<br/>".$detalle['size_name'];}
            elseif($detalle['type']=='Special'){
                $forma_nombre = $detalle['shape_name']."<br/>".$detalle['size_name'];
                if($detalle['number_of_cake']!= '' && !is_null($detalle['number_of_cake'])){$forma_nombre .= ' [ '.$detalle['number_of_cake'].' ]';}
            }
            
            if($detalle['category'] == $extraCategoryId){$forma_nombre = "Extra<br/>".$detalle['description'];}
            
            $id = $detalle['id'];
            unset($detalle['id']);
            
            /*mandar idCategory del pan; idCategoru del relleno y idCategory del decorado*/
            $array = json_encode(array(
                'idDetailTemp'=>$id,
                'type'=>$detalle['type'],
                'size'=>$detalle['size_id'],
                'shape'=>$detalle['shape_id'],
                'product'=>$detalle['product'],
                'quantity'=>$detalle['quantity'],
                'price'=>$detalle['price'],
                'number_of_cake'=>$detalle['number_of_cake'],
                'pan_id'=>$detalle['pan_id'],
                'relleno_id'=>$detalle['relleno_id'],
                'decorado_id'=>$detalle['decorado_id'])); 
            
            $listDetalles .= "<tr><td class='text-left'>";
            
            if($detalle['type'] == 'Special' && $detalle['category'] != $extraCategoryId){
                $listDetalles .=" <a class='btn btn-sm btn-default' onclick='setDetailSpecialOrderToEditWizard($array);'><i class='fa fa-pencil'></i></a>";
            }else{
                $listDetalles .=" <a class='btn btn-sm btn-default' onclick='setDetailSpecialOrderToEdit($array);'><i class='fa fa-pencil'></i></a> ";
            }            
                
            if($detalle['id_detail'] !== null){
                $listDetalles .=" <a class='btn btn-sm btn-danger' onclick='confirmAction(\"delete_special_order_detail\",function(){deleteSpecialOrderDetail($id);})' data-type='{$detalle['type']}'><i class='fa fa-trash'></i></a>";
            }else{
                $listDetalles .=" <a class='btn btn-sm btn-danger' onclick='deleteSpecialOrderDetail($id)' data-type='{$detalle['type']}'><i class='fa fa-trash'></i></a>";                    
            }
                            
            $listDetalles .= "</td>
                <td class='text-center'>".number_format($detalle['quantity'],2)."</td>
                <td class='text-center'>".$detalle['type']."</td>
                <td class='text-center'>".$forma_nombre."</td>
                <td class='text-center'>".$detalle['pan_name']."</td>
                <td class='text-center'>".$detalle['relleno_name']."</td>
                <td class='text-center'>".$detalle['decorado_name']."</td>
                <td class='text-right'>".number_format($detalle['price'],2)."</td>
                <td class='text-right'>".number_format($total,2)."</td>
                </tr>";
        }
        
        return array('requisitionDetails'=>$listDetalles,
                     'total'=>$totalTotal,
            );
    }
    
    public function getListRequisitionDetails($tokenForm){
        $requisitionDetails = $this->getRequisitionDetails($tokenForm);
        $detalles = $this->listRequisitionDetails($requisitionDetails);

            $json = array(
                'response' => true,
                'requisitionDetails' => $detalles['requisitionDetails'],
                'grandTotal'=>$detalles['total']
            ); 
            
            return $json;
    }
    
    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new SpecialOrderDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        $response = true;
        $msj = 'Producto eliminado correctamente.';
        if($currentData['type'] == 'Special' && !is_null($currentData['multiple']) && trim($currentData['multiple']) != ''){
            $rs = $repository->deleteByMultiple($currentData['multiple'], $currentData['token_form']);         
            
        }else{
            /*Puede ser type = Line o type = Special; pero si es Special multiple debe ser null o vacio*/
            $rs = $repository->delete($id,$currentData['token_form']);
        }
        
        if($rs == null){
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }      
        
        $requisitionDetails = $this->getRequisitionDetails($currentData['token_form']);
        $detalles = $this->listRequisitionDetails($requisitionDetails);

        $json = array(
                'response' => true,
                'requisitionDetails' => $detalles['requisitionDetails'],
                'grandTotal'=>$detalles['total']
            ); 
            return $json;
    }
    
    public function setShapesBySize($options){        
        $slicesRepo = new SliceRepository();
        $result = $slicesRepo->getShapesBySize($options['size']);   
        
        $list = "<option value=''>".$this->_getTranslation('Seleccionar una opcion...')."</value>";
        $listWizard = '';
        if($result){
            foreach($result as $key => $value){
                $list .= "<option value='$key'>".utf8_encode($value)."</option>";
            }

            foreach($result as $key => $value){
                $listWizard .= "<div class='col-md-4 col-xs-12 text-center _box_option_wizard _shape' data-propierty='shape' data-shapeid='{$key}'>".utf8_encode($value)."</div>";
            }           
        }      
        
        return array(
            'response'=>true,
            'listShapes'=>$list,
            'listShapesWizard'=>$listWizard
        );
    }
    
    public function setSliceFlavor($options){
        $categoria = $options['category'];
        $size = $options['size'];
        $shape = $options['shape'];
        
        $repoSlices = new SliceRepository();
        $result = $repoSlices->getListSelectSlices($categoria,$size,$shape);   
        
        $list = "<option value=''>".$this->_getTranslation('Seleccionar una opcion...')."</value>";
        if($result){
            foreach($result as $key => $value){
                $list .= "<option value='$key'>".utf8_encode($value)."</option>";
            }
        }      
        
        return array(
            'response'=>true,
            'listSlices'=>$list
        );
    }
    
    public function setSlicesWizard($options){
        $size = $options['size'];
        $shape = $options['shape'];
        
        $listPan = '';
        $listRelleno = '';
        $listDecorado = '';
        
        $repoSlices = new SliceRepository();
        $result = $repoSlices->getListSlicesWizard($size,$shape);      
       
        if($result){ 
            foreach($result as $key => $slice){
                switch($slice['category']){
                    case '1':
                        $listPan .= "<div class='col-md-4 col-xs-12 text-center _box_option_wizard _pan' data-toggle='tooltip' data-placement='top' title='".htmlentities(nl2br($slice['comments']),ENT_QUOTES)."' data-html='true' data-propierty='pan' data-panid='{$slice['id']}'>".htmlentities(nl2br($slice['flavor']),ENT_QUOTES)."</div>";
                        break;
                    case '2':
                        $listRelleno .= "<div class='col-md-4 col-xs-12 text-center _box_option_wizard _relleno' data-toggle='tooltip' data-placement='top' title='".htmlentities(nl2br($slice['comments']),ENT_QUOTES)."' data-html='true' data-propierty='relleno' data-rellenoid='{$slice['id']}'>".htmlentities(nl2br($slice['flavor']),ENT_QUOTES)."</div>";
                        break;
                    case '3':
                        $listDecorado .= "<div class='col-md-12 col-xs-12 text-center _box_option_wizard _decorado' data-toggle='tooltip' data-placement='top' title='".htmlentities(nl2br($slice['comments']),ENT_QUOTES)."' data-html='true' data-propierty='decorado' data-decoradoid='{$slice['id']}'>".htmlentities(nl2br($slice['flavor']),ENT_QUOTES)."</div>";
                        break;
                }                
            }
        }      
        
        return array(
            'response'=>true,
            'listPan'=>$listPan,
            'listRelleno'=>$listRelleno,
            'listDecorado'=>$listDecorado
        );
    }
    
    public function getListProducts($options){
        $list = "<option value=''>Seleccionar una opcion...</value>";
        if($options['type'] == 'Line'){
            $settings = new SettingsRepository();
            $repo = new ProductRepository();
            
            //$result = $repo->getListSelectProducts($settings->_get('id_category_for_vitrina_cakes'),$options['size']);
            $result = $repo->getListSelectProducts($settings->_get('id_category_for_vitrina_cakes'),null);
            foreach($result as $key => $value){
                $list .= "<option value='$key'>".htmlentities($value,ENT_QUOTES)."</option>";
            }
            
        }elseif($options['type'] == 'Special'){
            return $this->getListExtras();
        }
        
        
        return array(
            'response'=>true,
            'listProducts'=>$list
        );
    }
    
    public function getListExtras(){
        $list = "<option value=''>Seleccionar una opcion...</value>";
        
        $settings = new SettingsRepository();
        $repo = new SliceRepository();
        $result = $repo->getListSelectSlices($settings->_get('id_category_for_extra_cakes'));
        foreach($result as $key => $value){
            $list .= "<option value='$key'>". htmlentities($value,ENT_QUOTES)."</option>";
        }
        
        
        /* IF type = special se regresa $list vacia porque esta se carga al momento de seleccionar categoria */
        return array(
            'response'=>true,
            'listProducts'=>$list
        );
    }
    
    
    public function addPaymentToSpecialReq($options){
        $idReq = $options['idReq'];
        $repoSpecialReq = new SpecialRequisitionEntity();
        $dataSpecialReq = $repoSpecialReq->getById($idReq);
        $repoSpecialReq->setOptions($dataSpecialReq);
        
        $ventaRepo = new VentaRepository();
        $ventaRepo->crearTablaDetallesForUser();       
        $ventaRepo->crearTablaCobrosForUser();
        
        $ventaRepoTemp = new VentaDetallesTempRepository();
        $ventaRepoTemp->clearDetalles();
        
        $impuestosRepo = new ImpuestosRepository();
        $idImpuesto = 3; #si se cambia impuesto aqui, debemos cambiarlo en posAjax dentro de setVentaDetalles
        $dataImpuestos = $impuestosRepo->getById($idImpuesto);
        
        $ventaRepoTemp->save(array(
            'idProducto'=>'0',
            'descripcion'=>'Pago de orden especial: #'.$dataSpecialReq['req_number'], # Siemmpre debe llevar signo '#' porque lo uso en function isSpecialRequisition en PosAjax.php
            'cantidad'=>'1',
            'precio'=>$repoSpecialReq->getBalance(),
            'descuento'=>'0',
            'impuestos'=>$idImpuesto,
            'impuestos_tasa'=>$dataImpuestos['tasa_cuota'],
            'descuentoOrden'=>'0',
        ));
        
        return array(
            'response'=>true
        );       
    }
    
    public function changeStatus($options){
        $idReq = $options['id'];
        $status = $options['status'];
        $field = $options['field'];
        
        $rs = parent::query("UPDATE special_orders SET {$field} = '{$status}' WHERE id = '$idReq'");
    
        if($field == 'status_production'){
            $reqNumber = $options['req_number'];
            if($rs && $status == '1'){
                $btnStatus = "<span class='btn btn-sm btn-default' onclick='changeStatusForSR(this)' data-id='$idReq' data-reqnumber='$reqNumber' data-statusfield='status_production' data-status='2' data-statusname='Terminada'><i class='fa fa-star'></i> Pendiente</span>";
            }

            if($rs && $status == '2'){
                $btnStatus = "<span href='#' class='btn btn-sm btn-primary' onclick='changeStatusForSR(this)' data-id='$idReq' data-reqnumber='$reqNumber' data-statusfield='status_production' data-status='1' data-statusname='Pendiente'><i class='fa fa-star'></i> Terminada</span>";
            }   

            return array(
                'response'=>true,
                'btnStatus'=>$btnStatus
            );
        }
        
        if($field == 'status_delivery'){
            if($rs && $status == '2'){
                //$this->descontarInventarioBySRId($idReq);
                $string = "<i class='fa fa-check fa-2x text-olive'></i>";
            }elseif($rs && $status == '1'){
                //$this->agregarInventarioBySRId($idReq);
                 $string = "";
            }
            
            return array(
                'response'=>true,
                'string'=>$string
            );
        }       
        
        return array(
            'response'=>true
        );
    }
    
    public function getProductPrice($options){
        $type = $options['type'];
        $product = $options['product'];
        
        $comments = null;
        switch($type){
            case 'Line':
                $repo = new ProductRepository();
                $data = $repo->getById($product);
                $price = $data['sale_price'];
                $comments = $data['comments'];
                break;
            
            case 'Special':
                $repo = new SliceRepository;
                $data = $repo->getById($product);
                 $price = $data['price'];
                break;            
        }
        
        return array(
            'response'=>true,
            'price'=>$price,
            'comments'=>$comments
        );
    }
    
    public function deleteImage($options){
        $repo = new EntityRepository();
        $rs = $repo->delete($options['id'], 'images');
        
        if($rs){
            return array(
                'response'=>true
            );
        }
    }
    
    public function allowEditSpecialOrder($options){ 
        $entityRepository = new EntityRepository();
        $entityRepository->update($options['idSpecialOrder'], array('allow_edit'=>'1'), 'special_orders');
        return array(
            'response'=>true,
        );
        
    }
    
    public function saveCustomer($options){
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }

        $entity = new CustomerRepository();
        $entity->setOptions($data);        
        
        if(trim($data['name']) == ''){
            $this->flashmessenger->addMessage(array('info'=>'Lo sentimos. El campo nombre es obligatorio.'));
            return array(
                'response'=>false,
                'message'=>$this->flashmessenger->getMessageString()
              );
        }
        
        if($data['action']=='insert'){
            $result = $entity->save($entity->getOptions());
            $lastCliente = $entity->getLastInsertId();
            
        }elseif($data['action']=='edit'){
            unset($data['token_form'],$data['action'],$data['id']);
            $result = $entity->update($options['customer'],$data);
            $lastCliente = $options['customer'];
        }        

        if($result){
            $this->flashmessenger->addMessage(array('success'=>'Excelente!! El cliente se registro exitosamente.'));
        }else{
            $this->flashmessenger->addMessage(array('danger'=>'Opss. Algo salio mal al intetar registrar el cliente.'));
        }        
       
        return array(
            'response'=>true,
            'message'=>$this->flashmessenger->getMessageString(),
            'customerList'=>$this->getListCustomers($lastCliente)
              );
    }  
    
    public function getListCustomers($selectedCustomer = null){
        $entity = new CustomerRepository();
        $listClientes= $entity->getListSelectCustomers();
        
        $list = "<option value=''>".$this->_getTranslation('Seleccionar una opcion...')."</option>";
        foreach($listClientes as $key => $value){
            $selected = "";
            if($key == $selectedCustomer){$selected = "selected";}
            $list .= "<option value='$key' $selected >$value</option>";
        }
        return $list;
    }
    
        
    public function getCustomerData($options){
        $customerId = $options['id'];
        
        $repo = new CustomerRepository();
        $data = $repo->getById($customerId);
        $customerData = array(
            'name'=>$data['name'],
            'phone'=>$data['phone'],
            'email'=>$data['email'],
            'address'=>$data['address'],
            'city'=>$data['city'],
            'zipcode'=>$data['zipcode'],            
        );
        return array(
            'response'=>true,
            'customerData'=>$customerData
        );
    } 
    
     public function getImages($options) {
        $idReq = $options['idReq'];
        $entity = new SpecialOrderRepository();
        $entity->setOptions($entity->getById($idReq));
        $stringImages = $entity->showImagesForProductionAndBakedPlan();
        
        if($stringImages == null){$stringImages = "<h3 class='text-center'>No existen imagenes para esta Orden Especial</h3>";}
         
        return array(
            'response'=>true,
            'images'=>$stringImages
        );
    }
    
     public function getTotalSales($options){
        $salesRecord = new SpecialOrderRepository();
        $salesData = $salesRecord->getSpecialOrderByDateRange($options['start'],$options['end'],'store_id');
        
        $string = '';
        $totalSales = 0;
        $totalOrders = 0;
        $chartDataSales = array();
        $chartDataOrders = array();
        if($salesData){
            foreach($salesData as $sale){
                $totalSales += $sale['total_sales'];
                $totalOrders += $sale['total_orders'];
                $chartDataSales[$sale['store_name']] = number_format($sale['total_sales'],2,'.','');
                $chartDataOrders[$sale['store_name']] = number_format($sale['total_orders'],2,'.','');
                $string .= "<tr>";
                $string .= "<td class='text-center'>{$sale['store_name']}</td>";
                $string .= "<td class='text-center'>".number_format($sale['total_orders'],2)."</td>";
                $string .= "<td class='text-right'>$".number_format($sale['total_sales'],2)."</td>";
                $string .= "</tr>";
            }

            return array(
                'response'=>true,
                'salesData'=>$string,
                'caption'=>$this->_getTranslation('Pedidos')." ".$this->_getTranslation('de')." ".$options['start']." ".$this->_getTranslation('a')." ".$options['end'],
                'totalSales'=>number_format($totalSales,2,'.',''),
                'totalOrders'=>number_format($totalOrders,2,'.',''),
                'chartDataSales'=>$chartDataSales,
                'chartDataOrders'=>$chartDataOrders
            );
        }   
        
        return array('response'=>false);
    }
    
    public function getFeedback($options){       
        
        return array(
            'response'=>true,
            'feedback'=> $this->_getFeedback($options),
        );
    }
    
    public function saveFeedback($options){
        $this->_saveFeedback($options);
        
        return array(
            'response'=>true
        );
    }
    
     /*EMAILING BOL*/
    public function getDataSpecialOrder($options){
        $specialRequisitionData = $this->getById($options['id']);
        $this->setOptions($specialRequisitionData);
        
        $msg = $this->_getTranslation('Hola');
        $msg .= ' '.$specialRequisitionData['customer_name'].",\n";
        $msg .= $this->_getTranslation("Puedes revisar tu Pedido Especial en el archivo adjunto.");
        
        return array(
            'response'=>true,
            'to'=>$specialRequisitionData['email'],
            'subject'=>$this->_getTranslation("Pedido Especial # {$specialRequisitionData['req_number']}"),
            'messageMail'=>$msg
        );        
    }
    
    public function emailingSpecialOrder($options){
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }
        
        $shippingRepo = new SpecialOrderRepository();
        $shippingRepo->setOptions($shippingRepo->getById($data['id_special_requisition']));
        $shippingRepo->setId($data['id_special_requisition']);
        $specialOrderPdf = $shippingRepo->crearPDF();
        
        $company = new CompanyRepository();
        $company->setOptions($company->getById(1));
       
        try{
            $emailer = new Emailer();
            $emailer->sendEmail(array(
                'to'=>$data['to'],
                'cc'=>$data['cc'],
                'subject'=>$data['subject'],
                'from_title'=>$company->getName(),
                'message'=>$data['message'],
                'attachment'=>$specialOrderPdf
            ));
            
            $this->flashmessenger->addMessage(array('success'=>'EL Pedido especial se envio correctamente.'));
            return array(
                'response'=>true,
                'msg'=>$this->flashmessenger->getMessageString()
            );
        } catch (Exception $ex) {
            $this->flashmessenger->addMsg(array('danger'=>'Oops =(. Algo salio mal al tratar de enviar el Pedido especial. Intenta nuevamente'));
            return array(
                'response'=>null,
                'msg'=>$this->flashmessenger->getMessageString()
            );
        }        
    }  
    /*EN EMAILING BOL*/
}