<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetPriceModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'fleets_prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'from_city', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'to_city', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjFleetPriceModel($attr);
	}
}
?>