<!-- Modal -->
<div id="modalAddDeposit" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalAddDeposit"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-database'></i> <span id="title_modal_deposit"><?php echo $_translator->_getTranslation('Agregar producto a cotizacion');?></span></h4>
            </div>                
                <div class="box-body">
                    <?php 
                    $formAddDepositDetail = new AddDepositDetailForm();
                    echo $formAddDepositDetail->openForm(null);
                    echo $formAddDepositDetail->showActionController();
                    echo $formAddDepositDetail->showId();
                    $formAddDepositDetail->showElement('idDetailTemp');?>    
                    
                    <div class='flashmessenger_modal_add_product'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">                       
                        <?php $formAddDepositDetail->showElement('sale_date');?>    
                        <?php $formAddDepositDetail->showElement('sale_date_final');?> 
                        <?php $formAddDepositDetail->showElement('sale_total_cash');?>               
                        <?php $formAddDepositDetail->showElement('sale_comments');?>                   
                    </div> 
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <?php $element = $formAddDepositDetail->getElement('save_deposit_detail');echo $formAddDepositDetail->createOnlyElement($element);?>
                    </div>
                    <div class="pull-left">
                        <?php $element = $formAddDepositDetail->getElement('cerrar_modal');echo $formAddDepositDetail->createOnlyElement($element);?>
                    </div>
                </div>
                <?php echo $formAddDepositDetail->closeForm();?>
            </div>
        </div>
    </div>        
    </div>
    <script>
        $("#sale_date,#sale_date_final").datetimepicker({format: 'MM/DD/YYYY'});
        $('#addDepositDetail').on('click',function(){        
            clearModalAddDepositDetail();
            _getTranslation('Agregar detalle',function(msj){ $('#title_modal_deposit').html(msj);});
            $('#modalAddDeposit').modal('show');
        });    

        $('._saveDepositDetail').on('click',function(){setDepositDetails();});
        $('._closeModalAddDeposit').on('click',function(){clearModalAddProduct();$('#modalAddDeposit').modal('hide');});
  
    </script>
</div>