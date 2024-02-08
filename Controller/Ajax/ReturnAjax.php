<?php
class ReturnAjax extends ReturnRepository {    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }

    public function setReturnDetails(array $options) {
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }
        
        $producto = $this->getProductById($data['id_product']);
        if($producto){
            $data['description'] = $producto['description'];         
            
            $this->insertDetalle($data);
            $compraDetalles = $this->getReturnDetails($data['token_form']);
            $detalles = $this->listReturnDetalles($compraDetalles);

            $json = array(
                'response' => true,
                'returnDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
            );
            
            return $json;
        }else{
            $this->flashmessenger->addMessage(array('danger'=>$this->_getTranslation('Producto no registrado.')));
            return $json = array(
                'response'=>null,
                'message'=>$this->flashmessenger->getMessageString());
        }
    }
    
    public function listReturnDetalles($detalles){      
        $tools = new Tools();
        $listDetalles = "";
        $cantidadItems = 0;
        
        foreach($detalles as $detalle){
            $id = $detalle['id'];
            
            unset($detalle['id']);                      
            $array = json_encode($detalle);
            $cantidadItems += $detalle['quantity'];          
            
            $listDetalles .= "<tr>  
                <td class='text-left'>                       
                    <a class='btn btn-sm btn-primary' onclick='setDetailReturnToEdit($array);'><i class='fa fa-pencil'></i></a>
                    <a class='btn btn-sm btn-danger' onclick='deleteReturnDetalles($id);'><i class='fa fa-minus'></i></a>
                </td>
                <td class='text-center'>".$detalle['code']."</td>
                <td>".$detalle['description']."</td>                    
                <td class='text-center'>".$detalle['presentation']."</td>                    
                <td class='text-center'>".$detalle['brand']."</td>                     
                <td class='text-right'>".number_format($detalle['quantity'],2)."</td>
                <td class='text-center'>".$detalle['location_name']."</td>   
                </tr>";
        }        
        
        return array('listDetails'=>$listDetalles,
                     'totalItems'=>$cantidadItems,
            );
    }
    
    public function getListReturnDetails($tokenForm){
        $compraDetalles = $this->getReturnDetails($tokenForm);
        $detalles = $this->listReturnDetalles($compraDetalles);
            
            $json = array(
                'response' => true,
                'returnDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
            ); 
            
            return $json;
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new ReturnDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        if($repository->delete($id)){
            $response = true;
            $msj = 'Producto eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $compraDetalles = $this->getReturnDetails($currentData['token_form']);
        $detalles = $this->listReturnDetalles($compraDetalles);

       $json = array(
                'response' => true,
                'returnDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
            );
            return $json;
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
                    'label'=>$item['description']." ".$item['presentation'],
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
            'id_product'=>$data['id']
        );
    }
    
    public function getLocations($options){
        /*Si no existe el producto, las locaciones se obtienen del registro del producto en la tabla productos*/
        $repoSupplieLocation = new LocationRepository();
        $productRepo = new ProductRepository();
        $productData = $productRepo->getById($options['id_product']);
        $locations = $repoSupplieLocation->getListSelectLocations($productData['location'],$options['store_id']);
        
        /*Si no existe locaciones asignadas a la sucursal seleccionada y que esten seteadas al producto, se regresa la locacion por default de la sucursal.*/
        if($locations == null){
            $storeRepo = new StoreRepository();
            $storeData = $storeRepo->getById($options['store_id']);
            $locations = $repoSupplieLocation->getListSelectLocations($storeData['default_location']);
        }
        
        $listLocations = "";           
        if($locations && count($locations) > 0){
            foreach($locations as $idLocation => $locationName){
                $listLocations .= "<option value='$idLocation'>$locationName</option>";
            }
        }else{
            $listLocations .= "<option value='0'>".$this->_getTranslation('Seleccionar una opcion...')."</value>"; 
        }        
        
        return array(
            'response'=>true,
            'location'=>$listLocations,
            'numLocations'=>count($locations)
        );
    }
}