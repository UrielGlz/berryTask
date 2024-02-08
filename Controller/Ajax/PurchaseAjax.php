<?php
class PurchaseAjax extends PurchaseRepository {
    public $formProduct = null;
    public $formPurchase = null;
    public $formVendor = null;
    
    public function __construct() {
        parent::__construct();
    }

    public function getResponse($request, $options) {
        return $this->$request($options);
    }

    public function setPurchaseDetails(array $options) {
        $data = array();
        foreach($options['options'] as $row){
            $data[$row['name']] = $row['value'];
        }
        
        $producto = $this->getProductById($data['id_product']);
        if($producto){
            $data['description'] = $producto['description'];         
            
            $this->insertDetalle($data);
            $compraDetalles = $this->getPurchaseDetails($data['token_form']);
            $detalles = $this->listCompraDetalles($compraDetalles);

            $json = array(
                'response' => true,
                'purchaseDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
                'total_importe'=>number_format($detalles['total_importe'],2),
                'total_descuentos'=>number_format($detalles['total_descuentos'],2),
                'total_subtotal'=>number_format($detalles['total_subtotal'],2),
                'total_impuestos'=>$detalles['total_impuestos'],
                'total'=>$detalles['total']
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
        $total_importe = 0;
        $total_descuentos = 0;
        $total_subtotal = 0;
        $total_impuestos = 0;
        $total_impuestos_string = '';
        $taxes_applied = array();
        $total = 0;
        
        foreach($detalles as $detalle){
            $detalle['expiration_date'] = $tools->setFormatDateToForm($detalle['expiration_date']);
            $id = $detalle['id'];
            $discountGeneral = $detalle['discount_general'];
            
            unset($detalle['id'],$detalle['discount_general'],$detalle['discount_general_type']);                      
            $array = json_encode($detalle);
            $cantidadItems += $detalle['quantity'];
            $total_importe += $detalle['cost'] * $detalle['quantity'];
            $total_descuentos += $detalle['discount_amount'] + $detalle['discount_general_amount'];
            $total_subtotal += $detalle['amount'];
            $total_impuestos + $detalle['taxes_amount'];
            $total += $detalle['total'];
            
            if(isset($taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']], $taxes_applied)){
                $taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']] += $detalle['taxes_amount'];
            }else{
                $taxes_applied[$detalle['taxName'].' '.$detalle['taxes_rate']] = $detalle['taxes_amount'];
            }            
            
            $listDetalles .= "<tr>  
                <td class='text-left'>                       
                    <a class='btn btn-sm btn-primary' onclick='setDetailPurchaseToEdit($array);'><i class='fa fa-pencil'></i></a>
                    <a class='btn btn-sm btn-danger' onclick='deleteDetalles($id);'><i class='fa fa-minus'></i></a>
                </td>
                <td class='text-center'>".$detalle['code']."</td>
                <td>".$detalle['description']."</td>                    
                <td class='text-right'>".number_format($detalle['quantity'],2)."</td>
                <td class='text-right'>".number_format($detalle['cost'],2)."</td>
                <td class='text-right'>".number_format($detalle['discount'],2)."</td>
                <td class='text-right'>".$discountGeneral.'% '.number_format($detalle['discount_general_amount'],2)."</td>
                <td class='text-right'>".number_format($detalle['amount'],2)."</td>
                <td class='text-right'>".$detalle['taxName'].' '.$detalle['taxes_rate'].'% '.number_format($detalle['taxes_amount'],2)."</td>                       
                <td class='text-right'>".number_format($detalle['total'],2)."</td>  
                </tr>";
        }
        
        if(count($taxes_applied)>0){
            $total_impuestos_string = '';
            foreach($taxes_applied as $tax => $amount){
                $total_impuestos_string .= "<tr>"
                        . "<th colspan='9' class='text-right'>$tax%</th>"
                        . "<th id='total_impuestos' class='text-right'>".number_format($amount,2)."</th>"
                        . "</tr>";
            }
        }        
        
        return array('listDetails'=>$listDetalles,
                     'totalItems'=>$cantidadItems,
                     'total_importe'=>$total_importe,
                     'total_descuentos'=>$total_descuentos,
                     'total_subtotal'=>$total_subtotal,
                     'total_impuestos'=>$total_impuestos_string,
                     'total'=>$total
            );
    }
    
    public function getListPurchaseDetails($tokenForm){
        $compraDetalles = $this->getPurchaseDetails($tokenForm);
        $detalles = $this->listCompraDetalles($compraDetalles);
            
            $json = array(
                'response' => true,
                'purchaseDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
                'total_importe'=>number_format($detalles['total_importe'],2),
                'total_descuentos'=>number_format($detalles['total_descuentos'],2),
                'total_subtotal'=>number_format($detalles['total_subtotal'],2),
                'total_impuestos'=>$detalles['total_impuestos'],
                'total'=>$detalles['total']
            ); 
            
            return $json;
    }
    
    public function calculateGralDiscount($options){
        $repo = new PurchaseDetailsTempRepository();
        $rs = $repo->setGeneralDiscount($options['discount']);
        
        if($rs){
             return $this->getListPurchaseDetails($options['token_form']);
        }else{
            #null == no existen registro para aplicar descuentos
            return array(
                'reponse'=>false
            );
        }     
    }

    public function deleteDetalles(array $options){
        $id = $options['id'];
        $repository = new PurchaseDetailsTempRepository();
        $currentData = $repository->getById($id);
        
        if($repository->delete($id)){
            $response = true;
            $msj = 'Producto eliminado correctamente.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $compraDetalles = $this->getPurchaseDetails($currentData['token_form']);
        $detalles = $this->listCompraDetalles($compraDetalles);

       $json = array(
                'response' => true,
                'purchaseDetails' => $detalles['listDetails'],
                'totalItems'=>number_format($detalles['totalItems'],2),
                'total_importe'=>number_format($detalles['total_importe'],2),
                'total_descuentos'=>number_format($detalles['total_descuentos'],2),
                'total_subtotal'=>number_format($detalles['total_subtotal'],2),
                'total_impuestos'=>$detalles['total_impuestos'],
                'total'=>$detalles['total']
            );
            return $json;
    }
    
    public function getDefaultDataProduct($options){
        $repo = new ProductRepository();
        $data = $repo->getById($options['product']);      
        
        return array(
            'response'=>true,
            'cost'=>$data['cost'],
            'taxes'=>$data['taxes'],
            'taxes_included'=>$data['taxes_included'],
        );
    }
    
    public function getVendorMethodPayment($options){
        $vendor = $options['vendor'];
        $date = $options['date'];
        $dueDate = $date;
        
        $repoVendor = new VendorRepository();
        $dataVendor = $repoVendor->getById($vendor);
        
        if($date !== '' && !is_null($date)){
            $tools = new Tools();
            $date = $tools->setFormatDateToDB($date);
            
            if($dataVendor['payment_method']=='2'){
                $dueDate = $this->getDueDate($date, $dataVendor['credit_days']);
                $dueDate = $tools->setFormatDateToForm($dueDate);
            }
        }  
        
        return array(
            'response'=>true,
            'method_payment'=>$dataVendor['payment_method'],
            'credit_days'=>$dataVendor['credit_days'],
            'due_date'=>$dueDate
        );        
    }
    
    public function setDueDate($options) {
       $date = $options['date'];
       $creditDays = $options['credit_days'];
       
       $tools = new Tools();
       $date = $tools->setFormatDateToDB($date);
       
       $dueDate = parent::getDueDate($date, $creditDays);       
       
       $dueDate = $tools->setFormatDateToForm($dueDate);
       
       return array(
            'response'=>true,
            'due_date'=>$dueDate
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
    
    public function approvePurchase($options){
        if($this->setApprovalStatusByApprover($options)){
            $updatedData = $this->getById($options['purchase_id']);
            $msg = $this->_getTranslation('Compra #');
            $msg .= $options['purchase_id'].'. ';
            $msg .= $this->_getTranslation('Status de aprobacion puesto en:');
            $this->flashmessenger->addMessage(array('success'=>$msg.' '.$updatedData['statusApprovalName']));
            return array(
                'response'=>true,
            );
        }
    }
}