<!-- Modal -->

<!-- Se utiliza para agregar clientes desde cualquier modulo; por eso le pusimos gadget -->

<div id="modalAddCustomerGadget"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalCustomer"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-group'></i> <span id="title_modal_customer"><?php echo $_translator->_getTranslation('Agregar proveedor');?></span></h4>

            </div>

                <?php 

                $form = new CustomerForm();

                echo $form->openForm();

                echo $form->showActionController();

                echo $form->showId();?>

                <div class="box-body">

                <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>

                <div class='col-md-12 col-xs-12'>

                    <div class="col-md-6 col-xs-12">

                        <?php

                        $form->showElement('name');

                        $form->showElement('address');

                        $form->showElement('city');

                        $form->showElement('zipcode');

                        ?>

                    </div>

                    <div class="col-md-6 col-xs-12">

                        <?php

                        $form->showElement('phone');

                        $form->showElement('email');

                        ?>           

                    </div>     

                </div>

                    

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type='button' value="<?php echo $_translator->_getTranslation('Agregar cliente') ?>" class="btn btn-primary _saveCustomer" />

                    </div>

                    <div class="pull-left">

                        <input type='button' value="<?php echo $_translator->_getTranslation('Cerrar') ?>" class="btn btn-default _closeModalCustomer" />

                    </div>

                </div>

                <?php echo $form->closeForm();?>

            </div>

        </div>

    </div>           

    </div>

    <style> label.payment_method{margin-top: -5px;}</style>

    <script>

        $('#modalAddCustomerGadget').on('shown.bs.modal', function (e) {

            if($('#action').val() === 'insert'){$('#name').focus();}

            

            var input = null;

            $("form[name=customer]").find('div').each(function() {

                if($(this).hasClass('has-error') === true){

                    input = $(this).data('errorfor');

                    return false;

                }

            });    

            

            if(input !== null){$('#'+input).focus();}

        });

        

        $('._closeModalCustomer').on('click',function(){

            clearForm('customer');

            $('.flashmessenger').html('');

            $('#modalAddCustomerGadget').modal('hide');

        });

    </script>

</div>