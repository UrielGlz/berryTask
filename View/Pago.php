<section class="content-header">
    <h1><i class='fa-fw fa fa-dollar'></i> <?php echo $_translator->_getTranslation('Pagos');?></small></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li><a href="<?php echo ROOT_HOST?>/Controller/Payment.php?action=list"><i></i><?php echo $_translator->_getTranslation('Lista de pagos')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Pagos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <?php 
        if($action === 'edit'){?><a href="Payment.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pago')?></a><?php }?>  
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
    <div class="col-xs-12 col-md-12">
        <div class="col-md-5 col-xs-5">
            <?php echo $form->showElement('fecha')?>       
            <?php echo $form->showElement('forma_de_pago')?>
            <?php echo $form->showElement('num_operacion')?>                    
        </div>
        <div class="col-md-5 col-xs-5">
            <?php echo $form->showElement('monto')?>
            <?php echo $form->showElement('proveedor')?>
            <?php echo $form->showElement('notas')?>
        </div>        
         <div class="col-md-2 col-xs-2 text-right">          
            <?php 
                $labelMontoPago = trim($form->getValueElement('monto'));
                $labelSumaPago = trim($form->getValueElement('suma_de_pagos'));
                if($labelMontoPago == ''){$labelMontoPago = 0;}
                if($labelSumaPago == ''){$labelSumaPago = 0;}
            ?>
            <h3 id="label_monto_pago" style="margin: 0px;">$<?php echo number_format($labelMontoPago,2)?></h3>
            <i><?php echo $_translator->_getTranslation('Monto de pago')?></i>
            <div class="m-t-3"></div>
            <h3 id="label_suma_pagos" style="margin: 0px;">$<?php echo number_format($labelSumaPago,2)?></h3>
            <i><?php echo $_translator->_getTranslation('Suma de pagos')?></i>
        </div>
    </div>  
    <div class="clear"></div>
    <hr/>
    <div class="table-responsive">
        <p>Facturas pendientes de pago</p>

        <table class="table table-striped table-condensed">
            <thead>
            <th class='text-center'><?php echo $_translator->_getTranslation('Num. Factura')?></th>
            <th class='text-center'><?php echo $_translator->_getTranslation('Proveedor')?></th>
            <th class='text-center'><?php echo $_translator->_getTranslation('Fecha')?></th>
            <th class='text-center'><?php echo $_translator->_getTranslation('Fecha pago')?></th>
            <th class='text-right'><?php echo $_translator->_getTranslation('Total')?></th>
            <th class='text-right'><?php echo $_translator->_getTranslation('Balance')?></th>
            <th class='text-center'><?php echo $_translator->_getTranslation('Agregar')?></th>
            </thead>
            <tbody id="listFacturasProveedores">
            <?php 
                #Status de pago en general                 
                if($form->getValueElement('status')=='1' || $form->getValueElement('status')==''){                      
                    $ajaxPago = new PagoAjax();                    
                    $facturasPendientes = $ajaxPago->getListFacturasByProveedor(array('proveedor'=>$form->getValueElement('proveedor')));
                    echo $facturasPendientes['listFacturas'];              
                }
            ?>
            </tbody>
        </table>    
    </div>
    <div class="clear" ></div>
    <hr>    
    <div class="table-responsive">
        <p>Facturas seleccionadas para pago</p>
        
        <table class="table table-striped table-condensed table-thead-customize">
        <thead>
        <th class="text-center"><?php echo $_translator->_getTranslation('Edit')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Num. Factura')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Proveedor')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Fecha')?></th>
        <th class="text-center"><?php echo $_translator->_getTranslation('Fecha pago')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Total')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Balance')?></th>
        <th class="text-right"><?php echo $_translator->_getTranslation('Pago')?></th>
        </thead>
        <tbody id="listFacturasAPagar">
        <?php if(isset($_listFacturas)){
                $entity = new PurchaseRepository();  
                foreach($_facturas as $factura => $value){
                    $result = $entity->getById($factura);
                    if($result){   ?>                    
                        <tr id="deleteInvoice_<?php echo $factura?>">
                        <td>
                            <a onclick="deleteInvoiceFromPayment('<?php echo $factura?>')" class='btn btn-sm btn-danger'><i class='fa fa-trash'></i></a>
                        </td>
                        <td class="text-center"><?php echo $result['reference'] ?></td>
                        <td class="text-center"><?php echo $result['vendorName'] ?></td>
                        <td class="text-center"><?php echo $result['date'] ?></td>
                        <td class="text-center"><?php echo $result['due_date'] ?></td>
                        <td class="text-right"><?php echo number_format($result['total'],2) ?></td>
                        <td class="text-right"><?php echo number_format($result['saldo_pendiente'],2); ?></td>                        
                        <td class="text-right"><?php $form->showElement("pago[$factura]");?></td>                        
                        </tr>
                        <script>$('#addInvoice_<?php echo $factura?>').hide();</script> 

            <?php    }
                }
            }?>                       
        </tbody>
        <tr>
            <td colspan="7" class="text-right"><label><b>Total</b></label></td>
            <td class="text-right"><h3 class='sumPagos'></h3></td>
        </tr>
        </table>
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
<script>
    $('#forma_de_pago, #proveedor').select2();
    $("#fecha").datetimepicker({format: 'DD/MM/YYYY'});   

    $("#proveedor").on('select2:select', function () {
        getListFacturasProveedores();
        $("#listFacturasAPagar").html('');
    });    

    $('#monto').on('blur',function(){
        var monto = this.value;
        if(monto.trim() === ''){monto = '0';}
        $('#monto_original').val(monto);
        setLabelMonto(monto);
    });

    $('#monto').on('change',function(){ 
        if($('#monto_original').val() === ''){return null;}
        else{limpiarFacturasAPagarPorCambioMonto();}
    });
</script>