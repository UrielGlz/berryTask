<!-- Modal -->

<div id="modalAddUser"  class='modal fade' data-backdrop="static" data-keyboard="false" role="dialog" style=" overflow: hidden;background: transparent">

    <div class="modal-dialog modal-lg" role="document">

    <div class="modal-content">

        <div class="modal-body"> 

            <div class="box box-primary">

            <div class="box-header with-border">

                <button type="button" class="close _closeModalUser"><i class="fa fa-window-close"></i></button>

                <h4><i class='fa-fw fa fa-user'></i> <span id="title_modal_user"><?php echo $_translator->_getTranslation('Agregar usuario'); ?></span></h4>

            </div>

                <?php

                $login = new Login();

                $role_id = $login->getRole(); //id del rol del usuario que hizo login
                
                /*

                 * Necesito el id del rol para saber si puedo mostrar la informacion confidencial

                 * Solo admin puede verla

                 */

                echo $_form->openForm();

                echo $_form->showActionController();

                echo $_form->showId(); ?>

                

                <div class="box-body">

                    <div class='flashmessenger'><?php $flashmessenger->showMessage(true); ?></div>

                    <div class="card">

                    <ul class="nav nav-tabs" role="tablist">

                        <li role="presentation" class="active"><a href="#tab_general" aria-controls="home" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion general') ?></a></li>

                        <li role="presentation" class="hide"><a href="#tab_personal_info" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $_translator->_getTranslation('Informacion laboral') ?></a></li>          

                    </ul>

                    <!-- Tab panes -->

                    <div class="tab-content" style="padding-top: 20px">

                        <div role="tabpanel" class="tab-pane active" id="tab_general">

                            <div class="col-lg-6 col-xs-12">

                                <?php

                                $_form->showElement('user');

                                $_form->showElement('password');

                                $_form->showElement('confirm_password');

                                //$_form->showElement('nip');
                                
                                //$_form->showElement('confirm_nip');                        
                                
                                $_form->showElement('role');

                                $_form->showElement('initials');
                                
                                $_form->showElement('color');                                   
                                
                                ?>

                            </div>

                            <div class="col-lg-6 col-xs-12">

                                <?php

                                $_form->showElement('name');

                                $_form->showElement('last_name');

                                $_form->showElement('phone');

                                $_form->showElement('email');

                                $_form->showElement('status');

                                ?>

                            </div>     

                        </div>

                        <div role="tabpanel" class="tab-pane" id="tab_personal_info">

                            <div class="col-lg-6 col-xs-12">                                

                                <?php

                                /*

                                 * Aqui listamos informacion confidencial

                                 */

                                if ($role_id == '1') {

                                    // $_form->showElement('employe_number');
                                
                                    // $_form->showElement('ssn');                                                               
                                
                                    // $_form->showElement('address');
                                
                                    // $_form->showElement('alta_payroll');       
                                
                                    // $_form->showElement('baja_payroll');   
                                
                                    $_form->showElement('photo[]');

                                }

                                ?>

                            </div>

                            <div class="col-lg-6 col-xs-12">                                

                                <div id="divPhoto" class='col-md-12'></div>                                

                            </div>     

                        </div>

                    </div>

                     </div>

                    

                    

                </div>

                <div class="box-footer">

                    <div class="pull-right">

                        <input type="submit" value="<?php echo $_translator->_getTranslation('Terminar') ?>" class='btn btn-primary'/>

                    </div>
                    <div class="pull-left">

                        <input type="button" value="<?php echo $_translator->_getTranslation('Cancelar') ?>" class="btn btn-default _closeModalUser" />

                    </div>
                </div>

                <?php echo $_form->closeForm(); ?>

            </div>

        </div>

    </div>           

    </div>

    <style>

        label.confirm_password {

            margin-top:-7px;

        }

    </style>

    <script>

        $('#modalAddUser').on('shown.bs.modal', function (e) {

            if($('#action').val() === 'insert'){$('#user').focus();}

            

            var input = null;

            $("form[name=user]").find('div').each(function() {

                if($(this).hasClass('has-error') === true){

                    input = $(this).data('errorfor');

                    return false;

                }

            });    

            

            if(input !== null){$('#'+input).focus();}

        });

        

       // $('#alta_payroll,#baja_payroll').datetimepicker({format: 'MM/DD/YYYY'});

        $('select').on('select2:opening',function(e){

            e.preventDefault();

            if($(this).prop('disabled') === false){$(this).unbind('select2:opening').select2('open');}

        });

        $('._closeModalUser').on('click',function(){

            clearForm('user');

            $('.flashmessenger').html('');

            $('#modalAddUser').modal('hide');
        });

        // $('#role').on('select2:select',function(){

        //     if($('#role').val() === '4'){

        //         $('#area_bakery_production_id').prop('disabled',false);        

        //     }else{

        //         $('#area_bakery_production_id').prop('disabled',true);      

        //     }

        // });

        

        // if($('#role').val() === '4'){

        //         $('#area_bakery_production_id').prop('disabled',false);        

        // }else{

        //     $('#area_bakery_production_id').prop('disabled',true);      

        // }

        

        

        

    </script>

</div>