/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  VillaJu
 * Created: Jan 21, 2018
 */

ALTER TABLE `sat_c_claveprodserv` ADD PRIMARY KEY(`clave`);
ALTER TABLE `sat_c_claveprodserv` ADD INDEX(`descripcion`);

set @maxim = (select max(id) + 1 from acl_resources);
INSERT INTO `acl_resources` (`id`, `controller`, `action`) VALUES (@maxim, 'Producto', 'productJson');

update acl_roles set resources = concat(resources, ',', @maxim);