<!-- Modal -->
<div id="modalAddPaymentTerms"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalPaymentTerms"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_paymentterms"><?php echo $_translator->_getTranslation('Agregar termino de pago');?></span></h4>
            </div>
                <?php 
                if(!isset($_paymentForm)){$_paymentForm = new PaymentTermsForm();}        
                echo $_paymentForm->openForm();
                echo $_paymentForm->showActionController();
                echo $_paymentForm->showId();?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">
                        <?php
                        $_paymentForm->showElement('name');
                        $_paymentForm->showElement('days');
                        ?>
                    </div>   
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary _savePaymentTerms'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalPaymentTerms" />
                    </div>
                </div>
                <?php echo $_paymentForm->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddPaymentTerms').on('shown.bs.modal', function (e) {
            if($('form[name=paymentterms] #action').val() === 'insert'){$('form[name=paymentterms] #name').focus();}
            
            var input = null;
            $("form[name=paymentterms]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('form[name=paymentterms] #'+input).focus();}
        });
        
        $('._addPaymentTerms').on('click',function(){
            clearForm('paymentterms');                
            $('form[name=paymentterms] #action').val('insert');
            $('form[name=paymentterms] #id').val('');
            $('.flashmessenger').html('');
             _getTranslation('Agregar Termino de pago',function(msj){ $('#title_modal_paymentterms').html(msj);});
            $('#modalAddPaymentTerms').modal('show');
        });
    
        $('._closeModalPaymentTerms').on('click',function(){
            clearForm('paymentterms');
            $('.flashmessenger').html('');
            $('#modalAddPaymentTerms').modal('hide');
        });
    </script>
</div>
