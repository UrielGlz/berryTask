<!-- Modal -->
<div id="modalAddSliceToSpecialOrder" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalAddSliceToSpecialOrder"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-tags'></i> <span id="title_modal_AddSliceToSpecialOrder"><?php echo $_translator->_getTranslation('Agregar pastel de vitrina');?></span></h4>
            </div>
                <div class="box-body">
                    <div class='flashmessenger_addSliceToSpecialOrder'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">
                        <div class="hidden">
                            <?php 
                                $form->showElement('idDetailTemp');                        
                                $form->showElement('type');                       
                                $form->showElement('shape');
                                $form->showElement('category');                 
                                $form->showElement('multiple');
                            ?>
                        </div>
                         <?php 
                            $form->showElement('size');
                            $form->showElement('product');
                            $form->showElement('quantity');
                            if($form->getValueElement('role_logued')!=='1'){$form->hideElement(array('price'));}
                            $form->showElement('price');                            
                            $form->showElement('number_of_cake');
                            $form->showElement('comments_cake');
                        ?> 
                    </div>   
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <?php $element = $form->getElement('buscar');echo $form->createOnlyElement($element);?>
                    </div>
                    <div class="pull-left">
                        <?php $element = $form->getElement('cerrar_modal');echo $form->createOnlyElement($element);?>
                    </div>
                </div>
            </div>
        </div>
    </div>           
    </div>
    <script>
    </script>
</div>
