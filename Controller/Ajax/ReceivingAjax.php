<?php
class ReceivingAjax extends ReceivingRepository {
    public $formProduct = null;
    public $formReceiving = null;
    public $formVendor = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }

    public function setReceivingDetails(array $options) {
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }
        
        $producto = $this->getProductById($data['id_product']);
        if($producto){
            $data['description'] = $producto['description'];         

            $this->insertDetalle($data);
            $compraDetalles = $this->getReceivingDetails($data['token_form']);
            $detalles = $this->listCompraDetalles($compraDetalles);

            $json = array(
                'response' => true,
                'receivingDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2)
            );
            
            return $json;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>$this->_getTranslation('Producto no registrado.')));
            return $json = array(
                'response'=>null,
                'message'=>$this->flashmessenger->getMessageString());
        }
    }
    
    public function listCompraDetalles($detalles){      
        $tools = new Tools();
        $listDetalles = "";
        $cantidadItems = 0;
        
        $locationsRepo = new LocationRepository();
        
        if(count($detalles) > 0){
            $storeId = $detalles[0]['store_id_of_document'];
            $storeRepo = new StoreRepository();
            $storeData = $storeRepo->getById($storeId);
            
            foreach($detalles as $detalle){
                $disabled = null;
                if($detalle['added'] == '0'){$disabled = true;}

                $detalle['expiration_date'] = $tools->setFormatDateToForm($detalle['expiration_date']);
                $id = $detalle['id'];
                $discountGeneral = $detalle['discount_general'];

                unset($detalle['id'],$detalle['discount_general'],$detalle['discount_general_type']);                      
                $array = json_encode($detalle);
                $cantidadItems += $detalle['quantity'];       

                if($storeData['with_locations'] == '1'){
                    $listLocations = $locationsRepo->getListSelectLocations($detalle['ids_locations'],$detalle['store_id_of_document']);
                }else{
                    $listLocations = $locationsRepo->getListSelectLocations($storeData['default_location']);
                }

                $list = "";
                if($listLocations){
                    foreach($listLocations as $key => $value){
                        $selected = "";
                        if($key == $detalle['location']){$selected = "selected";}
                        $list .= "<option value='$key' $selected >$value</option>";
                    }
                }

                $select = "<select id='location_{$id}' name='location_{$id}' class='_receivedQuantity' style='width:100%;height:20px'>";
                $select .= $list;
                $select .= "</select>";

                $listDetalles .= "<tr>                  
                    <td class='text-left'>                       
                        <a class='btn btn-sm btn-primary' onclick='setDetailReceivingToEdit($array);'><i class='fa fa-pencil'></i></a> ";

                if($disabled == true){
                    $listDetalles .= " <a class='btn btn-sm btn-danger disabled'><i class='fa fa-minus'></i></a>";
                }else{
                    $listDetalles .= " <a class='btn btn-sm btn-danger' onclick='deleteDetallesReceiving($id);'><i class='fa fa-minus'></i></a>";
                }

                $listDetalles .="</td>";

                $listDetalles .= "<td class='text-center'>".$detalle['code']."</td>
                    <td>".$detalle['description']."</td> 
                    <td class='text-center'>".$detalle['presentation']."</td> 
                    <td class='text-center'>".$detalle['brand']."</td> 
                    <td class='text-right'>".number_format($detalle['quantity'],2)."</td>  
                    <td class='text-center'><input id='received_{$id}' name='received_{$id}' type='text' value='{$detalle['received']}' class='_receivedQuantity' style='width:100%;text-align:right' /></td>
                    <td class='text-center'>$select</td>";


                $listDetalles .="</tr>";
            } 
        }      
        
        //var_dump($listDetalles);
        return array('listDetails'=>$listDetalles,
                     'totalItems'=>$cantidadItems,
            );
    }
    
    public function getListReceivingDetails($tokenForm){
        $compraDetalles = $this->getReceivingDetails($tokenForm);
        $detalles = $this->listCompraDetalles($compraDetalles);

            $json = array(
                'response' => true,
                'receivingDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
            ); 
            
            return $json;
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new ReceivingDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        if($repository->delete($id)){
            $response = true;
            $msj = 'Producto eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $compraDetalles = $this->getReceivingDetails($currentData['token_form']);
        $detalles = $this->listCompraDetalles($compraDetalles);

       $json = array(
                'response' => true,
                'receivingDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
            );
            return $json;
    }
    
    public function getDefaultDataProduct($options){
        $repo = new ProductRepository();
        $data = $repo->getById($options['product']);
        
        $locations = array();
        if(trim($data['location']!= '') && $data['location']!== null ){
            $repoProductLocation = new LocationRepository();
            $locations = $repoProductLocation->getListSelectLocations($data['location']);
        } 
        
        $listLocations = "";           
        if($locations && count($locations) > 0){
            foreach($locations as $idLocation => $locationName){
                $listLocations .= "<option value='$idLocation'>$locationName</option>";
            }
        }        
        
        return array(
            'response'=>true,
            'cost'=>$data['cost'],
            'taxes'=>$data['taxes'],
            'taxes_included'=>$data['taxes_included'],
            'location'=>$listLocations,
            'numLocations'=>count($locations)
        );
    }
    
    public function getListProduct($options){
        $repository = new ProductRepository();
        $item = $options['item'];
        
        $items = $repository->getProductsLike($item);
        $array = array();
        if($items){
            foreach($items as $item){
                $array[] = array(
                    'value'=>$item['id'],
                    'label'=>$item['description']." ".$item['presentation']." (".$item['code'].")",
                    'precio'=>$item['sale_price'],
                    'descuento'=>$item['discount'],
                    'impuestos'=>$item['taxes']
                );
            }
        }
        return array(
            'response'=>true,
            'products'=>$array
        );   
    }
    
    public function getProductByCode($options){
        $repo = new ProductRepository();
        $data = $repo->getByCode($options['code']);
        
        return array(
            'response'=>true,
            'idProduct'=>$data['id']
        );
    }
    
    public function getPurchaseDetailsToReceive($options){
        $reference = $options['document_reference'];
        $reference = explode('-', $reference);
        $type = $reference[0];
        $id = $reference[1];
        $purchaseDetails = null;
        
        if($type == 'purchase'){
            $purchaseRepo = new PurchaseRepository();
            $data = $purchaseRepo->getById($id);
            $storeId = $data['store_id'];
            
            $dataPurchase = array(
                'date'=>$data['formatedDate'],
                'vendor'=>$data['vendorName'],
                'reference'=>$data['reference'],
                'lot'=>$data['lot'],
                'date_label'=>'Fecha de compra',
                'vendor_label'=>'Proveedor',
                'reference_label'=>'Factura #',
                'lot_label'=>'Lote'
            );
            
            if(isset($options['_onSelect'])){
                $receivingDetailsTempRepo =  new ReceivingDetailsTempRepository();
                $receivingDetailsTempRepo->setReceivingDetailsByIdPurchase($id, $options['token_form']);
                $purchaseDetails = $this->getListReceivingDetails($options['token_form']);
                $purchaseDetails = $purchaseDetails['receivingDetails'];
            }
            
        }elseif($type == 'transfer'){
            $transferRepo = new TransferRepository();
            $data = $transferRepo->getById($id);
            $storeId = $data['to_store_id'];
            
            $dataPurchase = array(
                'date'=>$data['formatedDate'],
                'vendor'=>$data['fromStoreName'],
                'reference'=>$data['toStoreName'],
                'lot'=>$data['requested_by'],
                'date_label'=>'Fecha de traspaso',
                'vendor_label'=>'Desde',                
                'reference_label'=>'Para',
                'lot_label'=>'Requerido por'
            );
            
            if(isset($options['_onSelect'])){
                $receivingDetailsTempRepo =  new ReceivingDetailsTempRepository();
                $receivingDetailsTempRepo->setReceivingDetailsByIdTransfer($id, $options['token_form']);
                $purchaseDetails = $this->getListReceivingDetails($options['token_form']);
                $purchaseDetails = $purchaseDetails['receivingDetails'];
            }
            
        }       
        
        return array(
            'response'=>true,
            'purchaseData'=>$dataPurchase,
            'purchaseDetails'=>$purchaseDetails,
            'storeIdOfDocument'=>$storeId
        );
    }
    
     public function updateReceiviedQuantity($options){
        if(!isset($options['received_quantity'])){return array('response'=>true);}
        $data = array();
        foreach($options['received_quantity'] as $row){
            $data[$row['name']] = $row['value'];
        }   
       
        $dataUpdate = array(); 
        foreach($data as $key => $value){
            $key = explode('_',$key);
            $dataUpdate[$key[1]][$key[0]] = $value;
        }
        
        $repository = new ReceivingDetailsTempRepository();   
        foreach ($dataUpdate as $id => $values){
            $stringSet = null;
            foreach($values as $field => $value){
                if($value == '_NULL' || is_null($value) || trim($value)==''){
                    $stringSet .= "$field = NULL,";
                }else{
                    $stringSet .= "$field = '$value',";
                }  
            }
            
            $stringSet = trim($stringSet, ',');   
            parent::query("UPDATE ".$repository->getTableName()." SET $stringSet WHERE id = '$id'");
        }           
        
        return array(
            'response'=>true
        );
    }
}