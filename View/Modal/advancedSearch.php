<!-- Modal -->
<div id="modalAdvancedSearch" class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body"> 
            <?php 
            $_advanceSearchForm = new AdvancedSearchForm($controller);
            echo $_advanceSearchForm->openForm();
            echo $_advanceSearchForm->showActionController();?>
            <div class="box box-primary">
            <div class="box-header with-border">
                <button type="button" class="close _closeModalAdvancedSearch"><i class="fa fa-window-close"></i></button>
                <h4><i class='fa-fw fa fa-search'></i> <span><?php echo $_translator->_getTranslation('Busqueda avanzada');?></span></h4>
            </div>
                <div class="box-body">
                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>
                    <div class="col-md-12 col-xs-12">
                         <?php echo $_advanceSearchForm->getStringFiltersForm($controller); ?>
                    </div>   
                </div>
                <div class="modal-footer">
                <div class="col-lg-2 col-md-2 col-xs-6 pull-left   ">
                     <input type="button" value="<?php echo $_translator->_getTranslation('Cerrar')?>" class="btn btn-default _closeModalAdvancedSearch" />
                </div>
                <div class="col-lg-2 col-md-2 col-xs-6 pull-right">                
                    <?php $element = $_advanceSearchForm->getElement('search');echo $_advanceSearchForm->createElement($element); ?>
                </div>
            </div>
            </div>
            <?php echo $_advanceSearchForm->closeForm();?>
        </div>
    </div>           
    </div>
    <script>       
        $('._advancedSearch').on('click',function(){
            $('form[name=advanced_search] #startDate,form[name=advanced_search] #endDate').datetimepicker({format: 'MM/DD/YYYY'});   
            $('form[name=advanced_search] select').select2();
            $('#modalAdvancedSearch').modal('show'); 
        });
    
        $('._closeModalAdvancedSearch').on('click',function(){
            $('.flashmessenger').html('');
            $('#modalAdvancedSearch').modal('hide');
        });
        
    </script>
</div>
