<?php
$controller = 'index';
$action = '';
include $_SERVER["DOCUMENT_ROOT"].'/app/include/bootstrap.php';?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Berry Task | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo ROOT_HOST?>/public/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo ROOT_HOST?>/public/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo ROOT_HOST?>/public/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo ROOT_HOST?>/public/app/css/AdminLTE.min.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
    <?php
          if(isset($_POST['login'])){              
            $login = new Login($_POST);
            if($login->isLogged()){
                switch ($login->getRole()){
                    case '4':
                        echo "<script>window.location = '/Controller/Home.php'</script>";
                        break;
                    case '1':
                        echo "<script>window.location = '/Controller/Home.php'</script>";
                        break;
                    case '2':
                        echo "<script>window.location = '/Controller/Home.php'</script>";
                        break;
                    case '3':
                        echo "<script>window.location = '/Controller/Home.php'</script>";
                        break;
                    default:
                        echo "<script>window.location = '/Controller/Home.php'</script>";
                        break;
                }
            }
         }
    ?>        
<div class="login-box">
  <div class="login-logo">
    <b>Berry</b> Task
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Iniciar sesion</p>

    <form action="index.php" method="post">
      <div class="form-group has-feedback">
        <input type="text" id="user" name="user" class="form-control" placeholder="Usuario">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-4 pull-right">
          <button type="submit" id='login' name="login" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?php echo ROOT_HOST?>/public/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo ROOT_HOST?>/public/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
