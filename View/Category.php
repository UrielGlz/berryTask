<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Categorias');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Categorias');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addCategory"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar categoria')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblCategories" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Tipo');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="colmd-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Tipo');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="categorys">
        <?php
        if($_listCategories){
        foreach($_listCategories as $category){?>
            <tr>
            <td class="text-center" data-id="<?php echo $category['id']?>"> <?php echo $category['description']?></td>
            <td class="text-center text-capitalize" data-id="<?php echo $category['id']?>"> <?php echo $_translator->_getTranslation($category['type']);?></td>
            <td class="text-center" data-id="<?php echo $category['id']?>"> <?php echo $category['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $category['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $category['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addCategory.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddCategory').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addCategory').on('click',function(){
        clearForm('category');                
        $('form[name=category] #action').val('insert');
        $('form[name=category] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar categoria',function(msj){ $('#title_modal_category').html(msj);});
        $('#modalAddCategory').modal('show');
    });
    
    $('._closeModalCategory').on('click',function(){
        clearForm('category');
        $('.flashmessenger').html('');
        $('#modalAddCategory').modal('hide');
    });
    
    $('tbody.categorys td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('category');
            $('.flashmessenger').html('');
           _getTranslation('Editar categoria',function(msj){ $('#title_modal_category').html(msj);});
            var id = $(this).data('id');
            setDataToEditCategory(id);
        }       
    }); 
    
    $('tbody.categorys td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteCategory(id);
    });
    
    $('#tblCategories').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>