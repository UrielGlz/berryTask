<section class="content-header">
    <h1><i class='fa-fw fa fa-file-text-o'></i> <?php echo $_translator->_getTranslation('Lista de facturas');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de facturas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools pull-right">
           <a href="Invoice.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar factura')?></a>
           <span class="btn btn-default _advancedSearch"><i class='fa fa-search-plus'></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
                <div class='table-responsive'>
                    <div class="clear"></div>
                <table id="tblSalesOrders" class="table table-striped table-hover table-condensed font-size-12 datatable_whit_filter_column _responsiveDetails _hideSearch" style="width:100%">
                    <thead>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Factura #');?></th>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Fecha de pago');?></th>
                    <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Cliente');?></th>
                    <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Recibo #');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Balance');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Enviada');?></th>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>
                    </thead>
                    <tfoot>                    
                    <th class="filter"><?php echo $_translator->_getTranslation('Factura #');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Fecha');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Fecha de pago');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Cliente');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Recibo #');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Total');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Balance');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Enviada');?></th>
                    <th></th>
                    </tfoot>
                    <tbody>
                    <?php
                        if($_listSalesOrders){
                            foreach($_listSalesOrders as $salesOrder){?>
                                <tr>                                    
                                    <td class="text-center"><?php echo $salesOrder['invoice_num']?></td>
                                    <td class="text-center"><?php echo $salesOrder['formatedDate']?></td>
                                    <td class="text-center wordwrap-breakword"><?php echo $salesOrder['formatedDueDate']?></td>
                                    <td class="text-center"><?php echo $salesOrder['customerName']?></td>
                                    <td class="text-center"><?php echo $salesOrder['store_name']?></td>
                                    <td class="text-center"><?php echo $salesOrder['num_shipment']?></td>                                 
                                    <td class="text-center"><?php echo number_format($salesOrder['total'],2)?></td>
                                    <td class="text-center"><?php echo number_format($salesOrder['total']-$salesOrder['payments'],2)?></td>
                                    <td class="text-center"><?php echo $salesOrder['statusName']?></td>
                                    <td class="text-center _invoice_id_<?php echo $salesOrder['id'] ?>"><?php if($salesOrder['invoice_sent'] == '1'){echo "<i class='fa fa-check'></i>";}else{echo "<i class='fa fa-close'></i>";}?></td>
                                    <td class="text-center" style='white-space:nowrap'>
                                        <a class="btn btn-sm btn-default" href="Invoice.php?action=edit&id=<?php echo $salesOrder['id']?>"><i class="fa fa-pencil"></i></a>
                                        <span class="btn btn-sm btn-default" onclick="window.open('/Controller/Invoice.php?action=export&format=pdf&flag=invoice&id=<?php echo $salesOrder['id'];?>','','width=700,height=500,status=1,scrollbars=1,resizable=1')"><i class="fa fa-print"></i></span>
                                        <span class='btn btn-sm btn-default _emailing' data-operationname="invoice" data-operationid='<?php echo $salesOrder['id']?>' title='Enviar por correo'><i class='fa fa-envelope'></i></span>                                       
                                        <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Invoice.php?action=delete&id=<?php echo $salesOrder['id']?>"><i class="fa fa-trash"></i></a>
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
<style>
    .span_on_dropdown_menu{
        padding:3px 20px;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .span_on_dropdown_menu:hover{
        background-color: #f5f5f5;
    }
</style>
<?php //include ROOT."/View/Modal/emailing.php"; ?>
<?php include ROOT."/View/Modal/advancedSearch.php";?>
<script>
    
    $('#dueStartDate,#dueEndDate').datetimepicker({format: 'MM/DD/YYYY'});
    function formatResponsiveDetails ( d ) {
        // `d` is the original data object for the row
        return '<table class=\'table\' style="width:30%;">'+
            '<tr>'+
                '<td><?php echo $_translator->_getTranslation('Fecha de creacion')?>:</td>'+
                '<td>'+d[11]+'</td>'+
            '</tr>'+
            '<tr>'+
                '<td><?php echo $_translator->_getTranslation('Creado por')?>:</td>'+
                '<td>'+d[12]+'</td>'+
            '</tr>'+
        '</table>';
    }

    function callbackEmailing(data){
        var options = [];
        jQuery.each(data['data_sent'], function( i, field ) {
           options[field.name] = field.value;
        });

        var selector = "._invoice_id_"+options['operation_id'];
        $(selector).html("<i class='fa fa-check'></i>");
        $(selector).closest('tr').effect('highlight',{color: '#b2ffb2'},1500);
    }

</script>