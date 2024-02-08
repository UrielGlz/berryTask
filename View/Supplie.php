<section class="content-header">
    <h1><i class='fa-fw fa fa-list'></i> <?php echo $_translator->_getTranslation('Insumos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Insumos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addSupplie"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar insumo')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblSupplie" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">
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
        <tbody class="supplie">
        <?php
        if($_listSupplies){
        foreach($_listSupplies as $supplie){?>
            <tr>
            <td class="text-center" data-id="<?php echo $supplie['id']?>"> <?php echo $supplie['description']?></td>
            <td class="text-center" data-id="<?php echo $supplie['id']?>"> <?php echo $supplie['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $supplie['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $supplie['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addSupplie.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddSupplie').modal('show');</script> <?php }?>
<script>
    
    $('#type,#status').select2();
    $('._addSupplie').on('click',function(){
        clearForm('supplie');                
        $('form[name=supplie] #action').val('insert');
        $('form[name=supplie] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar insumo',function(msj){ $('#title_modal_supplie').html(msj);});
        $('#modalAddSupplie').modal('show');
    });
    
    $('._closeModalSupplie').on('click',function(){
        clearForm('supplie');
        $('.flashmessenger').html('');
        $('#modalAddSupplie').modal('hide');
    });
    
    $('tbody.supplie td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('supplie');
            $('.flashmessenger').html('');
           _getTranslation('Editar insumo',function(msj){ $('#title_modal_supplie').html(msj);});
            var id = $(this).data('id');
            setDataToEditSupplie(id);
        }       
    }); 
    
    $('tbody.supplie td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteSupplie(id);
    });
    
    $('#tblSuoplie').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>