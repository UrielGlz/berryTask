<section class="content-header">
    <h1><i class='fa-fw fa fa-database'></i> <?php echo $_translator->_getTranslation('Servicios');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Servicios');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addService"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar servicio')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblServices" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>      
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>      
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="services">
        <?php
        if($_listServices){
        foreach($_listServices as $service){?>
            <tr>
            <td class="text-center" data-id="<?php echo $service['id']?>"> <?php echo $service['code']?></td>
            <td class="text-center text-capitalize" data-id="<?php echo $service['id']?>"> <?php echo $_translator->_getTranslation($service['description']);?></td>
            <td class="text-center" data-id="<?php echo $service['id']?>"> <?php echo $service['category_name']?></td>            
            <td class="text-center" data-id="<?php echo $service['id']?>"> <?php echo $service['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $service['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $service['id']?>"><i class="fa fa-trash"></i></span>
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
<style> label.taxes_included,label.inventory{margin-top: -5px;}</style>
<?php include ROOT."/View/Modal/addService.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddService').modal('show');</script> <?php }?>
<script>

    $('#category,#unit_of_measurement,#taxes,#taxes_included,#status').select2();
    
    $('._addService').on('click',function(){
        clearForm('service');                
        $('form[name=service] #action').val('insert');
        $('form[name=service] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar servicio',function(msj){ $('#title_modal_service').html(msj);});
        $('#modalAddService').modal('show');
    });
    
    $('._closeModalService').on('click',function(){
        clearForm('service');
        $('.flashmessenger').html('');
        $('#modalAddService').modal('hide');
    });
    
    $('tbody.services td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) { 
            clearForm('service');
            $('.flashmessenger').html('');
           _getTranslation('Editar servicio',function(msj){ $('#title_modal_service').html(msj);});
            var id = $(this).data('id');
            setDataToEditService(id);
        }       
    }); 
    
    $('tbody.services td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteService(id);
    });
    
    $('#tblServices').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>