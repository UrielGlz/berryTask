<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Marcas');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Marcas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addBrand"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar marca')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblBrands" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>  
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="brands">
        <?php
        if($_listBrands){
        foreach($_listBrands as $brand){?>
            <tr>
            <td class="text-center" data-id="<?php echo $brand['id']?>"> <?php echo $brand['description']?></td>
            <td class="text-center" data-id="<?php echo $brand['id']?>"> <?php echo $brand['status_name']?></td>
           <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $brand['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $brand['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addBrand.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddBrand').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addBrand').on('click',function(){
        clearForm('brand');                
        $('form[name=brand] #action').val('insert');
        $('form[name=brand] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar marca',function(msj){ $('#title_modal_brand').html(msj);});
        $('#modalAddBrand').modal('show');
    });
    
    $('._closeModalBrand').on('click',function(){
        clearForm('brand');
        $('.flashmessenger').html('');
        $('#modalAddBrand').modal('hide');
    });
    
    $('tbody.brands td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('brand');
            $('.flashmessenger').html('');
           _getTranslation('Editar marca',function(msj){ $('#title_modal_brand').html(msj);});
            var id = $(this).data('id');
            setDataToEditBrand(id);
        }       
    }); 
    
    $('tbody.brands td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteBrand(id);
    });
    
    $('#tblBrands').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>