<section class="content-header">
    <h1><i class='fa-fw fa fa-clock-o'></i> <?php echo $_translator->_getTranslation('Reloj checador');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Reloj checador');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default _addTimeClock"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar checada')?></span> 
      <span class="btn btn-default" data-toggle='modal' data-target='#modalBusquedaAvanzadaTimeClock'><i class="fa fa-search-plus" ></i> <?php echo $_translator->_getTranslation('Busqueda avanzada');?></span>                
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblPunchesClock" class="table table-condensed table-striped table-hover table-bordered datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Hora inicio');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Hora final');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Total horas');?></th>
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Fecha');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Hora inicio');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Hora fin');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Total horas');?></th>   
                <th></th>
            </tfoot>
        <tbody class="punchesClock">
        <?php 
        if($_listaTimeClocks){
            foreach($_listaTimeClocks as $timeClock){?>
                <tr>
                    <td class="text-center"><?php echo $timeClock['formated_date']?></td>
                    <td class="text-center"><?php echo $timeClock['userName']?></td>
                    <td class="text-center"><?php echo $timeClock['sucursalName']?></td>
                    <td class="text-center"><?php echo $timeClock['check_in']?></td>
                    <td class="text-center"><?php echo $timeClock['check_out']?></td>
                    <td class="text-center"><?php echo number_format($timeClock['total'],2)?></td>                    
                    <td class="text-center">
                       <span class="btn btn-default _edit" data-id="<?php echo $timeClock['id']?>"><i class="fa fa-pencil"></i></span>
                       <span class="btn btn-danger _delete" data-id="<?php echo $timeClock['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addTimeClock.php";?>
<?php include ROOT."/View/Modal/BusquedaAvanzadaTimeClock.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddTimeClock').modal('show');</script> <?php }?>

<script>
    
    $('#id_user').select2();
    $('#date').datetimepicker({format: "MM/DD/YYYY"});
    $('#check_in,#check_out').datetimepicker({format: "MM/DD/YYYY hh:mm A "});
    $('._addTimeClock').on('click',function(){
        clearForm('time_clock');                
        $('form[name=time_clock] #action').val('insert');
        $('form[name=time_clock] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar checada',function(msj){ $('#title_modal_timeClock').html(msj);});
        $('#modalAddTimeClock').modal('show');
    });
    
    $('._closeModalTimeClock').on('click',function(){
        clearForm('size');
        $('.flashmessenger').html('');
        $('#modalAddTimeClock').modal('hide');
    });
    
    $('tbody.punchesClock td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('size');
            $('.flashmessenger').html('');
           _getTranslation('Editar checada',function(msj){ $('#title_modal_timeClock').html(msj);});
            var id = $(this).data('id');
            setDataToEditTimeClock(id);
        }       
    }); 
    
    $('tbody.punchesClock td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteTimeClock(id);
    });
    
    $('#tblPunchesClock').DataTable({
        paginate:false,
        filter:true,
        bFilter:true,
        aaSorting:[]
    });
</script>
