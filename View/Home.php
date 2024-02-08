<?php $login = new Login(); ?>

<section class="content-header">

    <h1>Dashboard<small>Control panel</small></h1>

    <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li class="active">Dashboard</li>

    </ol>

</section>

<section class="content">

    <div class="box-body">

       

    </div>

    <div class="box">

        <?php

        switch ($login->getRole()) {

            case '1':

                include ROOT . "/View/Dashboard/role_admin.php";

                break;

            case '2':

                  include ROOT."/View/Dashboard/role_miembro.php";
        
                break;

        }

        ?>

    </div>

</section>

<script src="<?php echo ROOT_HOST ?>/public/chart.js/Chart.js"></script>

<script src="<?php echo ROOT_HOST ?>/public/app/js/app-dashboard.js"></script>