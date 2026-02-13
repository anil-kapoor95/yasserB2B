<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetExtraModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'fleets_extras';
	
	protected $schema = array(
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjFleetExtraModel($attr);
	}
}
?>