<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Envios de pedidos de sucursal');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/ShipmentStoreRequest.php?action=list"><?php echo $_translator->_getTranslation('Lista de envios de pedidos de sucursal')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Envios de pedidos de sucursal');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <?php echo $form->openForm();?>
    <div style="display: none"><?php
        echo $form->showActionController();
        echo $form->showId();
        echo $form->showElement('status');
        ?>
    </div>
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-header with-border">
        <div class="box-tools">
            <?php 
            if($action === 'edit'){?>
            <a href="ShipmentStoreRequest.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar envio')?></a>
            <a class="btn btn-default" href="#" onclick="javascript: void window.open('ShipmentStoreRequest.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php
            
            }?>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">
                    <?php $form->showElement('date');?>
                    <?php $form->showElement('to_store');?>                        
                    <?php if($action === 'edit'){$form->showElement('id_store_request');}?>     
                </div> 
                <div class="col-xs-12 col-md-6">
                    <?php if($action == 'edit'){?>
                            <div class="form-group">
                                    <label class="col-md-3 col-xs-3" style="display: inline-block"><?php echo $_translator->_getTranslation('Status');?></label>
                                    <h3 class="col-md-8 col-xs-8" style="margin-top: 5px;"><?php echo $shipment->getStatusName();?></h3>                            
                            </div>
                    <?php }?>
                    <?php $form->showElement('comments');?>
                </div> 
                <?php 
                if($action == 'edit'){?>
                    <div class="col-md-12 col-xs-12">                    
                        <h4><?php echo $_translator->_getTranslation('Informacion de recibo');?></h4>
                        <hr/>
                        <div class="col-md-6 col-xs-12">
                            <table class="table table-condensed">
                                <tr>
                                    <th class="active col-md-6 col-xs-6"><?php echo $_translator->_getTranslation('Fecha de recibo');?></th>
                                    <td class="col-md-6 col-xs-6"><?php echo $shipment->getReceivingDateFormated();?></td>
                                </tr>
                            </table>
                        </div>   
                        <div class='col-md-6 col-xs-6' style='height:100%;'>
                            <label><?php echo $_translator->_getTranslation('Comentarios');?></label><br/>
                            <div style='height:100%;overflow: scroll'><?php echo $shipment->getReceivingComments();?></div>
                        </div>
                    </div>
            <?php }?>
            </div>
            <div class="clear"></div>
            <div class='col-md-12'>
                <hr/>
                <?php 
                $shipmentAjax = new ShipmentStoreRequestAjax();
                $listCompraDetalles = $shipmentAjax->getListShipmentDetails($form->getTokenForm());?>
                <table id='shipment-table' class="table table-condensed table-striped table-hover table-tfoot-customize font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                    <thead>                
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
                        <th class="col-md-5"><?php echo $_translator->_getTranslation('Descripcion');?></th>
                        <th class="col-md-3"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Requerido')?></th> 
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Cantidad')?></th> 
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Recibido')?></th> 
                    </thead>
                    <tfoot>   
                    <th></th>    
                    <th class="filter" data-filtername='codigo'><?php echo $_translator->_getTranslation('Codigo');?></th>
                    <th class="filter" data-filtername='descripcion'><?php echo $_translator->_getTranslation('Descripcion');?></th>
                    <th class="filter" data-filtername='tamano'><?php echo $_translator->_getTranslation('Tamaño');?></th>
                    <th class="filter" data-filtername='requerido'><?php echo $_translator->_getTranslation('Requerido');?></th>
                    <th class="filter" data-filtername='cantidad'><?php echo $_translator->_getTranslation('Cantidad');?></th>
                    <th class="filter" data-filtername='recibido'><?php echo $_translator->_getTranslation('Recibido');?></th>                  
                    </tfoot>
                    <tbody>
                        <?php echo $listCompraDetalles['shipmentDetails'];?>
                    </tbody>
                    <tfoot>
                        <tr>
                          <td></td>
                          <td class="text-right" colspan="3"><?php echo $_translator->_getTranslation('Total');?></td>                         
                          <td class="text-right"><span id='requiredItems'><?php echo $listCompraDetalles['requiredItems']?></span></td>  
                          <td class="text-right"><span id='totalItems'><?php echo $listCompraDetalles['totalItems']?></span></td>  
                          <td class="text-right"><span id='receivedItems'><?php echo $listCompraDetalles['receivedItems']?></span></td>                 
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row">
                <?php 
                $form->showElement('idDetailTemp');
                $form->showElement('idProduct');?>
                <div class='col-lg-2'>
                    <?php $element = $form->getElement('quantity');?>
                    <div class='col-lg-12 p-a-0'><label><?php echo $element['label']?></label></div>
                    <?php echo $form->createElement($element);?>
                </div> 
                <div class='col-lg-6'>
                    <?php $element = $form->getElement('product');?>
                    <div class='col-lg-12 p-a-0'><label><?php echo $element['label']?></label></div>
                    <?php echo $form->createElement($element);?>
                </div>         
                <div class='col-lg-1 p-t-3'>
                    <?php /*$element = $form->getElement('buscar');*/?>
                    <?php /*echo $form->createElement($element);*/?>
                </div>
                <div class='col-lg-1 pull-right' style="margin-top: 20px;">
                    <?php $element = $form->getElement('terminar');?>
                    <?php echo $form->createElement($element);?>
                </div>
            </div>         
  </div><!-- /.box-body -->
  <?php echo $form->closeForm();?>
</div><!-- /.box -->
</section>
<style>
    tfoot {
    display: table-header-group;
}
</style>
<?php if(isset($_disabled) && $_disabled){?> <script>$('#shipment-table tr td a').addClass('disabled');</script><?php } ?>
<script>  
    $('#to_store').select2({placeholderOption: 'first'});
    $("#date,#dateDatePicker").datetimepicker({format: "MM/DD/YYYY hh:mm A "});
    
    $('#quantity').on('focus',function(){$(this).select();});
    $('#quantity').on('keydown',function(e){
        var keycode = e.keyCode || e.which;
        if(keycode === 13 && $('#product').prop('disabled') === true) {
           setShipmentDetails();
        }
    });
        
    /*    
    $('#product').on('keydown',function(e){
        var keycode = e.keyCode || e.which;
        if(keycode === 13) {
            $( "#idProduct" ).val($(this).val());  
           setShipmentDetails(true);
        }
    });*/
    
    $( "#product" ).autocomplete({
        source: function( request, response ) {
          $.post('/Controller/Product.php', {
              action: 'ajax',
              request: 'getListaProducts',
              item: request.term
          }, function(data) {
                  response(data.productos);
             }, 'json');
        },     
        select: function( event, ui ) {
            $( "#idProduct" ).val( ui.item.value ); 
            $("#product").val('');
            setShipmentDetails();
            return false;
          }    
    } );    

    
    /*Guardar cantidad en Enter*/
    $('table#shipment-table tbody').on('focus','._shippedQuantity',function(){$(this).select();});        
    $('table#shipment-table tbody').on('keydown','tr td input._shippedQuantity',function(e){        
        var keycode = e.keyCode || e.which;
        if(keycode === 13) {   
            $(this).unbind('blur');/*Deshabilita blur, lo hago para que no llame dos veces la function setShipmentDetails(); en la respues de esta funcion se vuelve a llenar tabla y se activa de nuevo blur*/
            $("#idDetailTemp").val($(this).data('iddetailtemp')); 
            $("#idProduct").val($(this).data('productid')); 
            $("#quantity").val($(this).val());             
        
            var nextFocus = parseInt($(this).data('focusindex'));
            nextFocus++;
            setShipmentDetails(null,function(){
                $('input[data-focusindex='+nextFocus+']').focus();
            });                  
        }
    }); 
    
    /*Guardar cantidad en Blur*/
    $('table#shipment-table tbody').on('blur','tr td input._shippedQuantity',function(){  
        var that = this;
        var index = 0;
         setTimeout(function(){
            var focused = $(':focus');  
            index = $(focused).data('focusindex');
            
            $("#idDetailTemp").val($(that).data('iddetailtemp')); 
            $("#idProduct").val($(that).data('productid')); 
            $("#quantity").val($(that).val()); 
            setShipmentDetails(null,function(){$('input[data-focusindex='+index+']').focus();});           
            
          }, 1);   
    });
    
    $('select').on('select2:opening',function(e){
        e.preventDefault();
        if($(this).attr('readonly') !== 'readonly'){$(this).unbind('select2:opening').select2('open');}
    });
</script>