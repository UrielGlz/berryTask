<!-- Modal -->
<div id="modalAddSalesRecord" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalSalesRecord"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_salesrecord"><?php echo $_translator->_getTranslation('Agregar registro de venta');?></span></h4>
            </div>
                <?php 
                echo $form->openForm();
                echo $form->showActionController();
                echo $form->showId();?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-5 col-xs-12">
                        <?php
                        $form->showElement('allow_edit');
                        $form->showElement('store_id');
                        $form->showElement('date');
                        $form->showElement('initial_cash');
                        $form->showElement('final_cash');
                        $form->showElement('debit_card');
                        $form->showElement('credit_card');
                        $form->showElement('check');
                        $form->showElement('stamp');
                        $form->showElement('withdrawal');
                        ?>
                        <hr/>
                        <div class="form-group">
                            <label class="control-label col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php echo $_translator->_getTranslation('Total')?></label>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right _totalSales"></div>
                        </div>                        
                    </div> 
                    <div>
                    <?php $salesRecordAjax = new SalesRecordAjax();?>
                    <div class="col-md-7">
                        <label><?php echo $_translator->_getTranslation('Notas')?></label>
                        <?php $element = $form->getElement('comments');?>
                        <div class="form-group"><?php echo $form->createOnlyElement($element);?> </div>
                        
                        <?php $listSalesRecordExpensesDetalles = $salesRecordAjax->getListSalesRecordExpensesDetalles($form->getTokenForm());?>
                        <h4><?php echo $_translator->_getTranslation('Detalle de retiros en efectivo');?></h4>
                        <div class="table-responsive">
                        <table id='salesRecordDetails' class="table table-condensed table-striped table-hover font-size-11">
                            <thead>                
                                <th class="col-md-5 text-center"><?php echo $_translator->_getTranslation('Concepto')?></th>                                
                                <th class="col-md-4 text-center"><?php echo $_translator->_getTranslation('Notas')?></th>
                                <th class="col-md-2 text-right"><?php echo $_translator->_getTranslation('Monto')?></th>
                                <th class="col-md-1 text-center"></th>
                            </thead>
                            <tbody>
                                <?php echo $listSalesRecordExpensesDetalles['salesRecordExpenseDetails'];?>
                            </tbody>
                        </table>   
                        </div>
                    </div>  
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="button" id='terminar' value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalSalesRecord" />                        
                        <input type="button" id="btn_allow_edit" name="btn_allow_edit" value="<?php echo $_translator->_getTranslation('Permiso para editar')?>" class='btn btn-default'/>
                    </div>
                </div>
                <?php echo $form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddSalesRecord').on('shown.bs.modal', function (e) {
            if($('#action').val() === 'insert'){$('#initial_cash').focus();}
            
            var input = null;
            $("form[name=salesrecord]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('#'+input).focus();}
        });
    </script>
</div>
