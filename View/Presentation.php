<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Presentaciones');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Presentaciones');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addPresentation"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar presentacion')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblPresentations" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
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
        <tbody class="presentations">
        <?php
        if($_listPresentations){
        foreach($_listPresentations as $presentation){?>
            <tr>
            <td class="text-center" data-id="<?php echo $presentation['id']?>"> <?php echo $presentation['description']?></td>
            <td class="text-center" data-id="<?php echo $presentation['id']?>"> <?php echo $presentation['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $presentation['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $presentation['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addPresentation.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddPresentation').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addPresentation').on('click',function(){
        clearForm('presentation');                
        $('form[name=presentation] #action').val('insert');
        $('form[name=presentation] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar presentacion',function(msj){ $('#title_modal_presentation').html(msj);});
        $('#modalAddPresentation').modal('show');
    });
    
    $('._closeModalPresentation').on('click',function(){
        clearForm('presentation');
        $('.flashmessenger').html('');
        $('#modalAddPresentation').modal('hide');
    });
    
    $('tbody.presentations td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('presentation');
            $('.flashmessenger').html('');
           _getTranslation('Editar presentacion',function(msj){ $('#title_modal_presentation').html(msj);});
            var id = $(this).data('id');
            setDataToEditPresentation(id);
        }       
    }); 
    
    $('tbody.presentations td ._delete').on('click',function(){
        var id = $(this).data('id');
        deletePresentation(id);
    });
    
    $('#tblPresentations').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>