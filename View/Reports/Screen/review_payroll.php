<div class="box box-primary">
<div class="box-header with-border">
    <h3 class="box-title"><i class="fa fa-th-large"></i> <?php echo $_translator->_getTranslation(ucfirst($_reporte->getTituloReporte()));?></h3>
    <div class="box-tools pull-right">
        <!--<a class="btn btn-default" href="<?php echo $_reporte->getStringToSendGET().'&action=create&output=excel' ?>"><i class="fa fa-file-excel-o"></i> <?php echo $_translator->_getTranslation('Exportar a excel')?></a>-->
    </div><!-- /.box-tools -->
</div><!-- /.box-header -->
<div class="box-body">
<?php
$reportResult = $_reporte->getReporte();
if($reportResult == null){
    echo "There is no information";
    exit;
}

$details = $reportResult['data'];
$groupByEmployee = $reportResult['groupByEmployee'];
?>
    <div class="card">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#summary" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Horas trabajadas por empleado') ?></a></li>
            <li role="presentation"><a href="#details" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Detalle de horas trabajadas') ?></a></li>                        
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="margin-top: 20px">
            <div role="tabpanel" class="tab-pane active" id="summary">
                <table id="tblGroupByEmployee" class="table table-striped table-bordered table-hover table-condensed datatable_whit_filter_column _hideSearch">
                    <thead class="table-header">
                    <tr>
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>           
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                    </tr>
                    </thead>
                    <tfoot>                        
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                    </tfoot>
                    <tbody>
                    <?php 
                    foreach($groupByEmployee as $row){?>
                        <tr>
                        <td class="text-center"><?php echo $row['storeName'];?></td>
                        <td class="text-center"><?php echo $row['name'];?></td>  
                        <td class="text-center"><?php echo number_format($row['total'],2);?></td> 
                        </tr><?php  
                    }?>                    
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="details">
                <table id="tblDetails" class="table table-striped table-bordered table-hover table-condensed datatable_whit_filter_column _hideSearch">
                    <thead class="table-header">
                    <tr>
                        <th class="col-md-1 text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                        <th class="col-md-3 text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>         
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Nombre');?></th> 
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Entrada');?></th> 
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Salida');?></th> 
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                        <th class="col-md-2 text-center"><?php echo $_translator->_getTranslation('Aprobado');?></th>
                    </tr>
                    </thead>
                    <tfoot>                        
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Entrada');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Salida');?></th>
                        <th class="filter text-center"><?php echo $_translator->_getTranslation('Total');?></th>
                        <th></th>
                    </tfoot>
                    <tbody>
                    <?php 
                    foreach($details as $user_id => $info){
                        foreach($info as $detail){ 
                            $approved = '';
                            if($detail['approved'] == 'Si'){$approved = 'checked';}?>
                            <tr>
                            <td class="text-center"><?php echo $detail['sucursalName'];?></td>
                            <td class="text-center" data-sort="<?php echo $detail['format_date_for_sort'] ?>"><?php echo $detail['date'];?></td>
                            <td class="text-center"><?php echo $detail['userName'];?></td>      
                            <td class="text-center"><?php echo $detail['check_in'];?></td>
                            <td class="text-center"><?php echo $detail['check_out'];?></td>
                            <td class="text-center"><?php echo number_format($detail['total'],2);?></td>
                            <td class="text-center">
                                <?php 
                                if(!is_null($detail['check_out']) && $detail['check_out'] != ''){?>
                                    <input type="checkbox" class="_approveWorkedHours" data-id="<?php echo $detail['id'] ?>" <?php echo $approved; ?> /><?php
                                }?>                                
                            </td>
                            </tr>
                            <?php                             
                        }  
                    }?>                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<style>
    tfoot {
        display: table-header-group;
    }
    input[type=checkbox]{
            -ms-transform: scale(1.5); /* IE */
            -moz-transform: scale(1); /* FF */
            -webkit-transform: scale(1.5); /* Safari and Chrome */
            -o-transform: scale(1.5); /* Opera */
            transform: scale(1.5);
            padding: 10px;            
        }

      /* Might want to wrap a span around your checkbox text */
      .checkboxtext{
        /* Checkbox text */
        font-size: 110%;
        display: inline;
      }
</style>
<script type="text/javascript" language="javascript">
    $('._approveWorkedHours').on('click',function(){
        var value = $(this).prop('checked');
        
        $.post('/Controller/Ajax.php', {
            action: 'ajax',
            request: 'approveWorkedHours',
            id: $(this).data('id'),
            value: value
        }, function(data) {
            if (data.response){
               return true;
            }
        }, 'json');
    });
    
     $('#tblGroupByEmployee').DataTable({
        paginate:false,
        lengthChange: false,
        filter:true,
        bFilter:true,
        aaSorting:[],
        dom: 'Bfrtip',
        buttons: [{ extend: 'excel', text: 'Descargar en excel'}]
    });
    
    $('#tblDetails').DataTable({
        paginate:false,
        lengthChange: false,
        filter:true,
        bFilter:true,
        aaSorting:[],
        dom: 'Bfrtip',
        buttons: [{ extend: 'excel', text: 'Descargar en excel'}]
    });
    
    $('#tblGroupByEmployee_filter,#tblDetails_filter').hide();
</script>