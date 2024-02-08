<section class="content-header">
    <h1><i class='fa-fw fa fa-cube'></i> <?php echo $_translator->_getTranslation('Lista de recibos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de compras');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="Receiving.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar recibo')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblReceiving" class="table table-bordered table-striped table-hover table-condensed font-size-11 datatable_whit_filter_column">
            <thead>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Documento');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Numero');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Fecha de recibo');?></th>                    
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Proveedor');?></th>
            <th class="col-lg-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-1 text-center">Accion</th>
            </thead>
            <tfoot>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Documento');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Numero');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha de recibo');?></th>                    
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Proveedor');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listReceiving){
                    foreach($_listReceiving as $recibo){?>
                        <tr> 
                            <td class="text-center"><?php echo $recibo['type']?></td>                                    
                            <td class="text-center"><?php echo $recibo['reference_id']?></td>    
                            <td class="text-center"><?php echo $recibo['receiving_date']?></td>
                            <td class="text-center"><?php echo $recibo['vendor']?></td>
                            <td class="text-center"><?php echo $recibo['statusName']?></td>
                            <td class="text-center"><?php echo $recibo['storeName']?></td>  
                            <td class="text-center">
                                <a class="btn btn-sm btn-default" href="Receiving.php?action=edit&id=<?php echo $recibo['id']?>"><i class="fa fa-pencil"></i></a>
                                <a class="btn btn-sm btn-default" href="#" onclick="javascript: void window.open('Receiving.php?action=export&flag=pdf&id=<?php echo $recibo['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>
                                <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Receiving.php?action=delete&id=<?php echo $recibo['id']?>"><i class="fa fa-trash"></i></a>
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
    $('#tblReceiving').DataTable({   
            paginate:false,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [
            'excel'
            ]
        });
</script>