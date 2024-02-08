<!-- Modal -->
<div id="modalAddService"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalService"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-database'></i> <span id="title_modal_service"><?php echo $_translator->_getTranslation('Agregar servicio');?></span></h4>
            </div>
                <?php 
                echo $_form->openForm();
                echo $_form->showActionController();
                echo $_form->showId();
                $_form->showElement('type');
                $_form->showElement('inventory')?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                         <div class="tab-content">
                            <div class="col-md-6 col-xs-12">
                                <?php                                    
                                $_form->showElement('code');
                                $_form->showElement('description');
                                $_form->showElement('category');
                                $_form->showElement('unit_of_measurement');
                                $_form->showElement('cost');       
                                $_form->showElement('sale_price');
                                ?>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <?php                                              
                                $_form->showElement('discount');
                                $_form->showElement('taxes');
                                $_form->showElement('taxes_included');
                                $_form->showElement('status');
                                $_form->showElement('comments');
                                ?>           
                            </div>     
                        </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalService" />
                    </div>
                </div>
                <?php echo $_form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <style> /*label.payment_method{margin-top: -5px;}*/</style>
    <script>
        $('#modalAddService').on('shown.bs.modal', function (e) {
            if($('#action').val() === 'insert'){$('#code').focus();}
            
            var input = null;
            $("form[name=product]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('#'+input).focus();}
        });
    </script>    
</div>