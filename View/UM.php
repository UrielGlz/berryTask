<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Unidades de medida');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Unidades de medida');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addUM"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar unidad de medida')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblUM" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Abreviacion');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Abreviacion');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="um">
        <?php
        if($_listUMs){
        foreach($_listUMs as $um){?>
            <tr>
            <td class="text-center" data-id="<?php echo $um['id']?>"> <?php echo $um['description']?></td>
            <td class="text-center" data-id="<?php echo $um['id']?>"> <?php echo $_translator->_getTranslation($um['abbreviation']);?></td>
            <td class="text-center" data-id="<?php echo $um['id']?>"> <?php echo $um['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $um['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $um['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addUM.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddUM').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addUM').on('click',function(){
        clearForm('um');                
        $('form[name=um] #action').val('insert');
        $('form[name=um] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar unidad de medida',function(msj){ $('#title_modal_um').html(msj);});
        $('#modalAddUM').modal('show');
    });
    
    $('._closeModalUM').on('click',function(){
        clearForm('um');
        $('.flashmessenger').html('');
        $('#modalAddUM').modal('hide');
    });
    
    $('tbody.um td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('um');
            $('.flashmessenger').html('');
           _getTranslation('Editar unidad de medida',function(msj){ $('#title_modal_um').html(msj);});
            var id = $(this).data('id');
            setDataToEditUM(id);
        }       
    }); 
    
    $('tbody.um td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteUM(id);
    });
    
    $('#tblUM').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>