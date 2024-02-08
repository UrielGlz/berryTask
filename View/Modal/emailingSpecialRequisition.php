<!-- Modal -->
<div id="modalEmailingSpecialRequisition" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php 
            $formMail = new EmailingForm();
            echo $formMail->openForm();
            ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><i class="fa fa-envelope"></i> <?php echo $_translator->_getTranslation('Enviar por correo')?></h3>
                </div>
                <div id="flashmessenger-invoice"></div>
                <div class="modal-body">            
                    <?php echo $formMail->showElement('id_special_requisition');?>
                    <?php echo $formMail->showElement('to');?>
                    <?php $formMail->showElement('cc');?>
                    <?php $formMail->showElement('subject');?>
                    <?php $formMail->showElement('message');?>
                </div>
                <div class="modal-footer">
                    <div class="pull-right"><?php $formMail->showElement('send_emailing');?></div>
                </div>
            <?php echo $formMail->closeForm();?>
        </div>
    </div>
</div>
<!-- Fin Modal -->