<!-- Modal -->
<div id="modalAddPurchaseProduct" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalAddProduct"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-database'></i> <span id="title_modal_purchaseProduct"><?php echo $_translator->_getTranslation('Agregar producto');?></span></h4>
            </div>
                <?php 
                $formAddProduct = new AddProductForm();
                echo $formAddProduct->openForm(null);
                echo $formAddProduct->showActionController();
                echo $formAddProduct->showId();?>
                
                <div class="box-body">
                    <div class='flashmessenger_modal_add_product'><?php $flashmessenger->showMessage(true);?></div>
                    <?php $formAddProduct->showElement('idDetailTemp');?>
                    <?php $formAddProduct->showElement('id_product');?>
                    <div class="col-md-6 col-xs-12">
                        <?php $formAddProduct->showElement('product');?>
                        <?php $formAddProduct->showElement('quantity');?>
                        <?php $formAddProduct->showElement('cost');?>
                        <?php $formAddProduct->showElement('discount');?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <?php $formAddProduct->showElement('taxes');?>
                        <?php $formAddProduct->showElement('taxes_included');?>
                        <?php $formAddProduct->showElement('expiration_date');?>
                    </div>     
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <?php $element = $formAddProduct->getElement('buscar');echo $formAddProduct->createOnlyElement($element);?>
                    </div>
                    <div class="pull-left">
                        <?php $element = $formAddProduct->getElement('cerrar_modal');echo $formAddProduct->createOnlyElement($element);?>
                    </div>
                </div>
                <?php echo $formAddProduct->closeForm();?>
            </div>
        </div>
    </div>           
    </div>
    <script>
        $('#modalAddPurchaseProduct').on('shown.bs.modal', function (e) {
            if($('#action').val() === 'insert'){$('#product').focus();}
            
            var input = null;
            $("form[name=addProduct]").find('div').each(function() {
                if($(this).hasClass('has-error') === true){
                    input = $(this).data('errorfor');
                    return false;
                }
            });    
            
            if(input !== null){$('#'+input).focus();}
        });
    </script>
</div>