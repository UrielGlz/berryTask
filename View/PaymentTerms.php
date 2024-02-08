<section class="content-header">
    <h1><i class='fa-fw fa fa-tags'></i> <?php echo $_translator->_getTranslation('Terminos de pago');?></small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>
      <li class="active"><?php echo $_translator->_getTranslation('Terminos de pago');?></li>
    </ol>
</section>
<section class="content">
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"></h3>
    <div class="box-tools pull-right">
      <span class="btn btn-default pull-right _addPaymentTerms"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar Termino de pago')?></span> 
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>
     <div class="clear"></div>
     <div class="table-responsive">
        <table id="tblPaymentTerms" class="table table-bordered table-condensed table-striped table-hover font-size-12 datatable_whit_filter_column _hideSearch" style="width:100%">
            <thead>
                <th class="col-md-1 col-xs-1 text-center"text-center"><?php echo $_translator->_getTranslation('No');?></th> 
                <th class="text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    
                <th class="text-center"><?php echo $_translator->_getTranslation('Dias');?></th>
                <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    
            </thead>
            <tfoot>
                <th class="filter text-center"><?php echo $_translator->_getTranslation('No');?></th>  
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>   
                <th class="filter text-center"><?php echo $_translator->_getTranslation('Dias');?></th>   
                <th></th>    
            </tfoot>
        <tbody class="paymentterms">
        <?php
        if($_listPaymentTerms){
        foreach($_listPaymentTerms as $paymentTerm){?>
            <tr>
            <td class="text-center" data-id="<?php echo $paymentTerm['id']?>"> <?php echo $paymentTerm['id']?></td>
            <td class="text-center" data-id="<?php echo $paymentTerm['id']?>"> <?php echo $paymentTerm['name']?></td>
            <td class="text-center" data-id="<?php echo $paymentTerm['id']?>"> <?php echo $paymentTerm['days']?></td>
            <td class="text-center">
                <span class="btn btn-default _edit" data-id="<?php echo $paymentTerm['id']?>"><i class="fa fa-edit"></i></span>
                <span class="btn btn-danger _delete" data-id="<?php echo $paymentTerm['id']?>"><i class="fa fa-trash"></i></span>
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
<?php include ROOT."/View/Modal/addPaymentTerms.php";?>
<?php if(isset($_noValid)){?> <script>$('#modalAddPaymentTerms').modal('show');</script> <?php }?>
<script>
    $('tbody.paymentterms td ._edit').on('click',function(e){
        if (!$(e.target).closest('._delete').length) {
            clearForm('paymentterms');
            $('.flashmessenger').html('');
           _getTranslation('Editar Termino de pago',function(msj){ $('#title_modal_paymentterms').html(msj);});
            var id = $(this).data('id');
            setDataToEditPaymentTerms(id);
        }       
    }); 
    
    $('tbody.paymentterms td ._delete').on('click',function(){
        var id = $(this).data('id');
        deletePaymentTerms(id);
    });
    
    $('._savePaymentTerms').on('click',function(){submit('paymentterms');});
</script>