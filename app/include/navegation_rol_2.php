<?php

$login = new Login();


?>

<header class="main-header">

  <!-- Logo -->

  <a href="Home.php" class="logo">

    <!-- mini logo for sidebar mini 50x50 pixels -->

    <span class="logo-mini"><b>B</b>TK</span>

    <!-- logo for regular state and mobile devices -->

    <span class="logo-lg"><b>Berry</b>Task</span>

  </a>

  <!-- Header Navbar: style can be found in header.less -->

  <nav class="navbar navbar-static-top">

    <!-- Sidebar toggle button-->

    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">

      <span class="sr-only">Toggle navigation</span>

    </a>



    <div class="navbar-custom-menu">

      <ul class="nav navbar-nav">

        <!-- Messages: style can be found in dropdown.less-->
        <li class="dropdown notifications-menu" id="list_notification">
          
        </li>

        <!-- User Account: style can be found in dropdown.less -->

        <li class="dropdown user user-menu">

          <a href="#" class="dropdown-toggle" data-toggle="dropdown">

            <img src="<?php echo ROOT_HOST ?>/public/app/img/logo.jpg" class="user-image" alt="User Image">

            <span class="hidden-xs">
              <?php echo $login->getCompleteName() ?>
            </span>

          </a>

          <ul class="dropdown-menu">

            <!-- User image -->

            <li class="user-header">

              <img src="<?php echo ROOT_HOST ?>/public/app/img/logo.jpg" class="img-circle" alt="User Image">

              <p>
                <?php echo $login->getCompleteName(); ?>
              </p>

            </li>

            <!-- Menu Footer-->

            <li class="user-footer">

              <div class="pull-left">

                <a href="#" class="btn btn-default btn-flat">
                  <?php echo $_translator->_getTranslation('Perfil'); ?>
                </a>

              </div>

              <div class="pull-right">

                <a href="<?php echo ROOT_HOST; ?>/class/Login/Logout.php" class="btn btn-default btn-flat"><?php echo $_translator->_getTranslation('Salir'); ?></a>

              </div>

            </li>

          </ul>

        </li>

        <!-- Control Sidebar Toggle Button -->

        <li>

          <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>

        </li>

      </ul>

    </div>

  </nav>

</header>

<!-- Left side column. contains the logo and sidebar -->

<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->

  <section class="sidebar">

    <!-- Sidebar user panel -->

    <div class="user-panel">

      <div class="pull-left image">

        <img src="<?php echo ROOT_HOST ?>/public/app/img/logo.jpg" class="img-circle" alt="User Image">

      </div>

      <div class="pull-left info">

        <p>
          <?php echo $login->getCompleteName() ?>
        </p>

        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>

      </div>

    </div>

    <!-- sidebar menu: : style can be found in sidebar.less -->

    <ul class="sidebar-menu" data-widget="tree">

      <li class="header text-uppercase">
        <?php echo $_translator->_getTranslation('Navegacion'); ?>
      </li>

      <li><a href="Home.php"><i class="fa fa-dashboard"></i> <span>
            <?php echo $_translator->_getTranslation('Inicio') ?>
          </span></a></li>

      <li class="treeview">

        <a href="#">

          <i class="fa fa-file-code-o"></i> <span>
            <?php echo $_translator->_getTranslation('Proyectos') ?>
          </span>

          <span class="pull-right-container">

            <i class="fa fa-angle-left pull-right"></i>

          </span>

        </a>

        <ul class="treeview-menu">

          <li><a href="Project.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span>
                <?php echo $_translator->_getTranslation('Lista Proyecto') ?>
              </span></a>
          </li>
          <li><a href="Project.php"><i class="fa fa-circle-o text-primary"></i> <span>
                <?php echo $_translator->_getTranslation('Nuevo proyecto') ?>
              </span></a>
          </li>


        </ul>

      </li>

      <li><a href="Reports.php"><i class="fa fa-tasks"></i> <span>
            <?php echo $_translator->_getTranslation('Mis tareas') ?>
          </span></a>
      </li>

      <li class="treeview">

        <a href="#">

          <i class="fa fa-sticky-note"></i> <span>
            <?php echo $_translator->_getTranslation('Tickets') ?>
          </span>

          <span class="pull-right-container">

            <i class="fa fa-angle-left pull-right"></i>

          </span>

        </a>

        <ul class="treeview-menu">

          <li><a href="Invoice.php"><i class="fa fa-circle-o text-primary"></i> <span>
                <?php echo $_translator->_getTranslation('Nuevo ticket') ?>
              </span></a>
          </li>

          <li><a href="Invoice.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span>
                <?php echo $_translator->_getTranslation('Lista de tickets') ?>
              </span></a>
          </li>

        </ul>

      </li>

      <li><a href="Reports.php"><i class="fa fa-bar-chart"></i> <span>
            <?php echo $_translator->_getTranslation('Reportes') ?>
          </span></a></li>

      <li class="treeview">

        <a href="#">

          <i class="fa fa-list"></i> <span>
            <?php echo $_translator->_getTranslation('Catalogos') ?>
          </span>

          <span class="pull-right-container">

            <i class="fa fa-angle-left pull-right"></i>

          </span>

        </a>

        <ul class="treeview-menu">

          <li><a href="Priorities.php"><i class="fa fa-circle-o text-primary"></i>
              <?php echo $_translator->_getTranslation('Proridades'); ?>
            </a></li>

          <li><a href="CategoryTask.php"><i class="fa fa-circle-o text-primary"></i>
              <?php echo $_translator->_getTranslation('Categoria de Tareas'); ?>
            </a></li>



          <li><a href="Customer.php"><i class="fa fa-circle-o text-primary"></i>
              <?php echo $_translator->_getTranslation('Clientes'); ?>
            </a></li>

          <li><a href="Service.php"><i class="fa fa-circle-o text-primary"></i>
              <?php echo $_translator->_getTranslation('Free'); ?>
            </a></li>

        </ul>

      </li>

      <li class="treeview">

        <a href="#">

          <i class="fa fa-gears"></i> <span>
            <?php echo $_translator->_getTranslation('Configuracion') ?>
          </span>

          <span class="pull-right-container">

            <i class="fa fa-angle-left pull-right"></i>

          </span>

        </a>

        <ul class="treeview-menu">

          <li><a href="User.php"><i class="fa fa-circle-o text-primary"></i>
              <?php echo $_translator->_getTranslation('Usuarios'); ?>
            </a></li>

        </ul>

      </li>

    </ul>

  </section>

  <!-- /.sidebar -->

</aside>