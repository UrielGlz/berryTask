<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Locaciones');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Locaciones');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addLocation"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar locacion')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblLocations" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>   
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="locations">
        <?php
        if($_listLocations){
        foreach($_listLocations as $location){?>
            <tr>
            <td class="text-center" data-id="<?php echo $location['id']?>"> <?php echo $location['description']?></td>
            <td class="text-center" data-id="<?php echo $location['id']?>"> <?php echo $location['store_name']?></td>
            <td class="text-center" data-id="<?php echo $location['id']?>"> <?php echo $location['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $location['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $location['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addLocation.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddLocation').modal('show');</script> <?php }?>
<script>
    
    $('#store_id,#status').select2();
    $('._addLocation').on('click',function(){
        clearForm('location');                
        $('form[name=location] #action').val('insert');
        $('form[name=location] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar locacion',function(msj){ $('#title_modal_location').html(msj);});
        $('#modalAddLocation').modal('show');
    });
    
    $('._closeModalLocation').on('click',function(){
        clearForm('location');
        $('.flashmessenger').html('');
        $('#modalAddLocation').modal('hide');
    });
    
    $('tbody.locations td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('location');
            $('.flashmessenger').html('');
           _getTranslation('Editar locacion',function(msj){ $('#title_modal_location').html(msj);});
            var id = $(this).data('id');
            setDataToEditLocation(id);
        }       
    }); 
    
    $('tbody.locations td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteLocation(id);
    });
    
    $('#tblLocations').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>