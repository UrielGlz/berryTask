<?php
class StoreRequestAjax extends StoreRequestRepository {    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }   
    
    public function listStoreRequestDetalles($detalles){
        $listDetalles = "";
        $totalProductos = 0;
        $tokenForm = null;
        
        foreach($detalles as $detalle){
            $tokenForm = $detalle['token_form'];
            $totalProductos += $detalle['quantity'];          
            
            $comments = null;
            if(trim($detalle['comments']) != ''){$comments = "<br/><p style='margin-left:10px;color:navy'>".$detalle['comments']."</p>";}
            
            $listDetalles .= "<tr>"             
                ."<td class='col-md-3 text-left'>".$detalle['description']."$comments</td>"
                ."<td class='col-md-1 text-center'>".$detalle['last_inventory']."</td>"
                ."<td class='col-md-1 text-center'>".$detalle['pending_to_receive']."</td>"
                ."<td class='col-md-1 text-center'><input name='quantity_{$detalle['id']}' type='text' value='{$detalle['quantity']}' class='_storeRequestQuantity text-right' /></td>"
                //."<td class='text-center'><input name='received_{$detalle['id']}' type='text' value='{$detalle['received']}' class='_storeRequestQuantity text-right' /></td>"           
                ."</tr>";
        }
        
        return array('listDetalles'=>$listDetalles,
                     'totalProductos'=>$totalProductos);
    }
    
    public function getListStoreRequestDetalles($tokenForm){
        $manifestDetalles = $this->getStoreRequestDetalles($tokenForm);
        $detalles = $this->listStoreRequestDetalles($manifestDetalles);

            $json = array(
                'response' => true,
                'storeRequestDetalles' => $detalles['listDetalles'],
                'totalProductos'=>$detalles['totalProductos']
            );       
            return $json;
    }
    
    public function setStoreRequestDetallesForArea($options){
        $repo = new StoreRequestDetailsTempRepository();
        $repo->setStoreRequestDetallesForArea($options);
        
        return $this->getListStoreRequestDetalles($options['token_form']);
    }
    
    public function thereIsOrderForToday($options){
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }   
        
        $storeRequest = $this->_thereIsOrderForToday($data);
        if($storeRequest != null){
            $this->flashmessenger->addMessage(array('danger'=>'Ya existe un pedido iniciado para este dia y para esta area.'));
            return array(
                'response'=>true,
                'storeRequest'=>$storeRequest,
                'msg'=>$this->flashmessenger->getRawMessage()
            );
        }
        
        return array(
                'response'=>null
            );
    }
    
    public function updateStoreRequestQty($options){
        if(!isset($options['storage_request_quantity'])){return array('response'=>true);}
        
        $data = array();
        foreach($options['storage_request_quantity'] as $row){
            $data[$row['name']] = $row['value'];
        }   
        
        $dataUpdate = array(); 
        foreach($data as $key => $value){
            $key = explode('_',$key);
            $dataUpdate[$key[1]][$key[0]] = $value;
        }
       
        $repository = new StoreRequestDetailsTempRepository();    
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
    
    public function blockUnblockOrder($options){
        $rs = $this->updateInProcess($options);
        
        if($rs){
            if($options['inProcess'] == '0'){ 
                $newInProcessTxt = "&nbsp;&nbsp;&nbsp;".$this->_getTranslation("Bloquear")."&nbsp;&nbsp;&nbsp;";
                $inProcessColumn = "";                
            }
            if($options['inProcess'] == '1'){ 
                $newInProcessTxt = $this->_getTranslation("Desbloquear");
                $inProcessColumn = "<i class='fa fa-check fa-2x text-olive'></i>";
            }
            
             return array(
                'response'=>true,
                'newInProcessText'=> html_entity_decode($newInProcessTxt),
                'inProcessColumn'=>$inProcessColumn
            );
        }
        
        return array(
            'response'=>null
        );               
    }
    
    public function generateShipment($options) {
        $login = new Login();      
        /*
        if($login->getRole() == '2'){
            $receivingRepo = new ReceivingStoreRequestRepository();
            $repo = new ShipmentStoreRequestRepository();
            $dataExist = $repo->existShipmentForStoreRequest($options['id_store_request']);

             if($dataExist){            
                $receivingData = $receivingRepo->getByNumShipment($dataExist['num_shipment']);            

                if($receivingData == null){
                    $this->_generateReceiving($dataExist['id']);
                    $receivingData = $receivingRepo->getByNumShipment($dataExist['num_shipment']);
                }

                return array(
                    'response'=>true,
                    'automatic_shipment'=>true,
                    'receiving_id'=>$receivingData['id']
                );                  
            }      
            
            $this->flashmessenger->addMessage(array('danger'=>'No se ha generado envio. No se puede recibir aun.'));
            return array(
                'response'=>true,
                'msg'=>$this->flashmessenger->getMessageString()
            );      
        }        
        */
        
        return parent::_generateShipment($options['id_store_request']);
    }
}