<section class="content-header">
    <h1><i class='fa-fw fa fa-cube'></i> <?php echo $_translator->_getTranslation('Recibos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/Receiving.php?action=list"><?php echo $_translator->_getTranslation('Lista de recibos')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Recibos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-header with-border">
        <div class="box-tools">
            <?php 
            if($action === 'edit'){?>
            <a href="Receiving.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar recibo')?></a>
            <a class="btn btn-default" href="#" onclick="javascript: void window.open('Receiving.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php  
            
            }?>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <?php echo $form->openForm();?>
            <div style="display: none"><?php
                echo $form->showActionController();
                echo $form->showId();
                echo $form->showElement('status');
                echo $form->showElement('type');
                echo $form->showElement('reference_id');
                echo $form->showElement('store_id_of_document');
                $arrayDataLabels = array(
                    'purchase'=>array(
                        'purchase_date_label'=>'Fecha de compra',
                        'purchase_vendor_label'=>'Provedor',
                        'purchase_reference_label'=>'Factura #',
                        'purchase_lot_label'=>'Lote'),
                    
                    'transfer'=>array(
                        'purchase_date_label'=>'Fecha de traspaso',
                        'purchase_vendor_label'=>'Desde',
                        'purchase_reference_label'=>'Requerido por',
                        'purchase_lot_label'=>'')
                    );
                
                $dataLabels = $arrayDataLabels[$form->getValueElement('type')];
                ?>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">
                    <table class="table table-condensed">
                        <tr>
                            <th class="text-left col-md-4"><?php echo $_translator->_getTranslation('Referencia');?></th>
                            <td><div class='form-group'><?php $element = $form->getElement('document_reference');echo $form->createOnlyElement($element);?></div></td>
                        </tr>
                        <tr>
                            <th id="purchase_date_label" class="text-left" class="data_purchase"><?php echo $_translator->_getTranslation($dataLabels['purchase_date_label'])?></th>
                            <td id="purchase_date" class="data_purchase"><?php echo $_receiving->getPurchaseDate(); ?></td>
                        </tr>
                        <tr>
                            <th id="purchase_vendor_label"  class="text-left" class="data_purchase"><?php echo $_translator->_getTranslation($dataLabels['purchase_vendor_label'])?></th>
                            <td id="purchase_vendor" class="data_purchase"><?php echo $_receiving->getVendorName(); ?></td>
                        </tr>
                        <tr>
                            <th id="purchase_reference_label" class="text-left" class="data_purchase"><?php echo $_translator->_getTranslation($dataLabels['purchase_reference_label'])?></th>
                            <td id="purchase_reference" class="data_purchase"><?php echo $_receiving->getReference(); ?></td>
                        </tr>
                        <tr>
                            <th id="purchase_lot_label" class="text-left" class="data_purchase"><?php echo $_translator->_getTranslation($dataLabels['purchase_lot_label'])?></th>
                            <td id="purchase_lot" class="data_purchase"><?php echo $_receiving->getLot(); ?></td>
                        </tr>
                    </table>
                </div> 
                <div class="col-xs-12 col-md-6">
                    <?php 
                    $form->showElement('date_time');
                    $form->showElement('comments');
                    ?> 
                </div> 
            </div>
            <div class="clear"></div>
            <div class='col-md-12'>
                <hr/>
                <?php 
                    $compraAjax = new ReceivingAjax();               
                    $listCompraDetalles = $compraAjax->getListReceivingDetails($form->getTokenForm());?>
                <div class='table-responsive'>
                <table id='receiving-table' style="font-size:11px" class="table table-condensed table-striped table-hover">
                    <thead>                                        
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
                        <th class="col-lg-2"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Presentacion');?></th>  
                        <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Marca');?></th>  
                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Cantidad')?></th>
                        <th class="col-lg-1 text-right"><?php echo $_translator->_getTranslation('Recibido')?></th>
                        <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Locacion')?></th>
                    </thead>
                    <tbody>
                        <?php echo $listCompraDetalles['receivingDetails'];?>
                    </tbody>
                </table>
                </div>
            </div>
            <!-- Modal -->         
            <div class="row">
                <div class="pull-right">
                    <div class='col-md-6 p-t-3'>
                        <?php $element = $form->getElement('agregar_producto');?>
                        <?php echo $form->createElement($element);?>
                    </div>
                    <div class='col-md-6 p-t-3'>
                        <?php $element = $form->getElement('terminar');?>
                        <?php echo $form->createElement($element);?>
                    </div>
                </div>
            </div>
        <?php echo $form->closeForm();?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<?php include ROOT."/View/Modal/addReceivingProduct.php";?>
<style>
    .input-group-btn.descuento_tipo{
        width:32%;      
    }
    
    label.taxes_included{
        margin-top: -2px;
    }

    .ui-autocomplete {
      z-index: 1510 !important;
    }
    
    tr th div.form-group {
        margin-bottom: 0px;
    }
</style>
<?php 
    if(isset($_disabled) && $_disabled === true){?>
        <script>$('table#receiving-table tbody tr td a').addClass('disabled');</script><?php    
    }
?>
<script> 
    $("#date_time,#dateTimeDatePicker,#expiration_date,#expirationDatePicker").datetimepicker({format: "MM/DD/YYYY hh:mm A"});
    $('#document_reference,#location,#taxes,#taxes_included,#method_payment').select2(); 
    $('#document_reference').on('select2:select',function(){getPurchaseDetailsToReceive(true);});    
    
    $('#agregar_producto').on('click',function(){
        clearModalAddProduct();
        $('.flashmessenger_modal_add_product').html('');
         _getTranslation('Agregar recibo',function(msj){ $('#title_modal_receivingProduct').html(msj);});
        $("form[name=addProduct] :input").attr("disabled", false);
        $('#modalAddReceivingProduct').modal('show');
    });
    
    $('._addProduct').on('click',function(){setReceivingDetails();});
    $('._closeModalAddProduct').on('click',function(){clearModalAddProduct();$('#modalAddReceivingProduct').modal('hide');});
    
    $('#terminar').on('click',function(){
       updateReceiviedQuantity(function(){submit('receiving');});
    });
    
    
    $( "#product" ).autocomplete({
        source: function( request, response ) {
          $.post('/Controller/Receiving.php', {
              action: 'ajax',
              request: 'getListProduct',
              item: request.term
          }, function(data) {
                  response(data.products);
             }, 'json');
        }, 

        focus: function( event, ui ) {
            $( "#product" ).val( ui.item.label );
            return false;
          },      
        select: function( event, ui ) {
            $( "#id_product" ).val( ui.item.value ); 
            //$("#product").val('');
            getDefaultDataProduct();
            return false;
          }    
    } );     
    
    getPurchaseDetailsToReceive();
    
    
    
</script>