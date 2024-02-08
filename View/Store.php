<section class="content-header">
    <h1><i class='fa-fw fa fa-map-marker'></i> <?php echo $_translator->_getTranslation('Sucursales');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Sucursales');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addStore"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar sucursal')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblStores" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Ciudad');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Contacto');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Email');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Sucursal');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Ciudad');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Contacto');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Email');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="stores">
        <?php
        if($_listStores){
        foreach($_listStores as $store){?>
            <tr>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['name']?></td>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['city']?></td>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['contact_name']?></td>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['phone']?></td>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['email']?></td>
            <td class="text-center" data-id="<?php echo $store['id']?>"> <?php echo $store['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $store['id']?>"><i class="fa fa-edit"></i></span>
                <?php 
                if($login->getRole() == '1'){ ?>
                    <span class="btn btn-danger _delete" data-id="<?php echo $store['id']?>"><i class="fa fa-trash"></i></span><?php
                }?>
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
<style>
    label.default_location{
        margin-top: -5px !important;
    }
</style>
<?php include ROOT."/View/Modal/addStore.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddStore').modal('show');</script> <?php }?>
<script>
    
    $('select').select2();
    $('._addStore').on('click',function(){
        clearForm('store');                
        $('form[name=store] #action').val('insert');
        $('form[name=store] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar sucursal',function(msj){ $('#title_modal_store').html(msj);});
        $('#modalAddStore').modal('show');
    });
    
    $('._closeModalStore').on('click',function(){
        clearForm('store');
        $('.flashmessenger').html('');
        $('#modalAddStore').modal('hide');
    });
    
    $('tbody.stores td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('store');
            $('.flashmessenger').html('');
           _getTranslation('Editar sucursal',function(msj){ $('#title_modal_store').html(msj);});
            var id = $(this).data('id');
            setDataToEditStore(id);
        }       
    }); 
    
    $('tbody.stores td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteStore(id);
    });
    
    $('#tblStores').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>