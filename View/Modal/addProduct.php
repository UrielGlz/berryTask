<!-- Modal -->
<div id="modalAddProduct"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalProduct"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-database'></i> <span id="title_modal_product"><?php echo $_translator->_getTranslation('Agregar producto');?></span></h4>
            </div>
                <?php 
                echo $_form->openForm();
                echo $_form->showActionController();
                echo $_form->showId();
                $_form->showElement('type');?>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="card">
                        <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>                
                                <li role="presentation"><a href="#more" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Inventario') ?></a></li>                
                        </ul>
                        <br/>
                         <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="general">
                                <div class="col-md-6 col-xs-12">
                                    <?php                                    
                                    $_form->showElement('code');
                                    $_form->showElement('description');
                                    $_form->showElement('category');
                                    $_form->showElement('supplie');                                    
                                    $_form->showElement('masa');    
                                    $_form->showElement('flour');  
                                    $_form->showElement('size');
                                    $_form->showElement('brand');
                                    $_form->showElement('unit_of_measurement');
                                    ?>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <?php                            
                                    $_form->showElement('cost');       
                                    $_form->showElement('sale_price');
                                    $_form->showElement('discount');
                                    $_form->showElement('taxes');
                                    $_form->showElement('taxes_included');
                                    $_form->showElement('show_on_store_request');
                                    $_form->showElement('status');
                                    $_form->showElement('comments');
                                    ?>           
                                </div>     
                            </div>
                        <div role="tabpanel" class="tab-pane" id="more">
                            <div class="col-md-6 col-xs-12">
                                <?php 
                                $_form->showElement('inventory');
                                $_form->showElement('min_stock');
                                $_form->showElement('location');
                                ?>
                            </div>     
                        </div>
                         </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>
                    </div>
                    <div class="pull-left">
                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalProduct" />
                    </div>
                </div>
                <?php echo $_form->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <style> /*label.payment_method{margin-top: -5px;}*/</style>
    <script>
        $('#modalAddProduct').on('shown.bs.modal', function (e) {
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