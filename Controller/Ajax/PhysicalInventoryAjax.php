<?php
class PhysicalInventoryAjax extends PhysicalInventoryRepository {    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }   
    
    public function getListPhysicalInventoryDetalles($tokenForm,$area){
        $manifestDetalles = $this->getPhysicalInventoryDetalles($tokenForm,$area);
        $detalles = $this->listPhysicalInventoryDetalles($manifestDetalles);

            $json = array(
                'response' => true,
                'physicalInventoryDetalles' => $detalles['listDetalles'],
                'totalProductos'=>$detalles['totalProductos']
            );       
            return $json;
    }
    
    public function listPhysicalInventoryDetalles($detalles){
        $listDetalles = "";
        $totalProductos = 0;
        $tokenForm = null;
        
        foreach($detalles as $detalle){
            $tokenForm = $detalle['token_form'];
            $totalProductos += $detalle['quantity'];          
            
            $listDetalles .= "<tr>"             
                ."<td class='text-center'>".$detalle['description']."</td>"
                ."<td class='text-center'>".$detalle['sizeName']."</td>"
                ."<td class='text-center'><input name='quantity_{$detalle['id']}' type='text' value='{$detalle['quantity']}' class='_physicalInventoryQuantity text-right' /></td>"                
                ."</tr>";
        }
        
        return array('listDetalles'=>$listDetalles,
                     'totalProductos'=>$totalProductos);
    }  
    
    public function thereIsPhysicalInventoryForToday($options){
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }   
        
        $storeRequest = $this->_thereIsPhysicalInventoryForToday($data);
        if($storeRequest != null){
            $this->flashmessenger->addMessage(array('danger'=>'Ya existe un inventario fisico para este dia.'));
            return array(
                'response'=>true,
                'physicalInventory'=>$storeRequest,
                'msg'=>$this->flashmessenger->getRawMessage()
            );
        }
        
        return array(
                'response'=>null
            );
    }
    
    public function updatePhysicalInventoryQty($options){
        if(!isset($options['physical_inventory_quantity'])){return array('response'=>true);}
        
        $data = array();
        foreach($options['physical_inventory_quantity'] as $row){
            $data[$row['name']] = $row['value'];
        }   
        
        $dataUpdate = array(); 
        foreach($data as $key => $value){
            $key = explode('_',$key);
            $dataUpdate[$key[1]][$key[0]] = $value;
        }
       
        $repository = new PhysicalInventoryDetailsTempRepository();    
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