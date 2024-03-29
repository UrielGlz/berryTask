DELIMITER ;;
/*  Flexviews for MySQL 
    Copyright 2008 Justin Swanhart

    FlexViews is free software: you can redistribute it and/or modify
    it under the terms of the Lesser GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    FlexViews is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with FlexViews in the file COPYING, and the Lesser extension to
    the GPL (the LGPL) in COPYING.LESSER.
    If not, see <http://www.gnu.org/licenses/>.
*/

DROP PROCEDURE IF EXISTS `rename`;;
/****f* UTIL_API/rename
 * NAME
 *   flexviews.rename - Rename a materialized views
 * SYNOPSIS
 *   flexviews.rename(v_mview_id, v_new_schema, v_new_table);
 * FUNCTION
 *   This function renames the given materialized view.
 * INPUTS
 *   * v_mview_id - The materialized view id (see flexviews.get_id)
 *   * v_new_schema - The new schema, may be the same as the current schema. NULL or '' = current schema.
 *   * v_new_table  - The new table, may be the same as the current table. NULL or '' = current table.
 * RESULT
 *   An error will be generated in the MySQL client if the view can not be enabled.
 * NOTES
 *   If the materialized view cannot be renamed, an error is produced.
 *  If @fv_force is set to TRUE, the creation is forced and no error is issued, whenever possible.
 * SEE ALSO
 *   SQL_API/disable, UTIL_API/get_id, SQL_API/enable, SQL_API/create
 * EXAMPLE
 *   mysql>
 *     call flexviews.rename(flexviews.get_id('test','mv_example'), 'test', 'new_name_example')
 *     call flexviews.rename(flexviews.get_id('test','mv_example'), 'new_schema_example', 'test')
 *     call flexviews.rename(flexviews.get_id('test','mv_example'), 'new_schema', 'and_new_table')
******
*/
CREATE DEFINER=`flexviews`@`localhost` PROCEDURE `rename`(
  IN v_mview_id INT,
  IN v_mview_schema_new TEXT CHARACTER SET UTF8,
  IN v_mview_name_new TEXT CHARACTER SET UTF8
)
BEGIN
  DECLARE v_mview_enabled tinyint(1);
  DECLARE v_mview_name TEXT CHARACTER SET UTF8 DEFAULT NULL; 
  DECLARE v_mview_schema TEXT CHARACTER SET UTF8;

  DECLARE v_sql TEXT CHARACTER SET UTF8;

  DECLARE EXIT HANDLER
    FOR 1062
  BEGIN
    SET @fv_force = NULL;
    CALL flexviews.signal(
        CONCAT_WS('', 'Materialized view already exists: ', v_mview_name, ' in schema: ', v_mview_schema)
      );
  END;

  -- NULL v_mview_id?
  IF v_mview_id IS NULL THEN
    SET @fv_force = NULL;
	CALL flexviews.signal('Materialized view id is NULL');
  END IF;

  SELECT mview_name, 
         mview_schema, 
	     mview_enabled
    INTO v_mview_name, 
         v_mview_schema, 
         v_mview_enabled
    FROM flexviews.mview
   WHERE mview_id = v_mview_id;

   IF v_mview_name IS NULL THEN
     SET @fv_force = NULL;
	 CALL flexviews.signal('The specified materialized view does not exist');
   END IF;

  -- NULL or '' = dont-change;
  -- otherwise, check schema exists & table NOT exists

  IF v_mview_schema_new IS NULL OR v_mview_schema_new = '' THEN
    SET v_mview_schema_new := v_mview_schema;
  END IF;
  IF NOT @fv_force <=> TRUE AND NOT `flexviews`.`schema_exists`(v_mview_schema_new) THEN
    -- NEW schema MUST exist
    CALL flexviews.signal(
        CONCAT_WS('', 'Schema not found: ', v_mview_schema_new)
      );
  END IF;

  IF v_mview_name_new IS NULL OR v_mview_name_new = '' THEN
    SET v_mview_name_new := v_mview_name;
  END IF;
  IF NOT @fv_force <=> TRUE AND `flexviews`.`table_exists`(v_mview_schema_new, v_mview_name_new) THEN
    -- NEW table MUST NOT exist in given db
    CALL flexviews.signal(
        CONCAT_WS('', 'Table already exists: ', v_mview_name_new, ' in schema: ', v_mview_schema_new)
      );
  END IF;

   IF v_mview_enabled = TRUE THEN
     SET v_sql = CONCAT('RENAME TABLE ', v_mview_schema, '.', v_mview_name, ' TO ', v_mview_schema_new, '.', v_mview_name_new,
       ',', v_mview_schema, '.', v_mview_name, '_delta TO ', v_mview_schema_new, '.', v_mview_name_new, '_delta');
     SET @v_sql = v_sql;
     PREPARE mv_stmt FROM @v_sql;
     SET @v_sql := NULL;
     EXECUTE mv_stmt;
     DEALLOCATE PREPARE mv_stmt;
   END IF;

   UPDATE flexviews.mview
      SET mview_name = v_mview_name_new,
          mview_schema = v_mview_schema_new
    WHERE mview_id = v_mview_id;
	
	-- generic error
  IF NOT ROW_COUNT() = 1 THEN
	SET @fv_force = NULL;
	CALL flexviews.signal(
        CONCAT_WS('', 'Could not create materialized view: ', v_mview_name)
      );
  END IF;
  
  SET @fv_force = NULL;
END ;;

DELIMITER ;
