<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Lista de recibos de pedidos de sucursal');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de de recibos de pedidos de sucursal');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="ReceivingStoreRequest.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar recibo')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblShipment" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
            <thead>
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>
            <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>                     
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Pedido #');?></th>
            <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
            <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Recibido');?></th>
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-2 text-center">Accion</th>
            </thead>
            <tfoot>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('No.');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>                     
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Pedido #');?></th>
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Recibido');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="filter text-center">Accion</th>
            </tfoot>
            <tbody>
            <?php 
                if($_listReceivingStoreRequests){
                    foreach($_listReceivingStoreRequests as $receiving){?>
                        <tr>
                            <td class="text-center"><?php echo $receiving['num_shipment']?></td>
                            <td class="text-center"><?php echo $receiving['date']?></td>   
                            <td class="text-center"><?php echo $receiving['storeName']?></td>  
                            <td class="text-center"><?php echo $receiving['id_store_request']?></td>
                            <td class="text-right"><?php echo $receiving['quantity']?></td>
                            <td class="text-right"><?php echo $receiving['received']?></td>
                            <td class="text-center"><?php echo $receiving['area_name']?></td>
                            <td class="text-center"><?php echo $receiving['statusName']?></td>
                            <td class="text-center" style="white-space: nowrap">
                                <a class="btn btn-sm btn-default" href="ReceivingStoreRequest.php?action=edit&id=<?php echo $receiving['id']?>"><i class="fa fa-pencil fa-"></i></a>                                
                                <?php 
                                if($login->getRole() == '1'){                                
                                    if(($receiving['status'] == '2' || $receiving['status'] == '5' )  && (is_null($receiving['invoice_id']) || $receiving['invoice_id'] == '' || $receiving['invoice_id'] == '0')) { ?>
                                        <a class="btn btn-sm btn-warning _createInvoiceFromReceiving" title="<?php echo $_translator->_getTranslation('Crear Factura') ?>" data-id="<?php echo $receiving['id']?>" data-status="<?php echo $receiving['status']?>"><i class='fa fa-file-text'></i></a><?php                                     
                                    }else{?>
                                        <a class="btn btn-sm btn-warning disabled" title="<?php echo $_translator->_getTranslation('Crear Factura') ?>" data-id="<?php echo $receiving['id']?>" data-status="<?php echo $receiving['status']?>"><i class='fa fa-file-text'></i></a>                                    
                                        <?php                                    
                                    }                                    
                                }?>     
                                    
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('ReceivingStoreRequest.php?action=export&flag=pdf&id=<?php echo $receiving['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                
                                 <?php
                                if($receiving['status'] !=='4' ){?>
                                    <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="ReceivingStoreRequest.php?action=delete&id=<?php echo $receiving['id']?>"><i class="fa fa-trash"></i></a><?php
                                }else{ ?>
                                    <a class="btn btn-sm btn-danger disabled"><i class="fa fa-trash"></i></a><?php
                                }?>
                                
                            </td>
                        </tr><?php
                    }                   
                }?>
            </tbody>
        </table>
    </div>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<script type="text/javascript" language="javascript">
    $('#tblShipment').DataTable({   
            paginate:false,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [
            'excel'
            ]
        });
    $('#tblShipment').removeClass( 'display' ).addClass('table table-striped table-bordered');
    
    
    function createInvoiceFromReceiving(id){
        $.confirm({
            theme: 'material',
            columnClass: 'col-md-6 col-md-offset-3',
            icon: 'fa fa-question-circle',
            title: 'Crear factura',
            /*Reemplace comilla simple por doble para que no marque error*/
            content: "Desea crear una Factura para este recibo ?<br/><br/><?php $form = new InvoiceForm(); echo str_replace('"', "'", $form->getElementString('id_customer')) ?>",
            buttons:{
                cancel: {
                    text:'No',
                    btnClass: 'btn-default col-md-4 pull-right',
                    action: function(){
                       $(this).remove();
                    }
                },
                confirm: {
                    text: 'Si ',
                    btnClass: 'btn-primary col-md-4 pull-right',
                    action: function(){
                       $.post('/Controller/Invoice.php', {
                            action: 'ajax',
                            request: 'createInvoiceFromReceiving',
                            receiving_id: id,
                            customer_id: $('#id_customer').val()
                        }, function(data) {
                            if (data.response){
                                document.location.reload();
                            }else{
                                $('.flashmessenger').html(data.msg);
                            }
                        }, 'json');
                    }
                }
            }
        });
    }
    
     $('._createInvoiceFromReceiving').on('click', function(){
        var id = $(this).data('id');
        createInvoiceFromReceiving(id);
    });
</script>
