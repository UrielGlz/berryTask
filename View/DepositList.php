<section class="content-header">
    <h1><i class='fa-fw fa fa-file-text-o'></i> <?php echo $_translator->_getTranslation('Lista de depositos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Home.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
        <li class="active"><?php echo $_translator->_getTranslation('Lista de depositos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools pull-right">
           <a href="Deposit.php" class="btn btn-default"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar deposito')?></a> 
           <span class="btn btn-default _advancedSearch"><i class='fa fa-search-plus'></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage();?></div>
    <div class="clear"></div>
                <div class='table-responsive'>
                <div class="clear"></div>
                <table id="tblDeposits" class="table table-striped table-hover table-condensed font-size-12 datatable_whit_filter_column _responsiveDetails _hideSearch" style="width:100%">
                    <thead>     
                    <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Deposito #');?></th>                                     
                    <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha deposito');?></th>
                    <th class="col-lg-2 col-md-2 text-center"><?php echo $_translator->_getTranslation('Fecha de venta');?></th>                 
                    <th class="text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                    <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>
                    <th class="col-lg-1 col-md-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>
                    </thead>
                    <tfoot>   
                    <th class="filter"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Deposito #');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Fecha deposito');?></th>
                    <th class="filter"><?php echo $_translator->_getTranslation('Fecha de venta');?></th>                 
                    <th class="filter"><?php echo $_translator->_getTranslation('Total');?></th>      
                    <th class="filter"><?php echo $_translator->_getTranslation('Status');?></th>                 
                    <th></th>
                    </tfoot>
                    <tbody>
                    <?php 
                        if($_listDeposits){
                            foreach($_listDeposits as $deposit){?>
                                <tr>  
                                    <td class="text-center wordwrap-breakword"><?php echo $deposit['store_name']?></td>
                                    <td class="text-center"><?php echo $deposit['deposit_number']?></td>
                                    <td class="text-center"><?php echo $deposit['formatedDate']?></td>
                                    <td class="text-center wordwrap-breakword"><?php echo $deposit['sales_date']?></td>                                                                     
                                    <td class="text-center"><?php echo number_format($deposit['total'],2)?></td>        
                                    <td class="text-center"><?php echo $deposit['statusName']?></td>                                                                   
                                    <td class="text-center" style='white-space:nowrap'>
                                        <a class="btn btn-sm btn-default" href="Deposit.php?action=edit&id=<?php echo $deposit['id']?>"><i class="fa fa-pencil"></i></a>                                                                               
                                        <a class="btn btn-sm btn-danger" onclick="return confirmDelete('<?php echo $_translator->_getTranslation('Esta seguro de cancelar este registro ?')?>',this)" href="Deposit.php?action=delete&id=<?php echo $deposit['id']?>"><i class="fa fa-trash"></i></a>
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
    tfoot {
        display: table-header-group;
    }

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
<?php include ROOT."/View/Modal/advancedSearch.php";?>
<script>  
    $('#startDate,#endDate').datetimepicker({format: 'DD/MM/YYYY'});
    $('#tblDeposits').DataTable({
            searching: true,
            paginate: true,
            pageLength: 15,
            filter:true,
            aaSorting:[],
            dom: 'Bfrtip',
            buttons: [{ extend: 'excel', text: 'Descargar en excel'}]
        });
    $('#tblDeposits').removeClass( 'display' ).addClass('table table-striped table-bordered');

    $('#tblDeposits tfoot th.filter').each( function () {
        $(this).html( '<input type="text" placeholder="<?php echo $_translator->_getTranslation('Buscar') ?>"style="width:100%" />' );
    } );
    var table = $('#tblDeposits').DataTable();
    // Apply the search
    table.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search (this.value.replace("/;/g", "|"), true, false)
                    .draw();
            }
        } );
    } );
</script>