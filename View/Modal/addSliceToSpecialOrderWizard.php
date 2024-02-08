<!-- Modal -->
<div id="modalAddSliceToSpecialOrderWizard" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog modal-wizard-specialOrder" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <div class="box box-primary">
                <div class="box-header with-border">
                    <button type="button" class="close _closeModalAddSliceToSpecialOrderWizard"><i class="fa fa-window-close"></i></button>
                    <h4><i class='fa-fw fa fa-birthday-cake'></i> <span id="title_modal_AddSliceToSpecialOrderWizard"><?php echo $_translator->_getTranslation('Agregar pastel');?></span></h4>
                </div>
                <div class="box-body">
                    <div class='flashmessenger_addSliceToSpecialOrder'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-8 col-xs-12">
                        <h3 class="_circule_number_wizard">1</h3> <h3 style="display: inline-block"><?php echo $_translator->_getTranslation('Cantidad de personas');?></h3>
                        <div class="_wizard_sizes">
                            <?php 
                            $sizesRepo = new SizeRepository(); 
                            $sizesList = $sizesRepo->getListSizes();
                            $i = 0;
                            foreach($sizesList as $size){
                                echo "<div class='col-md-2 col-xs-12 text-center _box_option_wizard _size' data-propierty='size' data-sizeid='{$size['id']}'>{$size['description']}</div>";
                            }
                            ?>
                        </div>
                    </div>   
                    <div class="col-md-4 col-xs-12">
                        <h3 class="_circule_number_wizard">2</h3> <h3 style="display: inline-block"><?php echo $_translator->_getTranslation('Forma del pastel');?></h3>
                        <div class="_wizard_shape"></div>
                    </div>
                    <div class="clear"></div>
                    <br/>
                    <div class="col-md-4 col-xs-12">
                        <h3 class="_circule_number_wizard">3</h3> <h3 style="display: inline-block"><?php echo $_translator->_getTranslation('Pan');?></h3>
                        <div class="_wizard_pan"></div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <h3 class="_circule_number_wizard">4</h3> <h3 style="display: inline-block"><?php echo $_translator->_getTranslation('Relleno');?></h3>
                        <div class="_wizard_relleno"></div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                        <h3 class="_circule_number_wizard">5</h3> <h3 style="display: inline-block"><?php echo $_translator->_getTranslation('Decorado');?></h3>
                        <div class="_wizard_decorado"></div>
                        <?php //echo $form->showElement('precio_decorado') ?>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <?php $element = $form->getElement('addSliceWizard');echo $form->createOnlyElement($element);?>
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
<style>
    ._box_option_wizard{
        border: solid 1px #ccc;
        padding: 10px;
    }
    
    ._box_option_wizard:hover{
        border: solid 1px green;
        background-color: #89E894;        
        padding: 10px;
        cursor: pointer;
    }
    
    ._wizard_sizes ._box_option_wizard._size{
        width: 20% !important;
    }
    
    ._selected_option_wizard{
        background-color: #89E894;
        border: solid 1px green;
    }
    
    ._circule_number_wizard{
        display: inline-block;
        width: 40px;
        height: 40px;
        text-align: center;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        background: #eee;
        padding-top: 6px;
    }
</style>
<script>
    $('.box-body').on('click','._box_option_wizard',function(){
        var propierty = $(this).data('propierty');
        $('._'+propierty).removeClass('_selected_option_wizard');
        $(this).addClass('_selected_option_wizard');
        
        if(propierty === 'shape'){
            var shapeId = $(this).data('shapeid');            
            
            if($('#id_shape_for_letter').val() == shapeId){
                 setLetter();                 
            }else if($('#idShapeForNumber_'+shapeId).length){ /*Valido si existe campo con este nombre*/
                setNumbers($('#idShapeForNumber_'+shapeId));
            }
        }
        
        if(propierty === 'pan'){$('._wizard_relleno').show();}
        if(propierty === 'relleno'){$('._wizard_decorado').show();}
        if(propierty === 'decorado'){
            var decoradoId = $(this).data('decoradoid');
            if($('#idProductForSpecialDecorated').val() == decoradoId){
                setPriceForSuperSpecialDecorated();
            }else{
                $('#price').val('');
            }            
        }
    });
    
    $('._size').on('click',function(){
        $('#size').val($(this).data('sizeid'));
        setShapesBySize();
    });
    
    $('._wizard_shape').on('click','._shape',function(){
        $('#shape').val($(this).data('shapeid'));
        
        if($(this).hasClass('_selected_option_wizard') == false){
            setSlicesForSpecialOrderWizard();
        }
        
    });

    $('#agregar_detalle_wizard').on('click',function(){
        clearModalAddSliceToSpecialOrder();
        $('#modalAddSliceToSpecialOrderWizard').modal('show');
    });
    
    $('._closeModalAddSliceToSpecialOrderWizard').on('click',function(){
        $('#modalAddSliceToSpecialOrderWizard').modal('hide');
        clearModalAddSliceToSpecialOrder();        
        clearSpecialOrderWizard();
    });
    
</script>