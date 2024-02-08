<section class="content-header">

    <h1><i class='fa-fw fa fa-group'></i>
        <?php echo $_translator->_getTranslation('Clientes'); ?></small>
    </h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i>
                <?php echo $_translator->_getTranslation('Inicio') ?>
            </a></li>

        <li class="active">
            <?php echo $_translator->_getTranslation('Clientes'); ?>
        </li>

    </ol>

</section>

<section class="content">

    <div class="box">

        <div class="box-header with-border">

            <h3 class="box-title"></h3>

            <div class="box-tools pull-right">

                <span class="btn btn-default pull-right _addCustomer"><i class='fa fa-plus'></i>
                    <?php echo $_translator->_getTranslation('Agregar cilente') ?>
                </span>

            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->

        <div class="box-body">

            <div class='flashmessenger'>
                <?php $flashmessenger->showMessage(null); ?>
            </div>

            <div class="clear"></div>

            <div class="table-responsive">
                <form autocomplete="false">
                <table id="tblCustomers"
                    class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">

                    <thead>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Nombre'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Direccion'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Telefono'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Email'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Contacto'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Status'); ?>
                        </th>

                        <th class="text-center">
                            <?php echo $_translator->_getTranslation('Accion'); ?>
                        </th>

                    </thead>

                    <tfoot>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Nombre'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Direccion'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Telefono'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Email'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Contacto'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Status'); ?>
                        </th>

                        <th></th>

                    </tfoot>

                    <tbody class="customers">

                        <?php

                        if ($_listCustomers) {

                            foreach ($_listCustomers as $customer) { ?>

                                <tr>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['name'] ?></td>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['address'] ?></td>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['phone'] ?></td>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['email1'] ?></td>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['contact'] ?></td>

                                    <td class="text-center" data-id="<?php echo $customer['id'] ?>"> <?php echo $customer['status'] ?></td>

                                    <td class="text-center">
                                        <?php

                                        if ($login->getRole() == '1') { ?>
                                            <span class="btn btn-default _edit" data-id="<?php echo $customer['id'] ?>"><i
                                                    class="fa fa-edit"></i></span>

                                            <span class="btn btn-danger _delete" data-id="<?php echo $customer['id'] ?>"><i
                                                    class="fa fa-trash"></i></span>
                                            <?php

                                        } ?>

                                    </td>

                                </tr>

                            <?php }

                        } ?>

                    </tbody>

                </table>

                </form>
                
            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->

</section>

<?php include ROOT . "/View/Modal/addCustomer.php"; ?>

<?php if (isset($_noValid)) { ?>
    <script>$('#modalAddCustomer').modal('show');</script>
<?php } ?>

<script>



    $('#payment_method,#status').select2();

    $('#payment_method').on('change', function () {

        if (this.value == '1') { $('#credit_days').val('0'); $('#credit_days').prop('readOnly', true); }

        else if (this.value == '2') { $('#credit_days').val(''); $('#credit_days').prop('readOnly', false); }

    });



    $('._addCustomer').on('click', function () {

        clearForm('customer');

        $('form[name=customer] #action').val('insert');

        $('form[name=customer] #id').val('');

        $('.flashmessenger').html('');

        _getTranslation('Agregar cilente', function (msj) { $('#title_modal_customer').html(msj); });

        $('#modalAddCustomer').modal('show');

    });



    $('._closeModalCustomer').on('click', function () {

        clearForm('customer');

        $('.flashmessenger').html('');

        $('#modalAddCustomer').modal('hide');

    });



    $('tbody.customers td ._edit').on('click', function (e) {

        if (!$(e.target).closest('._delete').length) {

            clearForm('customer');

            $('.flashmessenger').html('');

            _getTranslation('Editar cilente', function (msj) { $('#title_modal_customer').html(msj); });

            var id = $(this).data('id');

            setDataToEditCustomer(id);

        }

    });



    $('tbody.customers td ._delete').on('click', function () {

        var id = $(this).data('id');

        deleteCustomer(id);

    });



    $('#tblCustomers').DataTable({

        paginate: false,

        filter: true,

        aaSorting: []

    });

</script>