<section class="content-header">

    <h1><i class='fa-fw fa fa-tasks'></i>
        <?php echo $_translator->_getTranslation('Lista de proyectos'); ?></small>
    </h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST ?>/Controller/Home.php"><i class="fa fa-dashboard"></i>
                <?php echo $_translator->_getTranslation('Inicio') ?>
            </a></li>

        <li class="active">
            <?php echo $_translator->_getTranslation('Lista de proyectos'); ?>
        </li>

    </ol>

</section>

<section class="content">

    <div class="box">

        <div class="box-header with-border">

            <h3 class="box-title"></h3>

            <div class="box-tools pull-right">

                <a href="Project.php" class="btn btn-default"><i class='fa fa-plus'></i>
                    <?php echo $_translator->_getTranslation('Agregar proyecto') ?>
                </a>

            </div><!-- /.box-tools -->

        </div><!-- /.box-header -->

        <div class="box-body">

            <div class='flashmessenger'>
                <?php $flashmessenger->showMessage(); ?>
            </div>

            <div class="clear"></div>

            <div class='table-responsive'>

                <table id="tblSpecialOrder"
                    class="table table-bordered table-striped table-hover table-condensed datatable_whit_filter_column">

                    <thead>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('No.'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('Nombre del proyecto'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('Cliente'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('Fecha inicio'); ?>
                        </th>

                        <th class="col-lg-2 text-center">
                            <?php echo $_translator->_getTranslation('Miembros'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('Progreso'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('%'); ?>
                        </th>

                        <th class="col-lg-1 text-center">
                            <?php echo $_translator->_getTranslation('Status'); ?>
                        </th>



                        <th class="col-lg-1 text-center">Accion</th>

                    </thead>

                    <tfoot>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('No.'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Nombre del proyecto'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Cliente'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Fecha inicio'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Miembros'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Progreso'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('%'); ?>
                        </th>

                        <th class="filter text-center">
                            <?php echo $_translator->_getTranslation('Status'); ?>
                        </th>

                        <th></th>

                    </tfoot>

                    <tbody>

                        <?php

                        if ($_listProjects) {

                            foreach ($_listProjects as $project) { ?>

                                <tr>

                                    <td class="text-center">
                                        <?php echo $project['id'] ?>
                                    </td>

                                    <td class="text-center">
                                        <?php echo $project['name'] ?>
                                    </td>

                                    <?php if (isset($project['customer_name'])) { ?>
                                        <td class="text-center">
                                            <?php echo $project['customer_name'] ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="text-center">N/A</td>
                                    <?php } ?>
                                    <td class="text-center">
                                        <?php echo $project['format_date_start'] ?>
                                    </td>

                                    <td class="text-center">

                                        <?php 
                                            if(isset($project['members'])){
                                                $repository = new ProjectRepository();
                                                $listUsers = $repository->GetUsersGroup($project['members']);
                                                echo $listUsers[0]['names'];
                                            }
                                            
                                         ?>
                                    </td>

                                    <td class="text-center">
                                        

                                        <div class="progress progress-sm active">
                                            <div class="progress-bar progress-bar-success progress-bar-striped"
                                                role="progressbar" aria-valuenow="<?php echo $project['progreso'] ?>" aria-valuemin="0" aria-valuemax="100"
                                                style="width: <?php echo   round($project['progreso'],2) ?>%">
                                                <span class="sr-only"><?php echo   round($project['progreso'],2) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php echo   round($project['progreso'],2) ?>
                                    </td>
                                    
                                    <td class="text-center">
                                        <?php echo $project['status_name'] ?>
                                    </td>

                                    <td class="text-center" style='white-space:nowrap'>

                                        <a class="btn btn-sm btn-default"
                                            href="Project.php?action=edit&id=<?php echo $project['id'] ?>"><i
                                                class="fa fa-pencil fa-"></i></a>
                                    </td>

                                </tr>
                                <?php

                            }

                        } ?>

                    </tbody>

                </table>

            </div>

        </div><!-- /.box-body -->

    </div><!-- /.box -->

</section>

<style>
    .span_on_dropdown_menu {

        display: block;

        padding: 3px 20px;

        font-weight: 400;

        line-height: 1.42857143;

        white-space: nowrap;

    }



    .span_on_dropdown_menu:hover {

        background-color: #e1e3e9;

        cursor: pointer;

    }
</style>


<script type="text/javascript" language="javascript">
   // getNotification();
    

</script>