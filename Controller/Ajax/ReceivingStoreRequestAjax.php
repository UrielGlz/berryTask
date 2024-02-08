<?php
class ReceivingStoreRequestAjax extends ReceivingStoreRequestRepository {
    public $formReceiving = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }
    
    public function setReceivingDetails(array $options) {
        $byCode = null;
        if(isset($options['byCode'])){$byCode = $options['byCode'];}        
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }

        $options = $data;
        #Cuando se da enter en el campo producto del Shipment, el producto se busca por codigo, 
        #el valor del codigo ingresado se almacena en idProducto
        if($byCode){            
            $result = $this->getProductoByCode($options['idProduct']);
            if($result){
                $options['idProduct'] = $result['id'];
            }            
        }
        
        $producto = $this->getProductById($options['idProduct']);
        if($producto){
            $data = array(
                'idDetailTemp'=>$options['idDetailTemp'],
                'id_product'=>$producto['id'],
                'received'=>$options['received']);

            $this->insertDetalle($data);
            $receivingDetails = $this->getReceivingStoreRequestDetails();
            $detalles = $this->listReceivingDetails($receivingDetails);

            $json = array(
                'response' => true,
                'receivingDetails' => $detalles['receivingDetails'],
                'totalPedido'=>number_format($detalles['totalPedido'],2),
                'totalItems'=>number_format($detalles['totalItems'],2),
                'receivedItems'=>number_format($detalles['receivedItems'],2)
            );
            
            return $json;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>$this->_getTranslation('Producto no registrado.')));
            return $json = array(
                'response'=>null,
                'message'=>$this->flashmessenger->getRawMessage());
        }
    }
    
    public function listReceivingDetails($detalles,$status = null){
        $disabled = null;
        $listDetalles = "";
        $cantidadPedido = 0;
        $cantidadItems = 0;
        $receivedItems = 0;
        
        if($status == '4'){$disabled = 'disabled';}
        
        foreach($detalles as $detalle){
            $cantidadPedido += $detalle['real_stock_in_store'];
            $cantidadItems += $detalle['quantity'];
            $receivedItems += $detalle['received'];
            
            $quantity = $detalle['quantity'];
            $received = $detalle['received'];
            $required = $detalle['real_stock_in_store'];
            
            $id = $detalle['id'];
            unset($detalle['id'],$detalle['real_stock_in_store'],$detalle['quantity'],$detalle['id'],$detalle['category'],$detalle['id_detail'],$detalle['id_receiving']);
            if(is_null($detalle['size'])){$detalle['size'] = '';}
            
            $array = json_encode($detalle);
            $listDetalles .= "<tr>  
                <td class='text-left'>                       
                    <a class='btn btn-sm btn-default $disabled' onclick='setDetailReceivingStoreRequestToEdit($array);'><i class='fa fa-pencil'></i></a>
                    <a class='btn btn-sm btn-danger $disabled' onclick='deleteReceivingStoreRequestDetails($id);'><i class='fa fa-trash'></i></a>
                </td>
                <td class='text-center'>".$detalle['code']."</td>
                <td>".$detalle['description']."</td>
                <td>".$detalle['size']."</td>
                <td class='text-right'>".number_format($required,2)."</td> 
                <td class='text-right'>".number_format($quantity,2)."</td>
                <td class='text-center'><input name='received_{$id}' type='text' value='{$received}' class='_receivedQuantity text-right' /></td>
                </tr>";
        }
        
        return array('receivingDetails'=>$listDetalles,
                    'totalPedido'=>$cantidadPedido,
                    'totalItems'=>$cantidadItems,
                    'receivedItems'=>$receivedItems
            );
    }
    
    public function getListReceivingDetails($status = null){
        $receivingDetails = $this->getReceivingStoreRequestDetails();
        $detalles = $this->listReceivingDetails($receivingDetails,$status);

            $json = array(
                'response' => true,
                'receivingDetails' => $detalles['receivingDetails'],
                'totalPedido'=>number_format($detalles['totalPedido'],2),
                'totalItems'=>number_format($detalles['totalItems'],2),
                'receivedItems'=>number_format($detalles['receivedItems'],2)
            ); 
            
            return $json;
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $login = new Login();
        $repository = new ReceivingStoreRequestDetailsTempRepository();
        
        if($repository->updateString(array('received'=>'0')," id = '$id' ",'receiving_store_request_details_'.$login->getId())){
            $response = true;
            $msj = 'Producto eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $receivingDetails = $this->getReceivingStoreRequestDetails();
        $detalles = $this->listReceivingDetails($receivingDetails);

        $json = array(
                'response' => true,
                'receivingDetails' => $detalles['receivingDetails'],
                'totalPedido'=>number_format($detalles['totalPedido'],2),
                'totalItems'=>number_format($detalles['totalItems'],2),
                'receivedItems'=>number_format($detalles['receivedItems'],2)
            ); 
            return $json;
    }
    
    public function getShipmentData($options){
        $existReceiving = false;
        $idReceiving = null;
        $numShipment = $options['numShipment'];
        
        $repoReceiving = new ReceivingStoreRequestRepository();
        $data = $repoReceiving->getByNumShipment($numShipment);        
        
        $msg = '';
        if($data){
            $existReceiving = true;
            $idReceiving = $data['id'];
        }else{         
            $shipmentStoreRequestRepo = new ShipmentStoreRequestRepository();
            $rs = $shipmentStoreRequestRepo->getDataShipment($numShipment);
            if($rs['response'] == true){
                $idReceiving = $repoReceiving->saveUsingDataFromMainServer($rs);
                if($idReceiving){
                    $existReceiving = true;
                }else{
                    $msg = $this->flashmessenger->getMessageString();
                }                
            }else{
                $this->flashmessenger->addMessage(array('danger'=>$rs['msg']));
                $msg = $this->flashmessenger->getMessageString();
            }                  
        }           
                        
        return array(
            'response'=>true,
            'existReceiving'=>$existReceiving,
            'idReceiving'=>$idReceiving,
            'message'=>$msg
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
                    'label'=>"[".$item['code']."] ".$item['description']." ".$item['size'],
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
    
    public function updateReceivingStoreRequestQty($options){
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
       
        $repository = new ReceivingStoreRequestDetailsTempRepository();    
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
    
     public function allowEditReceivingStoreRequest($options){ 
        $entityRepository = new EntityRepository();
        $entityRepository->update($options['receiving_store_request_id'], array('allow_edit'=>'1'), 'receiving_store_requests');
        return array(
            'response'=>true,
        );        
    }
}