<!-- Modal -->
<div id="modalBusquedaAvanzadaSR" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><i class="fa fa-search-plus" ></i> <?php echo $_translator->_getTranslation('Busqueda')?></h3>
        </div>
            <?php 
            $_formBuscar = new SpecialOrderBuscarForm();
            #$controller declarada en Controller 
            $_formBuscar->setActionForm($controller.'.php');
            echo $_formBuscar->openForm();
            echo $_formBuscar->showActionController();?>
            <div class="modal-body">
                <div class="col-lg-10 col-lg-offset-1 col-md-10 col-lg-offset-1 col-xs-12">
                    <?php $_formBuscar->showElement('store_id');?>
                    <?php $_formBuscar->showElement('startDate');?>
                    <?php $_formBuscar->showElement('endDate');?>
                    <?php $_formBuscar->showElement('customer');?>
                    <?php $_formBuscar->showElement('home_service');?>
                    <?php $_formBuscar->showElement('status');?>
                    <?php $_formBuscar->showElement('status_production');?>
                </div>
            </div>
            <div class="clear"></div>
            <div class="modal-footer">
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right  p-l-0 ">
                    <?php $element = $_formBuscar->getElement('cerrar');echo $_formBuscar->createElement($element);?>
                </div>
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right p-r-0">                
                    <?php $element = $_formBuscar->getElement('search');echo $_formBuscar->createElement($element); ?>
                </div>
            </div>
        </div>
        <?php echo $_formBuscar->closeForm();?>
    </div>
</div>
<style>
    label.home_service,
    label.status_production{
        margin-top:-5px;
    }
</style>
<script>
    $('#store_id,#home_service,#status,#status_production').select2();
    $('#startDate,#endDate').datetimepicker({format: 'MM/DD/YYYY'});
</script>
