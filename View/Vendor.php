<section class="content-header">
    <h1><i class='fa-fw fa fa-group'></i> <?php echo $_translator->_getTranslation('Proveedores');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Proveedores');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addVendor"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar proveedor')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblVendors" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Direccion');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Email');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Contacto');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Direccion');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Email');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Contacto');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="vendors">
        <?php
        if($_listVendors){
        foreach($_listVendors as $vendor){?>
            <tr>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['name']?></td>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['address_complete']?></td>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['phone']?></td>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['email']?></td>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['contact_name']?></td>
            <td class="text-center" data-id="<?php echo $vendor['id']?>"> <?php echo $vendor['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $vendor['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $vendor['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addVendor.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddVendor').modal('show');</script> <?php }?>
<script>
    
    $('#payment_method,#status').select2();
    $('#payment_method').on('change',function(){
        if(this.value == '1'){$('#credit_days').val('0');$('#credit_days').prop('readOnly',true);} 
        else if(this.value == '2'){$('#credit_days').val('');$('#credit_days').prop('readOnly',false);} 
     });
        
    $('._addVendor').on('click',function(){
        clearForm('vendor');                
        $('form[name=vendor] #action').val('insert');
        $('form[name=vendor] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar proveedor',function(msj){ $('#title_modal_vendor').html(msj);});
        $('#modalAddVendor').modal('show');
    });
    
    $('._closeModalVendor').on('click',function(){
        clearForm('vendor');
        $('.flashmessenger').html('');
        $('#modalAddVendor').modal('hide');
    });
    
    $('tbody.vendors td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('vendor');
            $('.flashmessenger').html('');
           _getTranslation('Editar proveedor',function(msj){ $('#title_modal_vendor').html(msj);});
            var id = $(this).data('id');
            setDataToEditVendor(id);
        }       
    }); 
    
    $('tbody.vendors td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteVendor(id);
    });
    
    $('#tblVendors').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>