<!-- Modal -->
<div id="modalAddStore"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalStore"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-map-marker'></i> <span id="title_modal_store"><?php echo $_translator->_getTranslation('Agregar sucursal');?></span></h4>
            </div>
                <?php 
                echo $form->openForm();
                echo $form->showActionController();
                echo $form->showId();?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-6 col-xs-12">
                        <?php
                        $form->showElement('name');
                        $form->showElement('address');
                        $form->showElement('city');
                        $form->showElement('state');
                        $form->showElement('zipcode');
                        ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <?php
                        $form->showElement('contact_name');
                        $form->showElement('phone');
                        $form->showElement('fax');
                        $form->showElement('email');
                        $form->showElement('default_location');
                        /*$form->showElement('webpage');*/
                        $form->showElement('status');
                        ?>           
                    </div>     
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalStore" />
                    </div>
                </div>
                <?php echo $form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddStore').on('shown.bs.modal', function (e) {
            if($('#action').val() === 'insert'){$('#name').focus();}
            
            var input = null;
            $("form[name=store]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('#'+input).focus();}
        });
    </script>
</div>