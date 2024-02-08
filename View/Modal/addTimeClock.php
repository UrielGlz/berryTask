<!-- Modal -->
<div id="modalAddTimeClock"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalTimeClock"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa fa-clock-o'></i> <span id="title_modal_time_clock"><?php echo $_translator->_getTranslation('Agregar checada');?></span></h4>
            </div>
                <?php 
                echo $_form->openForm();
                echo $_form->showActionController();
                echo $_form->showId();?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">
                        <?php
                        $_form->showElement('date');
                        $_form->showElement('id_user');
                        $_form->showElement('check_in');
                        $_form->showElement('check_out');
                        ?>
                    </div>   
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalTimeClock" />
                    </div>
                </div>
                <?php echo $_form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddTimeClock').on('shown.bs.modal', function (e) {$('#description').focus();});
    </script>
</div>