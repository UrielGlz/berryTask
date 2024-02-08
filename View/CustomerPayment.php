<section class="content-header">
    <h1><i class='fa-fw fa fa-dollar'></i> <?php echo $_translator->_getTranslation('Pago de cliente');if($action == 'edit'){echo " #".$form->getId();}?></small></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li><a href="<?php echo ROOT_HOST?>/Controller/CustomerPayment.php?action=list"><i></i><?php echo $_translator->_getTranslation('Lista de pagos de proveedores')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Pagos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <?php 
        if($action === 'edit'){?><a href="CustomerPayment.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pago')?></a><?php }?>  
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <?php echo $form->openForm();?>
    <div style="display: none"><?php
        echo $form->showActionController();
        echo $form->showId();
        echo $form->showElement('status');
        echo $form->showElement('monto_original');
        echo $form->showElement('suma_de_pagos');
        ?>
    </div>   
    <div class="card">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab_general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>
            <li role="presentation"><a href="#tab_attachments" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Adjuntos') ?></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="padding-top: 20px">
            <div role="tabpanel" class="tab-pane active" id="tab_general">
                <div class="col-md-5 col-xs-5">
                    <?php echo $form->showElement('customer')?>
                    <?php echo $form->showElement('amount')?>
                    <?php echo $form->showElement('date')?>                         
                </div>
                <div class="col-md-5 col-xs-5">
                    <?php echo $form->showElement('payment_method')?>
                    <?php echo $form->showElement('operation_num')?>  
                    <?php echo $form->showElement('notes')?>
                </div>        
                 <div class="col-md-2 col-xs-2 text-right">          
                    <?php 
                        $find_letters = array(',');

                        $labelMontoPago = trim($form->getValueElement('amount'));
                        if($labelMontoPago == ''){$labelMontoPago = '0.00';}
                        $existe = (str_replace($find_letters, '', $labelMontoPago) != $labelMontoPago);
                        if(!$existe){$labelMontoPago = number_format($labelMontoPago,2);}

                        $labelSumaPago = trim($form->getValueElement('suma_de_pagos'));               
                        if($labelSumaPago == ''){$labelSumaPago = 0;}              
                        $existe = (str_replace($find_letters, '', $labelSumaPago) != $labelSumaPago);
                        if(!$existe){$labelSumaPago = number_format($labelSumaPago,2);}
                    ?>
                    <h3 class="label_monto_pago" style="margin: 0px;">$<?php echo $labelMontoPago?></h3>
                    <i><?php echo $_translator->_getTranslation('Monto de pago')?></i>
                    <div class="m-t-3"></div>
                    <h3 class="label_suma_pagos" style="margin: 0px;">$<?php echo $labelSumaPago?></h3>
                    <i><?php echo $_translator->_getTranslation('Suma de pagos')?></i>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_attachments">
                <div class="col-xs-12 col-md-6">
                    <?php $form->showElement('attachments[]');?>
                    <?php echo $customerPayment->getListFiles($form->getId());?>  
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <hr/>
    <div class="table-responsive">
        <p><?php echo $_translator->_getTranslation('Facturas pendientes de pago') ?></p>

        <table id="pendingInvoices" class="table table-striped table-condensed font-size-12 datatable_whit_filter_column _hideSearch" style="width:100%">
            <thead>
                <th class='text-center'><?php echo $_translator->_getTranslation('Factura #')?></th>
                <th class='text-center'><?php echo $_translator->_getTranslation('Cliente')?></th>
                <th class='text-center'><?php echo $_translator->_getTranslation('OC de cliente')?></th>
                <th class='text-center'><?php echo $_translator->_getTranslation('Fecha')?></th>
                <th class='text-center'><?php echo $_translator->_getTranslation('Fecha pago')?></th>
                <th class='text-right'><?php echo $_translator->_getTranslation('Total')?></th>
                <th class='text-right'><?php echo $_translator->_getTranslation('Balance')?></th>
                <th class='text-center'><?php echo $_translator->_getTranslation('Agregar')?></th>
            </thead>
            <tfoot>
                <th class="filter"><?php echo $_translator->_getTranslation('Factura #')?></th>
                <th class="filter"><?php echo $_translator->_getTranslation('Cliente')?></th>                
                <th class="filter"><?php echo $_translator->_getTranslation('OC de cliente');?></th>
                <th class="filter"><?php echo $_translator->_getTranslation('Fecha')?></th>
                <th class="filter"><?php echo $_translator->_getTranslation('Fecha de pago')?></th>
                <th class="filter"><?php echo $_translator->_getTranslation('Total');?></th>   
                <th class="filter"><?php echo $_translator->_getTranslation('Balance')?></th>                                  
                <th></th>
            </tfoot>
            <tbody>
            <?php 
                #Status de pago en general                 
                if($form->getValueElement('status')=='1' || $form->getValueElement('status')==''){                      
                    $ajaxPago = new CustomerPaymentAjax();                    
                    $facturasPendientes = $ajaxPago->getListFacturasByCustomer(array('customer'=>$form->getValueElement('customer')));
                    echo $facturasPendientes['listFacturas'];              
                }
            ?>
            </tbody>
        </table>    
    </div>
    <div class="clear" ></div>
    <hr>    
    <div class="table-responsive">
        <p><?php echo $_translator->_getTranslation('Facturas seleccionadas para pago') ?></p>
        
        <table class="table table-striped table-condensed font-size-12">
        <thead>
        <th class="text-center"><?php echo $_translator->_getTranslation('Acciones')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Factura #')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Cliente')?></th>
        <th class='text-center'><?php echo $_translator->_getTranslation('OC de cliente')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Fecha')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Fecha pago')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Total')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Balance')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Pago')?></th>
        </thead>
        <tbody id="listFacturasAPagar">
        <?php if(isset($_listFacturas)){
                $entity = new InvoiceRepository();  
                foreach($_facturas as $factura => $value){
                    $result = $entity->getById($factura);
                    if($result){   ?>                    
                        <tr id="deleteInvoice_<?php echo $factura?>">
                        <td class="text-center">
                            <a onclick="deleteInvoiceFromPayment('<?php echo $factura?>')" class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></a>
                        </td>
                        <td class="text-center"><?php echo $result['invoice_num'] ?></td>
                        <td class="text-center"><?php echo $result['customerName'] ?></td>
                        <td class="text-center"><?php echo $result['customer_po'] ?></td>
                        <td class="text-center"><?php echo $result['formatedDate'] ?></td>
                        <td class="text-center"><?php echo $result['formatedDueDate'] ?></td>
                        <td class="text-right"><?php echo number_format($result['total'],2) ?></td>
                        <td class="text-right"><?php echo number_format($result['balance'],2); ?></td>                        
                        <td class="text-right col-md-2"><?php $form->showElement("pago[$factura]");?></td>                        
                        </tr>
                        <script>$('#addInvoice_<?php echo $factura?>').hide();</script> 

            <?php    }
                }
            }?>                       
        </tbody>
        </table>
        <div class="col-md-2 pull-right text-right">    
            <h3 class="label_monto_pago" style="margin: 0px;">$<?php echo $labelMontoPago;?></h3>
            <i><?php echo $_translator->_getTranslation('Monto de pago')?></i>
            <div class="m-t-3"></div>
            <h3 class="label_suma_pagos" style="margin: 0px;">$<?php echo $labelSumaPago?></h3>
            <i><?php echo $_translator->_getTranslation('Suma de pagos')?></i>
        </div>
    </div>
    
        <div class="col-md-2 col-xs-12 pull-right text-right"><?php $form->showElement('send');?></div>
    <?php echo $form->closeForm();?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<style>
    label.forma_de_pago {
        margin-top: -3px;
    }
</style>
<?php if(isset($_disabled) && $_disabled){?> <script>$('#listFacturasAPagar tr td a.btn-danger').hide();</script><?php } ?>
<script>
    $('#payment_method, #customer').select2();
    $("#date").datetimepicker({format: 'MM/DD/YYYY'});   

    $("#customer").on('select2:select', function () {
        getListFacturasCustomers();
        $("#listFacturasAPagar").html('');
    });    

    $('#amount').on('blur',function(){ 
        var monto = this.value;
        if(monto.trim() === ''){monto = '0';}
        $('#monto_original').val(_rawNumber(monto));
       
        setLabelMonto(monto);
    });

    $('#amount').on('change',function(){ 
        if($('#monto_original').val() === '' || $('#monto_original').val() === '0'){return null;}
        else{limpiarFacturasAPagarPorCambioMontoCustomer();}
    });
</script>