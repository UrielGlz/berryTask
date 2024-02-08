<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Lista de pagos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de pagos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="Payment.php" class="btn btn-default pull-right"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pago')?></a> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblPagos" class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">
            <thead>                        
            <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Num. Factura');?></th>                                     
            <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="col-lg-4 col-md-4 text-center"><?php echo $_translator->_getTranslation('Proveedor');?></th>    
            <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Total');?></th>
            <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 col-md-1 text-center">Accion</th>
            </thead>
            <tfoot>                        
            <th class="filter"><?php echo $_translator->_getTranslation('Num. Factura');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Proveedor');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Total');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listPagos){
                    foreach($_listPagos as $pago){?>
                        <tr>
                            <td class="text-center"><?php echo $pago['reference']?></td>  
                            <td class="text-center"><?php echo $pago['fecha']?></td>
                            <td class="text-center"><?php echo $pago['proveedorName']?></td>
                            <td class="text-right"><?php echo number_format($pago['total'],2)?></td>                          
                            <td class="text-center"><?php echo $pago['statusName']?></td>
                            <td class="text-right">
                                <a class="btn btn-xs btn-primary" href="Payment.php?action=edit&id=<?php echo $pago['id']?>"><i class="fa fa-pencil"></i></a>
                                <!--<a class="btn btn-xs btn-info" href="#" onclick="javascript: void window.open('Payment.php?action=import&flag=pdf&id=<?php echo $pago['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>-->
                                <a class="btn btn-xs btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Payment.php?action=delete&id=<?php echo $pago['id']?>"><i class="fa fa-trash"></i></a>
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
<?php  include ROOT."/View/Modal/BusquedaAvanzadaPago.php"; ?>
<script type="text/javascript" language="javascript">   
    // DataTable
    $('#tblPagos').DataTable( { 
        searching: true,
        paginate:false,
        filter:true,
        bFilter:true,
        aaSorting:[],
        dom: 'Bfrtip',
        buttons: [
            'excel',
        ]});
</script>