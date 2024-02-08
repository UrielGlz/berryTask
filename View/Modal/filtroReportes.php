<!-- Modal -->
<div id="modalFiltroReportes" class="modal fade" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <?php $_form = new ReportForm();
              echo $_form->openForm();
              echo $form->showActionController();
              echo $form->showElement('report');?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo $_translator->_getTranslation('Personalizar reporte');?></h3>
            <div id="infoProducto"></div>
        </div> 
        <div id="flashmessenger-editarProducto"></div>
        <div class="modal-body"> 
            <div class='col-lg-12 col-md-12 col-sm-12'>
                <div id='filtersArea'></div>
            </div>
            <div class="clear"></div>     
        </div>
        <div class="clear"></div>
            <div class="modal-footer">
                <div class="col-lg-2 col-md-2 col-xs-6 pull-left   ">
                    <?php $element = $form->getElement('cerrar');echo $form->createElement($element);?>
                </div>
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right">                
                    <?php $element = $form->getElement('enviar');echo $form->createElement($element); ?>
                </div>
            </div>
        <?php echo $_form->closeForm();?>
    </div>           
    </div>
</div>