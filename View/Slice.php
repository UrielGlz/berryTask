<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Partes del pastel');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Partes del pastel');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addSlice"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar parte del pastel')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblSlices" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Id');?></th>  
                <th class="text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>  
                <th class="text-center"><?php echo $_translator->_getTranslation('Sabor');?></th>  
                <th class="text-center"><?php echo $_translator->_getTranslation('Forma');?></th> 
                <th class="text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>  
                <th class="text-center"><?php echo $_translator->_getTranslation('Precio');?></th>
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Id');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Sabor');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Forma');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Precio');?></th>
                <th></th>    
            </tfoot>
        <tbody class="slices">
        <?php
        if($_listSlices){
        foreach($_listSlices as $size){?>
            <tr>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['id']?></td>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['category_name']?></td>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['flavor']?></td>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['shape_name']?></td>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['size_name']?></td>
            <td class="text-center" data-id="<?php echo $size['id']?>"> <?php echo $size['price']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $size['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $size['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addSlice.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddSlice').modal('show');</script> <?php }?>
<script>
    
    $('#category,#size,#shape,#status').select2();
    $('._addSlice').on('click',function(){
        clearForm('slice');                
        $('form[name=slice] #action').val('insert');
        $('form[name=slice] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar Parte del pastel',function(msj){ $('#title_modal_slice').html(msj);});
        $('#modalAddSlice').modal('show');
    });
    
    $('._closeModalSlice').on('click',function(){
        clearForm('slice');
        $('.flashmessenger').html('');
        $('#modalAddSlice').modal('hide');
    });
    
    $('tbody.slices td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('slice');
            $('.flashmessenger').html('');
           _getTranslation('Editar Parte del pastel',function(msj){ $('#title_modal_slice').html(msj);});
            var id = $(this).data('id');
            setDataToEditSlice(id);
        }       
    }); 
    
    $('tbody.slices td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteSlice(id);
    });
    
    $('#tblSlices').DataTable({
        paginate:true,
        pageLength: 100,
        filter:true,
        aaSorting:[]
    });
</script>