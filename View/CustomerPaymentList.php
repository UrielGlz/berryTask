<section class="content-header">
    <h1><i class='fa-fw fa fa-upload'></i> <?php echo $_translator->_getTranslation('Lista de pagos de clientes');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de pagos de clientes');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
        <a href="CustomerPayment.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar pago')?></a> 
        <span class="btn btn-default _advancedSearch"><i class='fa fa-search-plus'></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span>
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
    <div class='table-responsive'>
        <table id="tblPagos" class="table table-bordered table-striped table-hover table-condensed font-size-12 datatable_whit_filter_column _responsiveDetails _hideSearch" style="width:100%">
            <thead>               
            <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Pago #');?></th>                                        
            <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="col-lg-3 col-md-3 text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>                                    
            <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Factura #');?></th>  
            <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Forma pago');?></th>
            <th class="col-lg-1 col-md-1 text-right"><?php echo $_translator->_getTranslation('Monto');?></th>
            <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Status');?></th>
            <th class="col-lg-1 col-md-1 text-center">Accion</th>
            </thead>
            <tfoot>                                 
            <th class="filter"><?php echo $_translator->_getTranslation('Pago #');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Fecha');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Cliente');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Factura #');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Forma de pago');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Monto');?></th>
            <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>
            <th></th>
            </tfoot>
            <tbody>
            <?php 
                if($_listPagos){
                    foreach($_listPagos as $pago){?>
                        <tr>                            
                            <td class="text-center"><?php echo $pago['id']?></td>  
                            <td class="text-center"><?php echo $pago['date']?></td>
                            <td class="text-center"><?php echo $pago['customerName']?></td>                            
                            <td class="text-center"><?php echo $pago['invoice_num']?></td> 
                            <td class="text-center"><?php echo $pago['store_name']?></td>
                            <td class="text-center"><?php echo $pago['paymentMethod']?></td>
                            <td class="text-right"><?php echo number_format($pago['total'],2)?></td>                          
                            <td class="text-center"><?php echo $pago['statusName']?></td>
                            <td class="text-right" style="white-space: nowrap">
                                <a class="btn btn-sm btn-default" href="CustomerPayment.php?action=edit&id=<?php echo $pago['id']?>"><i class="fa fa-pencil"></i></a>
                                <!--<a class="btn btn-xs btn-info" href="#" onclick="javascript: void window.open('Payment.php?action=import&flag=pdf&id=<?php echo $pago['id']?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-file-pdf-o fa-"></i></a>-->
                                <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="CustomerPayment.php?action=delete&id=<?php echo $pago['id']?>"><i class="fa fa-trash"></i></a>
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
<?php include ROOT."/View/Modal/advancedSearch.php";?>
<script>  
    $('#dueStartDate,#dueEndDate').datetimepicker({format: 'MM/DD/YYYY'});
    function formatResponsiveDetails ( d ) {
        // `d` is the original data object for the row
        return '<table class=\'table\' style="width:30%;">'+
            '<tr>'+
                '<td><?php echo $_translator->_getTranslation('Fecha de creacion')?>:</td>'+
                '<td>'+d[10]+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td><?php echo $_translator->_getTranslation('Creado por')?>:</td>'+
                '<td>'+d[11]+'</td>'+
            '</tr>'+
        '</table>';
    }
</script>