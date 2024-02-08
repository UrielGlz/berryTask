<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">

    <meta name="author" content="">



    <title>Berry Task</title>

    <!-- Bootstrap 3.3.7 -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/jquery-ui/themes/base/all.css" />

    <!-- Font Awesome -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/font-awesome/css/font-awesome.min.css">

    <!-- Ionicons -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/Ionicons/css/ionicons.min.css">

    <!-- Theme style -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/app/css/AdminLTE.min.css">

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/app/css/app-styles.css" />

    <!-- AdminLTE Skins. Choose a skin from the css/skins

         folder instead of downloading all of them to reduce the load. -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/app/css/skins/_all-skins.min.css">

    <!-- Morris chart -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/morris.js/morris.css">

    <!-- jvectormap -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/jvectormap/jquery-jvectormap.css">

    <!-- Datetimepicker -->

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/bootstrap-datetimepicker/bootstrap-datetimepicker.css">

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/bootstrap-daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/select2/dist/css/select2.min.css">

    <!-- bootstrap wysihtml5 - text editor -->

    <link rel="stylesheet"
        href="<?php echo ROOT_HOST ?>/public/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/datatables.net-bs/css/dataTables.bootstrap.min.css" />

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/datatables.net/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/jquery-confirm/jquery-confirm.min.css" />

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/bootstrap-fileinput/css/fileinput.min.css" />

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/jquery-simpleswitch/jquery.simpleswitch.css" />
    <link rel="stylesheet"
        href="<?php echo ROOT_HOST ?>/public/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css" />

    <link rel="stylesheet" href="<?php echo ROOT_HOST ?>/public/plugins/iCheck/all.css" />



    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

    <!-- Google Font -->

    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->



    <!-- jQuery 3 -->

    <script src="<?php echo ROOT_HOST ?>/public/jquery/dist/jquery.min.js"></script>

    <!-- jQuery UI 1.11.4 -->

    <script src="<?php echo ROOT_HOST ?>/public/jquery-ui/jquery-ui.min.js"></script>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    <script>$.widget.bridge('uibutton', $.ui.button);</script>

    <!-- Bootstrap 3.3.7 -->

    <script src="<?php echo ROOT_HOST ?>/public/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Morris.js charts -->

    <script src="<?php echo ROOT_HOST ?>/public/raphael/raphael.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/morris.js/morris.min.js"></script>

    <!-- Sparkline -->

    <script src="<?php echo ROOT_HOST ?>/public/jquery-sparkline/dist/jquery.sparkline.min.js"></script>

    <!-- jvectormap -->

    <script src="<?php echo ROOT_HOST ?>/public/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- jQuery Knob Chart -->

    <script src="<?php echo ROOT_HOST ?>/public/jquery-knob/dist/jquery.knob.min.js"></script>

    <!-- datetimepicker -->

    <script src="<?php echo ROOT_HOST ?>/public/moment/min/moment.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/bootstrap-datetimepicker/bootstrap-datetimepicker.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Bootstrap WYSIHTML5 -->

    <script src="<?php echo ROOT_HOST ?>/public/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

    <!-- Slimscroll -->

    <script src="<?php echo ROOT_HOST ?>/public/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <!-- FastClick -->

    <script src="<?php echo ROOT_HOST ?>/public/fastclick/lib/fastclick.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/select2/dist/js/select2.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net/js/jquery.dataTables.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net/js/jszip.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net/js/dataTables.buttons.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net/js/buttons.html5.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net/js/buttons.print.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo ROOT_HOST ?>/public/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>



    <!-- AdminLTE App -->

    <script src="<?php echo ROOT_HOST ?>/public/app/js/adminlte.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/app/js/jquery-blockUI.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/app/js/app-functions.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/jquery-confirm/jquery-confirm.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/bootstrap-fileinput/js/fileinput.min.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/jquery-simpleswitch/jquery.simpleswitch.js"></script>

    <script src="<?php echo ROOT_HOST ?>/public/plugins/iCheck/icheck.min.js"></script>
</head>

<body class="hold-transition skin-blue sidebar-mini">

    <div class="wrapper">

        <?php

        $login = new Login();

        switch ($login->getRole()) {

            case '1':

                include ROOT . 'app/include/navegation.php';

                break;

            case '2':

                include ROOT . 'app/include/navegation_rol_2.php';

                break;

            case '3':

                include ROOT . 'app/include/navegation_rol_3.php';

                break;

            case '4':

                include ROOT . 'app/include/navegation_rol_4.php';

                break;

            case '5':

                include ROOT . 'app/include/navegation_rol_5.php';

                break;

            case '6':

                include ROOT . 'app/include/navegation_rol_6.php';

                break;

        } ?>



        <div class="content-wrapper" style='padding: 0px'>

            <?php include ROOT . "/View/" . $vista; ?>

            <?php include ROOT . "/View/Modal/gadgetTimeClock.php"; ?>

            <iframe name="iframe" id="iframe" style="display:none;"></iframe>

        </div>



        <footer class="main-footer">

            <strong>Copyright &copy; 2023 <a href="https://lunis.mx">Berry Task</a>.</strong>

        </footer>

    </div>
   
    <script>
        getNotification();//Mandar a llamar la funcion de notificacion rool
    </script>
    
</body>

</html>