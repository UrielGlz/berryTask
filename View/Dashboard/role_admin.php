<?php
$query_date = date('Y-m-d');
$date = new DateTime($query_date);
$date->modify('first day of this month');
$firstdayMonth = $date->format('m/d/Y');
$date->modify('last day of this month');
$lastdayMonth = $date->format('m/d/Y');
$login_ = new Login(); ?>

<!-- SALES -->
<div class="box-body">
    
    <div class="box box-primary">
        
        <div class="box-header">
            <div class="col-sm-3 invoice-col">
                <div class="form-group">
                    <select class="form-control select2 filterHome" style="width: 100%;" id="project_id"
                        name="project_id" required="true">
                        <?php
                        $repository_ = new ProjectRepository();
                        $listPriorities = $repository_->getListProject($login->getId()); ?>
                        <option value="">Proyecto</option>
                        <?php foreach ($listPriorities as $key => $value) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-3 invoice-col">
                <!-- Select prioritie_id  -->
                <div class="form-group">
                    <select class="form-control select2 filterHome" style="width: 100%;" id="prioritie_id"
                        name="prioritie_id">
                        <?php
                        $form_ = new TaskForm();
                        $listPriorities = $form_->getListPrioritie(); ?>

                        <?php foreach ($listPriorities as $key => $value) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3 invoice-col">
                <!-- Select prioritie_id  -->
                <div class="form-group">
                    <select class="form-control select2 filterHome" style="width: 100%;" id="status" name="status">
                        <?php
                        $form_ = new TaskForm();
                        $listPriorities = $form_->getListStatus(); ?>

                        <?php foreach ($listPriorities as $key => $value) { ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3 invoice-col">
                <!-- Select customer_id  -->
                <div class="form-group">
                    <select class="form-control select2 filterHome" style="width: 100%;" id="customer_id" name="customer_id">
                        <?php
                        $form_ = new TaskForm();
                        $listPriorities = $form_->getListCustomer(); ?>
            
                        <?php foreach ($listPriorities as $key => $value) { ?>
                            <option value="<?php echo $key ?>">
                                <?php echo $value ?>
                            </option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="pull-right box-tools">
            
                    <button type="button" class="btn btn-danger btn-sm pull-right" id="cleanFilter" title="Borrar filtros">Borrar
                        Filtro <i class="fa fa-eraser"></i></button>
                </div>
            </div>
            

        </div>
        <div class="box-body">
            <div class="col-sm-6 invoice-col">

            </div>

            <div class="col-lg-12 col-xs-12">

                <div class="card-datatable table-responsive" id="accordion">

                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <table class="dt-row-grouping table display compact" id="Taskbyresponsable">
                        <thead>
                            <tr>
                                <th class="text-center">Proyecto</th>
                                <th class="text-center">Descipción de la tarea</th>
                                <th class="text-center">Fecha vencimiento</th>
                                <th class="text-center">Categoria</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Prioridad</th>                                
                                <th class="text-center">Responsable</th>
                                <th class="text-center">Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="Tbody_taskbyresponsable">

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="chart_sales" class="col-lg-6 col-xs-12">

            </div>
        </div>
    </div>
</div>
<!-- END SALES -->

<script>
    $(document).ready(function () {
        // _salesChart('<?php echo $firstdayMonth ?>','<?php echo $lastdayMonth ?>');
        // _specialOrdersChart('<?php echo $firstdayMonth ?>','<?php echo $lastdayMonth ?>');
        // _TraficoChart('<?php echo $firstdayMonth ?>','<?php echo $lastdayMonth ?>');
        //  _MermasChart('<?php echo $firstdayMonth ?>','<?php echo $lastdayMonth ?>');
        getTaskByResponsable();//Obtiene todas las tareas por usario logeado
        $(".filterHome").on("change", function () {

            // $("select option:selected").each(function () {

            // });
            // var paramName = $(this).attr("id");
            // var paramVal = $(this).val();
            // var filter = array();
            // filter.push(paramName => paramVal);
            // alert(filter);
            // alert($(this).val());
            // alert($(this).attr("id"));

            getTaskByResponsable($(".filterHome").serializeArray());
        });
        $("#cleanFilter").on("click", function () {

            $(".filterHome").val("");

            getTaskByResponsable($(".filterHome").serializeArray());
        });


    });
</script>