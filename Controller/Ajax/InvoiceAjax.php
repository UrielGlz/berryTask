<?php
class InvoiceAjax extends InvoiceRepository
{
    public $status_invoice = null;
    public function __construct()
    {
        parent::__construct();
    }

    public function getResponse($request, $options)
    {
        return $this->$request($options);
    }   

    public function setInvoiceDetalles(array $options){
        $msg = null;
        if(isset($options['options'])){
            $data = array();
            foreach($options['options'] as $row){
                $data[$row['name']] = $row['value'];
            }        
        }elseif(isset($options['from_assigned_product'])){
            $data = $options['from_assigned_product'];
        }
                    
        $this->status_invoice = $data['status'];
        if($data['type'] == 'product'){
            $productRepo = new ProductRepository();
            $producto = $productRepo->getById($data['id_product']);
            $data['descripcion'] = $producto['description'];     
        }                               

        if(!$this->insertDetalle($data)){
            return array(
                'response'=>false,
                'msg'=>$this->flashmessenger->getRawMessage()
            );
        }                
            
        $shippingRequestDetalles = $this->getInvoiceDetalles($data['token_form']);
        $detalles = $this->listInvoiceDetalles($shippingRequestDetalles);

        $json = array(
            'response' => true,
            'invoiceDetails' => $detalles['listDetalles'],
            'totalItems'=>number_format($detalles['totalItems'],2),
            'total_importe'=>$detalles['total_importe'],
            'descuento_items'=>$detalles['descuento_items'],
            'descuento_general'=>$detalles['descuento_general'],
            'total_descuentos'=>number_format($detalles['total_descuentos'],2),
            'total_subtotal'=>number_format($detalles['total_subtotal'],2),
            'total_impuestos'=>$detalles['total_impuestos'],
            'total'=>$detalles['total']
        );
        
        if ($msg) {
            $json['msg'] = $this->flashmessenger->getMessageString();
        }

        return $json;

    }
    
    public function listInvoiceDetalles($detalles)
    {
        $listDetalles = "";       
        $tokenForm = null;
        $cantidadItems = 0;
        $total_importe = 0;
        $descuentos_item = 0;
        $descuentos_general = 0;
        $total_descuentos = 0;
        $total_subtotal = 0;
        $total_impuestos = 0;
        $total_impuestos_string = '';
        $taxes_applied = array();
        $total = 0;
        $propierties = array('varietyName','sizeName','colorName','packingName','brandName','qualityName','madurityName');
        
        if($detalles){
            $element = key($detalles);
            $shippingRequestData = $this->getById($detalles[$element]['id_invoice']);
            
            foreach($detalles as $detalle){
                $descriptionDetails = '';
                $tokenForm = $detalle['token_form'];                
                $id = $detalle['id'];
                $detalle['quantity_per_pallets'] = $detalle['quantity'];
                $discountGeneral = '';
                if ($detalle['discount_general_type'] == 'percent') {
                    $discountGeneral = $detalle['discount_general'] . "%";
                }
                    
                unset($detalle['id'],$detalle['discount_general'],$detalle['discount_general_type']); 
                
                $cantidadItems += $detalle['quantity'];
                $total_importe += $detalle['price'] * $detalle['quantity'];
                $descuentos_item += $detalle['discount_amount'];
                $descuentos_general += $detalle['discount_general_amount'];
                $total_descuentos += $detalle['discount_amount'] + $detalle['discount_general_amount'];
                $total_subtotal += $detalle['amount'];
                $total_impuestos + $detalle['taxes_amount'];
                $total += $detalle['total'];
                
                if($detalle['type'] == 'produce'){                    
                    $array = json_encode($detalle);  
                    $btnEdit = "<a class='btn btn-sm btn-default' onclick='setDetailProduceInvoiceToEdit($array,this);' data-mixto='0' data-controller='Invoice'><i class='fa fa-pencil'></i></a>";
                    
                    $descriptionDetails .= "<br/><small>";
                    foreach($propierties as $key => $propierty){
                        if ($detalle[$propierty] != '') {
                            $descriptionDetails .= "<span style='text-transform: uppercase;font-weight:bold'>" . str_replace('Name', '', $propierty) . "</span>:" . $detalle[$propierty] . " ";
                        }
                    }
                    $descriptionDetails .= "</small>";
                }elseif($detalle['type'] == 'product'){
                    $detalle['idDetailTemp'] = $detalle['idDetalleTemp'];
                    $detalle['product'] = $detalle['descripcion'];                   
                    unset($detalle['idDetalleTemp']);
                    $array = json_encode($detalle);   
                    $btnEdit = "<a class='btn btn-sm btn-default' onclick='setDetailInvoiceToEdit($array);'><i class='fa fa-pencil'></i></a>";
                    $descriptionDetails .= "<br/><small>".$detalle['description_details']."</smal>";
                    
                    if(isset($taxes_applied[$detalle['taxName']], $taxes_applied)){
                        $taxes_applied[$detalle['taxName']] += $detalle['taxes_amount'];
                    }else{
                        $taxes_applied[$detalle['taxName']] = $detalle['taxes_amount'];
                    }    
                }              

                $listDetalles .= "<tr>";
                $listDetalles .= "<td class='text-center' style='white-space:nowrap'>";       
                $listDetalles .= $btnEdit;
                if ($this->status_invoice == '1' || $this->status_invoice == null) {
                    $listDetalles .= "<a class='btn btn-sm btn-danger' onclick='deleteInvoiceDetalles($id,this);'><i class='fa fa-trash'></i></a>";
                }
                
                $listDetalles .= "<td class='text-left'>".$detalle['descripcion'].$descriptionDetails."</td>"
                 ."<td class='text-right'>".$detalle['quantity']."</td>"
                 ."<td class='text-right'>".$detalle['price']."</td>"
                 ."<td class='text-right'>".number_format($detalle['discount'],2)."</td>"
                 ."<td class='text-right'>".$discountGeneral.number_format($detalle['discount_general_amount'],2)."</td>"
                 ."<td class='text-right'>".$detalle['taxName'].' '.number_format($detalle['taxes_amount'],2)."</td>"                     
                 ."<td class='text-right'>".number_format($detalle['total'],2)."</td>"
                 ."</tr>";
            }
        }      
        
        if(count($taxes_applied)>0){
            $total_impuestos_string = '';
            foreach($taxes_applied as $tax => $amount){
                $total_impuestos_string .= "<tr>"
                        . "<th colspan='2' class='text-right' style='border:0px'>$tax</th>"
                        . "<td class='text-right' style='border: 0px;border-bottom: 1px solid #eee'>".number_format($amount,2)."</td>"
                        . "</tr>";
            }
        }        
        
        return array(
            'listDetalles' => $listDetalles,
                     'totalItems'=>$cantidadItems,
                     'total_importe'=>$total_importe,
                     'descuento_items'=>$descuentos_item,
                     'descuento_general'=>$descuentos_general,
                     'total_descuentos'=>$total_descuentos,
                     'total_subtotal'=>$total_subtotal,
                     'total_impuestos'=>$total_impuestos_string,
            'total' => $total
        );
    }   
    
    public function getListInvoiceDetalles($tokenForm, $status = null)
    {
        if ($status) {
            $this->status_invoice = $status;
        }
        $shippingRequestDetalles = $this->getInvoiceDetalles($tokenForm);
        $detalles = $this->listInvoiceDetalles($shippingRequestDetalles);

            $json = array(
                'response' => true,
                'invoiceDetails' => $detalles['listDetalles'],
                'totalItems'=>number_format($detalles['totalItems'],2),
                'total_importe'=>$detalles['total_importe'],
                'descuento_items'=>$detalles['descuento_items'],
                'descuento_general'=>$detalles['descuento_general'],
                'total_descuentos'=>number_format($detalles['total_descuentos'],2),
                'total_subtotal'=>number_format($detalles['total_subtotal'],2),
                'total_impuestos'=>$detalles['total_impuestos'],
                'total'=>$detalles['total']
            );       
            

            return $json;
    }

    public function deleteInvoiceDetalles(array $options)
    {
        $id = $options['id'];
        $repository = new InvoiceDetailsTempRepository();
        $data = $repository->getById($id);
        $tokenForm = $data['token_form'];
        
        $result = $repository->delete($id);
        
        if($result){
            $response = true;
            $msj = 'Producto eliminado correctamentes.';
        }else{
            $response = null;
            $msj = "No se pudo eliminar producto. Intente nuevamente.";
        }
        
        $shippingRequestDetalles = $this->getInvoiceDetalles($tokenForm);
        $detalles = $this->listInvoiceDetalles($shippingRequestDetalles);
        
        $json = array(
                'response' => $response,
                'message'=>$this->_getTranslation($msj),
                'invoiceDetails'=>$detalles['listDetalles'],
                'totalItems'=>number_format($detalles['totalItems'],2),
                'total_importe'=>$detalles['total_importe'],
                'descuento_items'=>$detalles['descuento_items'],
                'descuento_general'=>$detalles['descuento_general'],
                'total_descuentos'=>number_format($detalles['total_descuentos'],2),
                'total_subtotal'=>number_format($detalles['total_subtotal'],2),
                'total_impuestos'=>$detalles['total_impuestos'],
                'total'=>$detalles['total']
            );
            return $json;
    }
    
    public function updateSalesOrderPricetByProductKeyPrice($options)
    {
        $manifestDetailsTempRepo = new InvoiceDetailsTempRepository();
        $manifestDetailsTempRepo->updatePriceByProductKeyPrice($options);
        
        return $this->getListInvoiceDetalles($options['tokenForm'],$options['status']);     
    }
    
    public function applyGeneralDiscountToItems($options)
    {
        $purchaseGoodAndServiceDetailTemp = new InvoiceDetailsTempRepository();
        $rs = $purchaseGoodAndServiceDetailTemp->applyGeneralDiscountToItems($options);
        
        if($rs){
            return $this->getListInvoiceDetalles($options['token_form']);
        }
    }
    
    public function getDefaultDataProduct($options)
    {
        $repo = new ProductRepository();
        $data = $repo->getById($options['product']);      
        
        return array(
            'response'=>true,
            'price'=>$data['sale_price'],
            'taxes'=>$data['taxes'],
        );
    }
    
    
     public function getCustomerMethodPayment($options){
        $customer = $options['customer'];
        $date = $options['date'];
        $dueDate = $date;
        
        $repoCustomer = new CustomerRepository();
        $dataCustomer = $repoCustomer->getById($customer);
        
        $paymentTermsRepo = new PaymentTermsRepository();
        $paymentTermsData = $paymentTermsRepo->getById($dataCustomer['payment_terms']);
        
        if($date !== '' && !is_null($date)){
            $tools = new Tools();
            $date = $tools->setFormatDateToDB($date);
            
            $dueDate = $this->getDueDate($date, $paymentTermsData['days']);
            $dueDate = $tools->setFormatDateToForm($dueDate);
        }  
        
        return array(
            'response'=>true,
            'payment_terms'=>$paymentTermsData['id'],
            'due_date'=>$dueDate
        );        
    }
    
        public function createInvoiceFromReceiving(array $options){
        $receivingRepo = new ReceivingStoreRequestRepository();
        $repoSales = new InvoiceRepository();
        $customerRepo = new CustomerRepository();
        $entity = new EntityRepository();
        
        $customerData = $customerRepo->getById($options['customer_id']);        
        $dataQuotation = $receivingRepo->getById($options['receiving_id']);//Datos de la recibo
        $idQuotation = $dataQuotation['id'];
        
        $repoSales->setOptions($dataQuotation);
        $salesData = $repoSales->getOptions();
        
        $salesData['receiving_id'] = $options['receiving_id'];
        $salesData['date'] = date('Y-m-d');
        $salesData['invoice_num'] = $dataQuotation['num_shipment'];   /*num_shipment es que se muestras en lista de recibos como si fuera el numero de recibo*/     
        $salesData['id_customer'] = $options['customer_id'];      
        $salesData['billed_to_store'] = $dataQuotation['store_id'];  
        $salesData['payment_terms_id'] = $customerData['payment_terms'];
       
        $salesData['status'] = 1; //Este status es el de la Nota de Venta, puede ser 1->Abierta, 2->Cerrada
        $salesData['total'] = 0;
        $salesData['payments'] = 0;
        $salesData['status_payment'] = 1;
        unset($salesData['attachments']);
        
        $entity->startTransaction();
        $rs = $entity->save($salesData, 'invoices');
        if($rs){
            $idSale = $entity->getInsertId();
            $dataDetails = $receivingRepo->getReceivingStoreRequestDetailsSaved($idQuotation);//Productos del recibo
            
            $productRepo = new ProductRepository();
            $invoiceDetailRepo = new InvoiceDetailsTempRepository();
            
            $totalInvoice = 0;
            foreach ($dataDetails as $detail){     
                $productData = $productRepo->getById($detail['id_product']);
                
                
                $detail['type'] = 'product';
                $detail['id_product'] = $productData['id'];
                $detail['descripcion'] = $detail['description'];                        
                $detail['quantity'] = $detail['received'];
                $detail['price'] = $productData['sale_price'];
                $detail['taxes'] = $productData['taxes'];                     
                $detail['not_save'] = true;           
               
                $invoiceDetail = $invoiceDetailRepo->save($detail);      
                $totalInvoice += (float)$invoiceDetail['total'];
                
                $invoiceDetail['id_invoice'] = $idSale;    
                unset($invoiceDetail['token_form']); 
                
                if(!$entity->save($invoiceDetail, 'invoice_details')){
                    $this->flashmessenger->addMessage(array('danger'=>'Algo salio mal al tratar de generar la Nota de venta. Intente nuevamente.'));
                    return array(
                         'respnse'=>null
                    );
                }       
            }
            
            /*Actualizar total de factura*/
            $entity->query("UPDATE invoices SET total = '$totalInvoice' WHERE id = '$idSale'");
            
            $entity->commit();
            $entity->update($options['receiving_id'], array('invoice_id'=>$idSale), "receiving_store_requests");            

            $msg = $this->_getTranslation('Se genero factura exitosamente. Factura #');
            $msg .=" <a href='Invoice.php?action=edit&id={$idSale}'>{$salesData['invoice_num']}</a>";
            $this->flashmessenger->addMessage(array('success'=>$msg));            
            
            return array(
                'response'=>true
            );
        }
    }
}
