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
        <a href="StoreRequest.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pedido de sucursal')?></a> 
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
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>
            </thead>
            <tfoot>
            <th class="filter"><?php echo $_translator->_getTranslation('No.');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha de pedido');?></th>                     
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha de entrega');?></th>                     
            <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Area');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
            if($_listStoreRequests){
                foreach($_listStoreRequests as $requisicion){?>
                    <tr>
                        <td class="text-center"><?php echo $requisicion['id']?></td> 
                        <td class="text-center"><?php echo $requisicion['formatedDate']?></td>  
                        <td class="text-center"><?php echo $requisicion['formatedDeliveryDate']?></td>  
                        <td class="text-center"><?php echo $requisicion['storeName']?></td>
                        <td class="text-center"><?php echo $requisicion['areaName']?></td>
                        <td class="text-center"><?php echo $requisicion['statusName']?></td>
                        <td class="text-center" style="white-space: nowrap">
                            <a href="StoreRequest.php?action=edit&id=<?php echo $requisicion['id']?>" class="btn btn-sm btn-default"><i class="fa fa-pencil fa-"></i></a>
                            <a href="#"  class="btn btn-sm btn-default" onclick="javascript: void window.open('StoreRequest.php?action=export&format=pdf&flag=store_request&id=<?php echo $requisicion['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>                                                                              
                            <?php 
                            if($requisicion['status'] == '1'){?>
                                <a href="#" class="btn btn-sm btn-default" onclick="generateShipment('<?php echo $requisicion['id'] ?>')"><i class="fa fa-truck"></i> </a>
                    <?php   }elseif($requisicion['status'] == '2'){?>
                                <a href="#" class="btn btn-sm btn-default disabled"><i class="fa fa-truck"></i> </a>
                    <?php   } ?>                                                                                                                
                            <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="StoreRequest.php?action=delete&id=<?php echo $requisicion['id']?>"><i class="fa fa-trash"></i></a>
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
</script>