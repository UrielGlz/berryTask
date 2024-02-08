<!-- Modal -->

<div id="modalAddCustomer"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalCustomer"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-group'></i> <span id="title_modal_customer"><?php echo $_translator->_getTranslation('Agregar proveedor');?></span></h4>

            </div>

                <?php 

                if(!isset($form)){$form = new CustomerForm();}   

                echo $form->openForm();

                echo $form->showActionController();

                echo $form->showId();?>

                <div class="box-body">

                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true);?></div>

                    <div class="card">

                        <ul class="nav nav-tabs" role="tablist">

                                <li role="presentation" class="active"><a href="#general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>                

                                <!-- <li role="presentation"><a href="#more" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion comercial') ?></a></li>                 -->

                        </ul>

                        <br/>

                         <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="general">

                                <div class="col-md-6 col-xs-12">

                                    <?php

                                    $form->showElement('name');

                                    $form->showElement('address');      

                                    $form->showElement('contact');     

                                    $form->showElement('status');     

                                    ?>

                                </div>

                                <div class="col-md-6 col-xs-12">

                                    <?php

                                    $form->showElement('phone');                        

                                    $form->showElement('email1');

                                    $form->showElement('email2');

                                    $form->showElement('email3');

                                    $form->showElement('email4');

                                    ?>           

                                </div>     

                            </div>
                        <!-- TAB 2  -->
                        <!-- <div role="tabpanel" class="tab-pane" id="more">

                            <div class="col-md-6 col-xs-12">

                                <?php 

                                // $form->showElement('payment_method');

                                // $form->showElement('credit_days');

                                // $form->showElement('status');

                                ?>

                            </div>     

                        </div> -->

                         </div>

                    </div>

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar')?>" class='btn btn-primary'/>

                    </div>

                    <div class="pull-left">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar')?>" class="btn btn-default _closeModalCustomer" />

                    </div>

                </div>

                <?php echo $form->closeForm();?>

            </div>

        </div>

    </div>           

    </div>

    <style> label.payment_method{margin-top: -5px;}</style>

    <script>

        $('#modalAddCustomer').on('shown.bs.modal', function (e) {

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

    </script>

</div>