<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDriverModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'drivers';
	
	protected $schema = array( 
    array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
    array('name' => 'title', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'auth_id', 'type' => 'int', 'default' => ':NULL'),
    array('name' => 'first_name', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'last_name', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'gender', 'type' => 'enum', 'default' => ':NULL'),
    array('name' => 'dob', 'type' => 'date', 'default' => ':NULL'),
    array('name' => 'license_number', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'license_expiry', 'type' => 'date', 'default' => ':NULL'),
    array('name' => 'vehicle_id', 'type' => 'int', 'default' => ':NULL'),
    array('name' => 'password', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'license_file', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'national_id_number', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'address', 'type' => 'text', 'default' => ':NULL'),
    array('name' => 'city', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'state', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'zip', 'type' => 'varchar', 'default' => ':NULL'),
    array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
    array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
    array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjDriverModel($attr);
	}
}
?>