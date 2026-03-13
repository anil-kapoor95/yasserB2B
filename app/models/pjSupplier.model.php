<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjSupplierModel extends pjAppModel
    {
        protected $primaryKey = 'id';
        protected $table = 'suppliers';

        protected $schema = array(
            array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
            array('name' => 'auth_id', 'type' => 'int', 'default' => ':NULL'),
            array('name' => 'first_name', 'type' => 'varchar', 'default' => ':NULL'),
            array('name' => 'last_name', 'type' => 'varchar', 'default' => ':NULL'),
            array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
            array('name' => 'company_name', 'type' => 'varchar', 'default' => ':NULL'),
            array('name' => 'city', 'type' => 'varchar', 'default' => ':NULL'),
            array('name' => 'total_vehicles', 'type' => 'int', 'default' => 0),
            array('name' => 'status', 'type' => 'enum', 'default' => 'F'),
            array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
            array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()')
        );
	
	public static function factory($attr=array())
	{
		return new pjSupplierModel($attr);
	}
}
?>