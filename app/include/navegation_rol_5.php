<?php
$login = new Login();
?>
<header class="main-header">
    <!-- Logo -->
    <a href="Home.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>P</b>BK</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Paris</b>Bakery</span>
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
        <li class="dropdown messages-menu">
          <a href="#" data-target="#modalGadgetTimeClock" data-toggle="modal"><i class="fa fa-clock-o"></i> </a>
        </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo ROOT_HOST?>/public/app/img/logo.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $login->getCompleteName() ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo ROOT_HOST?>/public/app/img/logo.jpg" class="img-circle" alt="User Image">
                <p><?php echo $login->getCompleteName(); ?></p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat"><?php echo $_translator->_getTranslation('Perfil'); ?></a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo ROOT_HOST;?>/class/Login/Logout.php" class="btn btn-default btn-flat"><?php echo $_translator->_getTranslation('Salir'); ?></a>
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
          <img src="<?php echo ROOT_HOST?>/public/app/img/logo.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p><?php echo $login->getCompleteName() ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header text-uppercase"><?php echo $_translator->_getTranslation('Navegacion'); ?></li>
        <li><a href="Home.php"><i class="fa fa-dashboard"></i> <span><?php echo $_translator->_getTranslation('Inicio') ?></span></a></li>
        <li><a href="SalesRecord.php"><i class="fa fa-line-chart"></i> <span><?php echo $_translator->_getTranslation('Registro de ventas') ?></span></a></li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-birthday-cake"></i> <span><?php echo $_translator->_getTranslation('Ordenes especiales') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="SpecialOrder.php"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Agregar nueva') ?></span></a></li>  
            <li><a href="SpecialOrder.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Lista de ordenes') ?></span></a></li>  
          </ul>
        </li>  
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bookmark"></i> <span><?php echo $_translator->_getTranslation('Pedidos de sucursal') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="StoreRequest.php"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Agregar nueva') ?></span></a></li>  
            <li><a href="StoreRequest.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Lista de pedidos') ?></span></a></li>  
          </ul>
        </li>   
        <li class="treeview">
          <a href="#">
            <i class="fa fa-download"></i> <span><?php echo $_translator->_getTranslation('Recibos de pedidos') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="ReceivingStoreRequest.php"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Agregar nuevo') ?></span></a></li>  
            <li><a href="ReceivingStoreRequest.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Lista de envios') ?></span></a></li>  
          </ul>
        </li>     
        <li class="treeview">
          <a href="#">
            <i class="fa fa-shopping-basket"></i> <span><?php echo $_translator->_getTranslation('Compras') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="Purchase.php"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Agregar nueva') ?></span></a></li>  
            <li><a href="Purchase.php?action=list"><i class="fa fa-circle-o text-primary"></i> <span><?php echo $_translator->_getTranslation('Lista de compras') ?></span></a></li>  
          </ul>
        </li>   
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span><?php echo $_translator->_getTranslation('Pagos') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="Payment.php"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Agregar nuevo') ?></a></li>
            <li><a href="Payment.php?action=list"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Lista de pagos') ?></a></li>
          </ul>
        </li>   
        <li class="treeview">
          <a href="#">
            <i class="fa fa-cubes"></i> <span><?php echo $_translator->_getTranslation('Almacen') ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">            
            <li><a href="Receiving.php"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Recibos') ?></a></li>
            <li><a href="Receiving.php?action=list"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Lista de recibos') ?></a></li>
            <li><a href="Output.php"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Salidas') ?></a></li>
            <li><a href="Output.php?action=list"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Lista de salidas') ?></a></li>
            <li><a href="Transfer.php"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Traspasos') ?></a></li>
            <li><a href="Transfer.php?action=list"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Lista de traspasos') ?></a></li>
            <li><a href="Return.php"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Retornos') ?></a></li>
            <li><a href="Return.php?action=list"><i class="fa fa-circle-o text-primary"></i><?php echo $_translator->_getTranslation('Lista de retornos') ?></a></li>
          </ul>
        </li>
        <li><a href="PhysicalInventory.php?action=list"><i class="fa fa-check-square-o"></i><?php echo $_translator->_getTranslation('Inventario fisico') ?></a></li>      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>