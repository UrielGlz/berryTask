<section class="content-header">
    <h1><i class='fa-fw fa fa-database'></i> <?php echo $_translator->_getTranslation('Productos');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Productos');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addProduct"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar producto')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblProducts" class="table table-bordere table-condensed table-striped table-hoverd datatable_whit_filter_column">
            <thead>
                <th class="text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>   
                <th class="text-center"><?php echo $_translator->_getTranslation('Tipo de masa');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('UM');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Precio sucursal');?></th>
                <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Codigo');?></th>    
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Descripcion');?></th>   
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Tamaño');?></th>   
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Categoria');?></th>      
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Tipo de masa');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('UM');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Precio venta');?></th>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="products">
        <?php
        if($_listProducts){
        foreach($_listProducts as $product){?>
            <tr>
            <td class="text-center" data-id="<?php echo $product['id']?>"> <?php echo $product['code']?></td>
            <td class="text-center text-capitalize"> <?php echo $_translator->_getTranslation($product['description']);?></td>
            <td class="text-center"> <?php echo $product['size_name']?></td>
            <td class="text-center"> <?php echo $product['category_name']?></td>       
            <td class="text-center"> <?php echo $product['masa_name']?></td>       
            <td class="text-center"> <?php echo $product['um_name']?></td>       
            <td class="text-center"> <?php echo $product['sale_price']?></td>       
            <td class="text-center"> <?php echo $product['status_name']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $product['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $product['id']?>"><i class="fa fa-trash"></i></span>
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
<style> label.taxes_included,label.show_on_store_request,label.inventory{margin-top: -5px;}</style>
<?php include ROOT."/View/Modal/addProduct.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddProduct').modal('show');</script> <?php }?>
<script>

    $('#category,#size,#supplie,#masa,#flour,#brand,#presentation,#unit_of_measurement,#taxes,#taxes_included,#show_on_store_request,#status,#inventory,#location').select2();
    
    $('select').on('select2:opening',function(e){
        e.preventDefault();
        if($(this).prop('disabled') === false){$(this).unbind('select2:opening').select2('open');}
    });
    
    $('#category').on('select2:select',function(){
        if($('#category').val() === '<?php echo $_idCategoryForSupplie ?>'){
            $('#supplie').prop('disabled',false);
            
            $('#masa').val('').trigger('change').prop('disabled',true);
            $('#masa').prop('disabled',true);
            
            $('#flour').val('').trigger('change').prop('disabled',true);
            $('#flour').prop('disabled',true);
        
        }else if($('#category').val() === '<?php echo $_idCategoryForPanaderia ?>'){
            $('#masa').prop('disabled',false);
            $('#flour').prop('disabled',false);
            
            $('#supplie').val('').trigger('change').prop('disabled',true);
            $('#supplie').prop('disabled',true);
        }else{
            $('#supplie').val('').trigger('change').prop('disabled',true);
            $('#supplie').prop('disabled',true);
            
            $('#masa').val('').trigger('change').prop('disabled',true);
            $('#masa').prop('disabled',true);
            
            $('#flour').val('').trigger('change').prop('disabled',true);
            $('#flour').prop('disabled',true);
        }
    });
    
    $('._addProduct').on('click',function(){
        clearForm('product');                
        $('form[name=product] #action').val('insert');
        $('form[name=product] #id').val('');
        $('.flashmessenger').html('');
         _getTranslation('Agregar producto',function(msj){ $('#title_modal_product').html(msj);});
        $('#modalAddProduct').modal('show');
    });
    
    $('._closeModalProduct').on('click',function(){
        clearForm('product');
        $('.flashmessenger').html('');
        $('#modalAddProduct').modal('hide');
    });
    
    $('tbody.products td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('product');
            $('.flashmessenger').html('');
           _getTranslation('Editar producto',function(msj){ $('#title_modal_product').html(msj);});
            var id = $(this).data('id');
            setDataToEditProduct(id);
        }       
    }); 
    
    $('tbody.products td ._delete').on('click',function(){
        var id = $(this).data('id');
        deleteProduct(id);
    });
    
    $('#tblProducts').DataTable({
        paginate:false,
        filter:true,
        aaSorting:[]
    });
</script>
