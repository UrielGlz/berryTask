<!-- Modal -->
<div id="modalGadgetTimeClock"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-clock-o'></i> <span><?php echo $_translator->_getTranslation('Reloj checador');?></span></h4>
            </div>
            <div class="box-body">
                <div id='flashmessenger-gadgetTimeclock' style="height: 70px"><?php $flashmessenger->showMessage(true);?></div>
                <div class="col-md-12 col-xs-12" style="margin-bottom: 70px">
                    <div class="input-group">
                        <input type="password" id="nip_user" class="form-control text-center" placeholder="<?php echo $_translator->_getTranslation('NIP de empleado');?>" />
                        <span class="input-group-btn">
                        <button id="btn_punch" class="btn btn-primary" type="button"><span class="fa fa-download" aria-hidden="true">
                        </span> <?php echo $_translator->_getTranslation('Checar');?></button>
                        </span>
                    </div>
                </div>   
            </div>
            <div class="box-footer">
                <div class="pull-right">
                     <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo $_translator->_getTranslation('Cerrar')?></button>
                </div>
            </div>
            </div>
        </div>
    </div>           
    </div>
    <script type="text/javascript">      
        $('#nip_user').on('keypress',function(e) {
            if(e.which === 13) {
               setPunchTimeClock();
            }
        });
        
        $('#btn_punch').on('click',function(){
            setPunchTimeClock();
        });
        
        $('#modalGadgetTimeClock').on('shown.bs.modal',function(){
            $('#flashmessenger-gadgetTimeclock').html('');
            $('#nip_user').focus();
        });
        
        $('#modalGadgetTimeClock').on('hidden.bs.modal',function(){
            $('#flashmessenger-gadgetTimeclock').html('');
            $('#nip_user').val('');
        });
    </script>
</div>
