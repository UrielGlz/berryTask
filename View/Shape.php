<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Formas');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Formas');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addShape"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar forma')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblShapes" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
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
        <tbody class="shapes">
        <?php
        if($_listShapes){
        foreach($_listShapes as $shape){?>
            <tr>
            <td class="text-center" data-id="<?php echo $shape['id']?>"> <?php echo $shape['description']?></td>
            <td class="text-center" data-id="<?php echo $shape['id']?>"> <?php echo $shape['status_name']?></td>
           <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $shape['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $shape['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addShape.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddShape').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addShape').on('click',function(){
        clearForm('shape');                
        $('form[name=shape] #action').val('insert');
        $('form[name=shape] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar forma',function(msj){ $('#title_modal_shape').html(msj);});
        $('#modalAddShape').modal('show');
    });
    
    $('._closeModalShape').on('click',function(){
        clearForm('shape');
        $('.flashmessenger').html('');
        $('#modalAddShape').modal('hide');
    });
    
    $('tbody.shapes td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('shape');
            $('.flashmessenger').html('');
           _getTranslation('Editar forma',function(msj){ $('#title_modal_shape').html(msj);});
            var id = $(this).data('id');
            setDataToEditShape(id);
        }       
    }); 
    
    $('tbody.shapes td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteShape(id);
    });
    
    $('#tblShapes').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>