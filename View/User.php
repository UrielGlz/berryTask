<section class="content-header">

    <h1><i class='fa-fw fa fa-user'></i> <?php echo $_translator->_getTranslation('Usuarios');?></small></h1>

    <ol class="breadcrumb">

        <li><a href="<?php echo ROOT_HOST?>/Controller/Inicio.php"><i class="fa fa-dashboard"></i><?php echo $_translator->_getTranslation('Inicio')?></a></li>

      <li class="active"><?php echo $_translator->_getTranslation('Usuarios');?></li>

    </ol>

</section>

<section class="content">

<div class="box">

  <div class="box-header with-border">

    <h3 class="box-title"></h3>

    <div class="box-tools pull-right">

      <span class="btn btn-default _addUser"><i class='fa fa-plus'></i> <?php echo $_translator->_getTranslation('Agregar usuario')?></span> 

      <!-- <span class="btn btn-default _advancedSearch"><i class='fa fa-search-plus'></i> <?php echo $_translator->_getTranslation('Busqueda avanzada')?></span>  -->

    </div><!-- /.box-tools -->

  </div><!-- /.box-header -->

  <div class="box-body">

    <div class='flashmessenger'><?php $flashmessenger->showMessage(null);?></div>

     <div class="clear"></div>

     <div class="table-responsive">
        <form autocomplete="false">
        <table id="tblUsers" class="table table-bordered table-condensed table-striped table-hover datatable_whit_filter_column">

<thead>

    <th class="text-center"><?php echo $_translator->_getTranslation('#');?></th>

    <th class="text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>    

    <th class="text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    

    <th class="text-center"><?php echo $_translator->_getTranslation('Rol');?></th>    

    <th class="text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    

    <th class="text-center"><?php echo $_translator->_getTranslation('Status');?></th>   

    <th class="col-md-1 col-xs-1 text-center"><?php echo $_translator->_getTranslation('Accion');?></th>    

</thead>

<tfoot>

    <th class="filter text-center"><?php echo $_translator->_getTranslation('#');?></th>

    <th class="filter text-center"><?php echo $_translator->_getTranslation('Usuario');?></th>    

    <th class="filter text-center"><?php echo $_translator->_getTranslation('Nombre');?></th>    

    <th class="filter text-center"><?php echo $_translator->_getTranslation('Rol');?></th>    

    <th class="filter text-center"><?php echo $_translator->_getTranslation('Telefono');?></th>    

    <th class="filter text-center"><?php echo $_translator->_getTranslation('Status');?></th>

    <?php

    /*

        if($user['role']=='1')

        {

            ?>

                <th class="filter text-center"><?php echo $_translator->_getTranslation('SSN');?></th>

            <?php

        }*/

    ?>

    <th></th>    

</tfoot>

<tbody class="users">

<?php

if($_listUsers){

foreach($_listUsers as $user){?>

<tr>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['employe_number']?></td>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['user']?></td>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['complete_name']?></td>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['role_name']?></td>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['phone']?></td>

<td class="text-center" data-id="<?php echo $user['id']?>"> <?php echo $user['status_name']?></td>

<?php

/*

    if($user['role']=='1')

    {

        ?>

            <th class="filter text-center"><?php echo $user['ssn'];?></th>

        <?php

    }*/

?>

<td class="text-center">

    <span class="btn btn-default _edit" data-id="<?php echo $user['id']?>"><i class="fa fa-edit"></i></span>

    <span class="btn btn-danger _delete" data-id="<?php echo $user['id']?>"><i class="fa fa-trash"></i></span>

</td>

</tr>

<?php }

}?>

</tbody>

</table>

        </form>
      
     </div>

  </div><!-- /.box-body -->

</div><!-- /.box -->

</section>

<?php include ROOT."/View/Modal/addUser.php";?>

<?php //include ROOT."/View/Modal/advancedSearch.php";?>

<?php if(isset($_noValid)){?> <script>$('#modalAddUser').modal('show');</script> <?php }?>

<script>

     $('.my-colorpicker1').colorpicker({        
        
        format: 'hex',
        colorSelectors: {
                'black': '#000000',
                'white': '#ffffff',
                'red': '#FF0000',
                'default': '#777777',
                'primary': '#337ab7',
                'success': '#5cb85c',
                'info': '#5bc0de',
                'warning': '#f0ad4e',
                'danger': '#d9534f'
            },
            sliders: {
                saturation: {
                    maxLeft: 100,
                    maxTop: 40
                },
                hue: {
                    maxTop: 40
                },
                alpha: {
                    maxTop: 100
                }
            },
    });
    $('#role,#store_id,#area_bakery_production_id,#status').select2();

    $('._addUser').on('click',function(){

        clearForm('user');       

        $('form[name=user] #action').val('insert');

        $('form[name=user] #id').val('');

        $('.flashmessenger').html('');

        $('#divPhoto').html('');

         _getTranslation('Agregar usuario',function(msj){ $('#title_modal_user').html(msj);});

        $('#modalAddUser').modal('show');

    });

    

    $('._closeModalUser').on('click',function(){

        clearForm('user');

        $('.flashmessenger').html('');

        $('#modalAddUser').modal('hide');

    });

    

    $('tbody.users td ._edit').on('click',function(e){  

        if (!$(e.target).closest('._delete').length) { 

            clearForm('user');

            $('#divPhoto').html('');

            $('.flashmessenger').html('');

             _getTranslation('Editar usuario',function(msj){ $('#title_modal_user').html(msj);});

            var id = $(this).data('id'); 
           
            setDataToEditUser(id);

        }       

    }); 

    

    $('tbody.users td ._delete').on('click',function(){

        var id = $(this).data('id');

        deleteUser(id);

    });

    

   $('#tblUsers').DataTable({

        paginate:false,

        filter:true,

        aaSorting:[]

    });

    

    

    

    

</script>