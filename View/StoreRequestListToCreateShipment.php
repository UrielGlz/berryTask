<section class="content-header">
    <h1><i class='fa-fw fa fa-bookmark'></i> <?php echo $_translator->_getTranslation('Lista de pedidos de sucursal');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de ordenes especiales');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">   
        <span class="btn btn-default _searchStoreRequest"><i class="fa fa-search-plus"></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblStoreRequest" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
            <thead>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('No.');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Fecha de pedido');?></th>                     
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Fecha de entrega');?></th>                     
            <th class="col-lg-3 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('En proceso');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>
            </thead>
            <tfoot>
            <th class="filter"><?php echo $_translator->_getTranslation('No.');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha de pedido');?></th>                     
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha de entrega');?></th>                     
            <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('En proceso');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
            if($_listStoreRequests){
                foreach($_listStoreRequests as $requisicion){
                    if($requisicion['status'] == '2'){break;}?>
                    <tr>
                        <td class="text-center"><?php echo $requisicion['id']?></td> 
                        <td class="text-center"><?php echo $requisicion['formatedDate']?></td>  
                        <td class="text-center"><?php echo $requisicion['formatedDeliveryDate']?></td>  
                        <td class="text-center"><?php echo $requisicion['storeName']?></td>
                        <td class="text-center"><?php echo $requisicion['areaName']?></td>
                        <td class="text-center _inProcess_<?php echo $requisicion['id']; ?>"><?php if($requisicion['in_process'] == '1'){?> <i class="fa fa-check fa-2x text-olive"></i><?php } ?></td>
                        <td class="text-center" style="white-space: nowrap"><?php                             
                            if($requisicion['in_process'] == '0'){?>
                                <span class="btn btn-default _blockUnblock" data-id="<?php echo $requisicion['id'];?>" data-inprocess="1" >&nbsp;&nbsp;&nbsp;<?php echo $_translator->_getTranslation('Bloquear'); ?>&nbsp;&nbsp;&nbsp;</span><?php
                            }elseif($requisicion['in_process'] == '1'){?>
                                <span class="btn btn-default _blockUnblock" data-id="<?php echo $requisicion['id'];?>" data-inprocess="0"><?php echo $_translator->_getTranslation('Desbloquear'); ?></span><?php
                            }?>                    
                                
                            <a href="#"  class="btn btn-sm btn-default" onclick="javascript: void window.open('StoreRequest.php?action=export&format=pdf&flag=store_request&id=<?php echo $requisicion['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                                                              
                            <?php 
                            if($_areaData['automatic_shipment'] == '0'){?>
                                <a href="#" class="btn btn-sm btn-default" onclick="generateShipment('<?php echo $requisicion['id'] ?>')"><i class="fa fa-truck"></i> </a><?php 
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
<?php  //include ROOT."/View/Modal/BusquedaAvanzadaSR.php"; ?>
<script type="text/javascript" language="javascript">
    $('._searchSpecialRequest').on('click',function(){$('#modalBusquedaAvanzadaSR').modal('show');});    
    $('#tblStoreRequest').DataTable({paginate:false,filter:true,aaSorting:[]});
    
    $('._blockUnblock').on('click',function(){blockUnblockOrder(this);});
</script>