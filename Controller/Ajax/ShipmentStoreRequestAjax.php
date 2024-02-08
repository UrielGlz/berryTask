<?php
class ShipmentStoreRequestAjax extends ShipmentStoreRequestRepository {
    public $formShipment = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }

    public function setShipmentDetails(array $options) {
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
                'token_form'=>$options['token_form'],
                'idDetailTemp'=>$options['idDetailTemp'],
                'id_product'=>$producto['id'],
                'quantity'=>$options['quantity']); /*Para cuenta de inventario*/

            $this->insertDetalle($data);
            $shipmentDetails = $this->getShipmentDetails($data['token_form']);
            $detalles = $this->listShipmentDetails($shipmentDetails);

            $json = array(
                'response' => true,
                'shipmentDetails' => $detalles['shipmentDetails'],
                'requiredItems'=> number_format($detalles['requiredItems'],2),
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
    
    public function listShipmentDetails($detalles){
        $disabled = null;
        $listDetalles = "";
        $cantidadItems = 0;
        $receivedItems = 0;
        $requiredItems = 0;
        $focusIndex = 0;
        
        foreach($detalles as $detalle){
            $focusIndex++;
            $cantidadItems += $detalle['quantity'];
            $receivedItems += $detalle['received'];
            
            $quantity = $detalle['quantity'];
            $received = $detalle['received'];
            $required = $detalle['real_stock_in_store'];
            if($required > 0){$requiredItems += $required;}            
            
            $id = $detalle['id'];
            unset($detalle['id'],$detalle['token_form'],$detalle['min_stock'],$detalle['real_stock_in_store'],$detalle['received'],$detalle['category']);            
            if(is_null($detalle['size'])){$detalle['size'] = '';}
            
            $array = json_encode($detalle);
            $listDetalles .= "<tr>  
                <td class='text-center'>                       
                    <a class='btn btn-sm btn-default' onclick='setDetailShipmentToEdit($array);'><i class='fa fa-pencil'></i></a>";
                    /*<a class='btn btn-sm btn-danger $disabled' onclick='deleteShipmentDetails($id);'><i class='fa fa-trash'></i></a>*/
            $listDetalles .= "</td>
                <td class='text-center'>".$detalle['code']."</td>
                <td>".$detalle['description']."</td>
                <td>".$detalle['size']."</td>";
            
            /*<td class='text-right'>".number_format($detalle['min_stock'],2)."</td> */
            /*<td class='text-right'>".number_format($detalle['real_stock_in_store'],2)."</td> */
            
            $input = "<input type='text' value='{$quantity}' class='text-right form-control _shippedQuantity' data-iddetailtemp='{$id}' data-productid='{$detalle['product']}' data-focusindex='{$focusIndex}' />";
            
            $listDetalles .= "
                <td class='text-right'>".number_format($required,2)."</td>
                <td class='text-right'>".$input."</td> 
                <td class='text-right'>".number_format($received,2)."</td>
                </tr>";
        }
        
        return array('shipmentDetails'=>$listDetalles,
                    'totalItems'=>$cantidadItems,
                    'receivedItems'=>$receivedItems,
                    'requiredItems'=>$requiredItems
            );
    }
    
    public function getListShipmentDetails($tokenForm = null){
        $shipmentDetails = $this->getShipmentDetails($tokenForm);
        $detalles = $this->listShipmentDetails($shipmentDetails);

            $json = array(
                'response' => true,
                'shipmentDetails' => $detalles['shipmentDetails'],
                'requiredItems'=> number_format($detalles['requiredItems'],2),
                'totalItems'=>number_format($detalles['totalItems'],2),
                'receivedItems'=>number_format($detalles['receivedItems'],2)
            ); 
            
            return $json;
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new ShipmentStoreRequestDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        if($repository->delete($id)){
            $response = true;
            $msj = 'Producto eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $shipmentDetails = $this->getShipmentDetails($currentData['token_form']);
        $detalles = $this->listShipmentDetails($shipmentDetails);

        $json = array(
                'response' => true,
                'shipmentDetails' => $detalles['shipmentDetails'],
                'requiredItems'=> number_format($detalles['requiredItems'],2),
                'totalItems'=>number_format($detalles['totalItems'],2),
                'receivedItems'=>number_format($detalles['receivedItems'],2)
            ); 
            return $json;
    }
}