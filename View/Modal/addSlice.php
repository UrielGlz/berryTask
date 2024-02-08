<!-- Modal -->
<div id="modalAddSlice"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalSlice"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_slice"><?php echo $_translator->_getTranslation('Agregar parte del pastel');?></span></h4>
            </div>
                <?php 
                echo $form->openForm();
                echo $form->showActionController();
                echo $form->showId();?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-6 col-xs-12">
                        <?php
                        $form->showElement('category');
                        $form->showElement('size');
                        $form->showElement('shape');
                        $form->showElement('flavor');
                        $form->showElement('price');
                        $form->showElement('status');
                        ?>
                    </div>   
                    <div class="col-md-6 col-xs-12">                        
                        <label><?php echo $_translator->_getTranslation('Detalles')?></label>
                        <?php $element = $form->getElement('comments');?>
                        <div class="form-group"><?php echo $form->createOnlyElement($element);?> </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalSlice" />
                    </div>
                </div>
                <?php echo $form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddSlice').on('shown.bs.modal', function (e) {
            if($('#action').val() === 'insert'){$('#description').focus();}
            
            var input = null;
            $("form[name=slice]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('#'+input).focus();}
        });
    </script>
</div>
