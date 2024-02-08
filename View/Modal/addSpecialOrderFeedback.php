<!-- Modal -->
<div id="modalAddSpecialOrderFeedback" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalSpecialOrderFeedback"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-comments'></i> <span id="title_modal_brand"><?php echo $_translator->_getTranslation('Comentarios de pasteleria');?></span></h4>
            </div>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">
                        <input type="hidden" id="id_special_order_for_feedback"/>
                        
                        <div class="form-group">
                            <textarea id="feedback" class="form-control"></textarea>
                        </div>                        
                    </div>   
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary _saveModalSpecialOrderFeedback'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalSpecialOrderFeedback" />
                    </div>
                </div>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddSpecialOrderFeedback').on('shown.bs.modal', function (e) {$('#feedback').focus();});
    </script>
</div>
