<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Lista de envios de pedidos de sucursal');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de de envios de pedidos de sucursal');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="ShipmentStoreRequest.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar envio')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblShipment" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
            <thead>
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Envio No.');?></th>
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Pedido No.');?></th>
            <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha de pedido');?></th>
            <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>  
            <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha de envio');?></th>
            <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Ordenado');?></th>
            <th class="col-md-1 text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
            <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-2 text-center">Accion</th>
            </thead>
            <tfoot>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Envio No.');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Pedido No.');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha de pedido');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>   
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Ordenado');?></th>
            <th class="filter text-right"><?php echo $_translator->_getTranslation('Enviado');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listShipmentStoreRequests){
                    foreach($_listShipmentStoreRequests as $shipment){?>
                        <tr>
                            <td class="text-center"><?php echo $shipment['num_shipment']?></td>
                            <td class="text-center"><?php echo $shipment['id_store_request']?></td>
                            <td class="text-center"><?php echo $shipment['date']?></td>   
                            <td class="text-center"><?php echo $shipment['toName']?></td>  
                            <td class="text-center"><?php echo $shipment['required_date']?></td>   
                            <td class="text-right"><?php echo $shipment['required']?></td>
                            <td class="text-right"><?php echo $shipment['quantity']?></td>
                            <td class="text-center"><?php echo $shipment['area_name']?></td>
                            <td class="text-center"><?php echo $shipment['statusName']?></td>
                            <td class="text-center" style="white-space: nowrap">
                                <a class="btn btn-sm btn-default" href="ShipmentStoreRequest.php?action=edit&id=<?php echo $shipment['id']?>"><i class="fa fa-pencil fa-"></i></a>
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('ShipmentStoreRequest.php?action=export&flag=pdf&id=<?php echo $shipment['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                
                                <?php 
                                if($shipment['status'] == '1'){?>
                                    <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="ShipmentStoreRequest.php?action=delete&id=<?php echo $shipment['id']?>"><i class="fa fa-trash"></i></a><?php                               
                                }else{?>
                                    <a class="btn btn-sm btn-danger disabled"><i class="fa fa-trash"></i></a><?php                               
                                } ?>
                                
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
</script>
