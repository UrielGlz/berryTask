<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Registros de ventas');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Registros de ventas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default _addSalesRecord"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar registro de venta')?></span> 
      <span class="btn btn-default _advancedSearch"><i class='fa fa-search-plus'></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblBrands" class="table table-bordered table-condensed table-striped table-hover table-bordered datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Total ventas');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Total retiros');?></th> 
                <th class="text-center"><?php echo $_translator->_getTranslation('Total cierre');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th> 
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th> 
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Total ventas');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Total retiros');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Total cierre');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>    
                <th></th>
            </tfoot>
        <tbody class="sales_records">
        <?php
        if($_listSalesRecords){
        foreach($_listSalesRecords as $sale){?>
            <tr>
            <td class="text-center" data-id="<?php echo $sale['id']?>"> <?php echo $sale['formated_date']?></td>
            <td class="text-center text-capitalize" data-id="<?php echo $sale['id']?>"> <?php echo $_translator->_getTranslation($sale['store_name']);?></td>
            <td class="text-center" data-id="<?php echo $sale['id']?>"> <?php echo number_format($sale['total_sales'],2);?></td>
            <td class="text-center" data-id="<?php echo $sale['id']?>"> <?php echo number_format($sale['withdrawal'],2);?></td>
            <td class="text-center" data-id="<?php echo $sale['id']?>"> <?php echo number_format($sale['total_close'],2);?></td>
            <td class="text-center" data-id="<?php echo $sale['id']?>"> <?php echo $sale['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $sale['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $sale['id']?>"><i class="fa fa-trash"></i></span>
            </td>
            </tr>
        <?php }
        }?>
        </tbody>
        </table>
     </div>
  </div><!-- /.box-body -->
</div><!-- /.box -->
</section>
<?php include ROOT."/View/Modal/addSalesRecord.php";?>
<?php include ROOT."/View/Modal/advancedSearch.php";?>
<?php 
if(isset($_noValid) || isset($edit)){?> 
    <script>
        $('#modalAddSalesRecord').modal('show');
        $('#btn_allow_edit').hide();
    </script> <?php 
}

if(isset($_disabled) && $_disabled){?>
    <script>
        $('#salesRecordDetails tr td input').addClass('disabled');  
    </script><?php       
} ?>
<script>
    $('#salesRecordDetails tr td a').addClass('disabled');
    $('#store_id').select2();
    $("#date").datetimepicker({format: "MM/DD/YYYY hh:mm A"});
    
    $('#terminar').on('click',function(){updateSalesRecordExpense(function(){submit('salesrecord');});});
    $('._addSalesRecord').on('click',function(){setSalesRecordformToInsert();});
    
    $('._closeModalSalesRecord').on('click',function(){
        clearForm('salesrecord');
        $('.flashmessenger').html('');
        $('#modalAddSalesRecord').modal('hide');
    });
    
    $('#btn_allow_edit').on('click',function(){allowEditSalesRecord();});
    $('#salesRecordDetails').on('click',"tbody tr td a",function(e){ 
        $('#comments_'+$(this).data('id')).val('');
        $('#amount_'+$(this).data('id')).val('');
    });
    
    $('tbody.sales_records td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('salesrecord');
            $('.flashmessenger').html('');
           _getTranslation('Editar registro de venta',function(msj){ $('#title_modal_salesrecord').html(msj);});
            var id = $(this).data('id');
            setDataToEditSalesRecord(id);
        }       
    }); 
    
    $('tbody.sales_records td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteSalesRecord(id);
    });
    
    $('._sumSales,._minusSales').on('blur',function(){
        if($(this).val() == ''){$(this).val('0');}
        sumTotalSales();
    });
    
    $('#tblBrands').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[],
        dom: 'Bfrtip',
            buttons: [{ extend: 'excel', text: 'Descargar en excel'}]
    });
</script>