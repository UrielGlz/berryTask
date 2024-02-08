<section class="content-header">
    <h1><i class='fa-fw fa fa-download'></i> <?php echo $_translator->_getTranslation('Recibos de pedidos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li><a href="<?php echo ROOT_HOST?>/Controller/ReceivingStoreRequest.php?action=list"><?php echo $_translator->_getTranslation('Lista de recibos de pedidos de sucursal')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Recibos de pedidos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="box-header with-border">
        <div class="box-tools">
            <?php 
            if($action === 'edit'){?>
            <a href="ReceivingStoreRequest.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar recibo de pedido')?></a>
            <a class="btn btn-default" href="#" onclick="javascript: void window.open('ReceivingStoreRequest.php?action=export&flag=pdf&id=<?php echo $form->getId();?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print fa-"></i> <?php echo $_translator->_getTranslation('Imprimir') ?></a><?php  
            
            }?>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
     <div class="clear"></div>
            <?php echo $form->openForm();?>
            <div style="display: none"><?php
                echo $form->showActionController();
                echo $form->showId();
                echo $form->showElement('allow_edit');
                echo $form->showElement('status_invoice');
                echo $form->showElement('status');
                ?>
            </div>
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-6">
                    <?php $form->showElement('num_shipment');?>
                    <?php $form->showElement('date');?>      
                </div>
                <div class="col-xs-12 col-md-6">
                <?php 
                if($action == 'edit'){?>
                    <div class="form-group">
                        <label class="col-md-4 col-xs-4" style="display: inline-block;"><?php echo $_translator->_getTranslation('Status');?></label>
                        <h3 class="col-md-8 col-xs-8" style="margin-top: 5px"><?php echo $receiving->getStatusName();?></h3>                            
                    </div><?php 
                }?>
                <?php $form->showElement('comments');?>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">                    
                <h4><?php echo $_translator->_getTranslation('Informacion de envio');?></h4>
                <hr/>
                <div class="col-md-5 col-xs-12">
                    <table class="table table-condensed">
                        <tr>
                            <th class="active col-md-6 col-xs-6"><?php echo $_translator->_getTranslation('Fecha de envio');?></th>
                            <td class="col-md-6 col-xs-6"><?php echo $receiving->getShipmentDateFormated();?></td>
                        </tr>
                    </table>
                </div>                
                <div class='col-md-7 col-xs-6' style='height:100%;'>
                    <label><?php echo $_translator->_getTranslation('Comentarios');?></label><br/>
                    <div style='height:100%;overflow: scroll'><?php echo $receiving->getShipmentComments();?></div>
                </div>
            </div>
            <div class="clear"></div>
            <div class='col-md-12'>
                <hr/>
                <?php 
                    $compraAjax = new ReceivingStoreRequestAjax();               
                    $listCompraDetalles = $compraAjax->getListReceivingDetails($form->getTokenForm());?>
                <div class='table-responsive'>
                <table id='receiving-table' class="table table-condensed table-striped table-hover table-thead-customize table-tfoot-customize font-size-11 datatable_whit_filter_column _hideSearch" style="width:100%">
                    <thead>                
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion')?></th>
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>
                        <th class="col-md-5"><?php echo $_translator->_getTranslation('Descripcion');?></th>
                        <th class="col-md-3"><?php echo $_translator->_getTranslation('Tamaño');?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Pedido')?></th> 
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Enviado')?></th>
                        <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Recibido')?></th> 
                    </thead>
                    <tfoot>   
                        <th></th>    
                        <th class="filter" data-filtername='codigo'><?php echo $_translator->_getTranslation('Codigo');?></th>
                        <th class="filter" data-filtername='descripcion'><?php echo $_translator->_getTranslation('Descripcion');?></th>
                        <th class="filter" data-filtername='tamano'><?php echo $_translator->_getTranslation('Tamaño');?></th>
                        <th></th>
                        <th></th>     
                        <th></th>     
                    </tfoot>
                    <tbody>
                        <?php echo $listCompraDetalles['receivingDetails'];?>
                    </tbody>
                    <tfoot>
                        <tr>
                          <td></td>
                          <td class="text-right" colspan="3"><?php echo $_translator->_getTranslation('Total');?></td>
                          <td class="text-right"><span id='totalPedido'><?php echo $listCompraDetalles['totalPedido']?></span></td>                 
                          <td class="text-right"><span id='totalItems'><?php echo $listCompraDetalles['totalItems']?></span></td>
                          <td class="text-right"><span id='receivedItems'><?php echo $listCompraDetalles['receivedItems']?></span></td>                 
                        </tr>
                    </tfoot>
                </table>
                </div>
                <?php 
                $form->showElement('idDetailTemp');
                $form->showElement('idProduct');
                ?>                
                <div class="form-group">
                    <div class="col-md-8">
                        <div class="form-inline">
                            <div class="form-group col-md-2">
                                <?php $element = $form->getElement('received');?>
                                <label><?php echo $element['label']?></label><br/>
                                 <?php echo $form->createElement($element);?>
                            </div>
                            <div class="form-group col-md-8">
                                <?php $element = $form->getElement('product');?>
                                <label><?php echo $element['label']?></label><br/>
                                <?php echo $form->createElement($element);?>
                            </div>
                            <div class="form-group col-md-2" style="margin-top: 25px">
                                <?php $element = $form->getElement('buscar');?>
                                <?php echo $form->createElement($element);?>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="clear"></div>
                <br/>
                <div class="pull-left">
                    <?php if(!is_null($_receivingData['status_invoice']) && $_receivingData['status_invoice'] != '3'){ $element = $form->getElement('btn_allow_edit'); echo $form->createElement($element);}?>
                </div>
                <div class="pull-right">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="form-inline">
                                <?php if($action=='edit'){?>
                                        <div class="form-group col-md-8 _field_receive_icomplete">
                                            <?php $checked = null; if(isset($_receivingData['received_incomplete']) && $_receivingData['received_incomplete'] == '1'){$checked = 'checked';}?>
                                            <label><?php echo $_translator->_getTranslation('Recibir incompleto') ?>&nbsp;&nbsp;<input type="checkbox" name='received_incomplete' id="received_incomplete" <?php echo $checked;?>  value='1' <?php echo $checked;?>  style='width:20px;height:20px;margin-top:0px' /> </label>
                                        </div>
                                <?php }?>                                
                                <div class="form-group col-md-4">                                    
                                    <?php $element = $form->getElement('terminar');?>
                                    <?php echo $form->createElement($element);?>
                                </div>                                
                            </div>
                        </div>  
                    </div>                    
                </div>
                
            </div>
        <?php echo $form->closeForm();?>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<?php 
    if(isset($_disabled) && $_disabled === true){?>
        <script>$('table#receiving-table tbody tr td a').addClass('disabled'); $('._field_receive_icomplete').hide()</script><?php    
    }
?>
<script>  
    $('#num_shipment').select2();
    $('#num_shipment').on('select2:select',function(){
        getShipmentData();
    });
    
    $("#date,#dateDatePicker").datetimepicker({format: "MM/DD/YYYY hh:mm A "});
    
    $('#received').on('focus',function(){$(this).select();});
    $('#received').on('keydown',function(e){
        var keycode = e.keyCode || e.which;
        if(keycode === 13 && $('#product').prop('disabled') === true) {
            setReceivingStoreRequestDetails();
        }
    });
        
    /* 
    $('#product').on('keydown',function(e){
        var keycode = e.keyCode || e.which;
        if(keycode === 13) {
            $( "#idProduct" ).val($(this).val());  
           setReceivingStoreRequestDetails(true);
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
            setReceivingStoreRequestDetails();
            return false;
          }    
    } );    
    
    $('#terminar').on('click',function(){
       updateReceivingStoreRequestQty(function(){submit('receiving_store_request');});
    });
    
    $('#btn_allow_edit').on('click',function(){allowEditReceivingStoreRequest();});
    
    $('select').on('select2:opening',function(e){
       e.preventDefault();
       if($(this).prop('disabled') === false){$(this).unbind('select2:opening').select2('open');}
    });
   
   $(function(){
        if(parseInt($('#totalPedido').html()) <= parseInt($('#receivedItems').html())){ 
            $('#received_incomplete').prop('disabled',true);
        }else{
            $('#received_incomplete').prop('disabled',false);
        }
   });
   
    
    
</script>