<?php

if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjAuctionModel extends pjAppModel
{
    protected $primaryKey = 'id';
    protected $table = 'auctions';

    protected $schema = array(
        array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'supplier_id', 'type' => 'int', 'default' => ':NULL'),
        array('name' => 'status', 'type' => 'enum', 'default' => 'active'),
        array('name' => 'accepted_on', 'type' => 'datetime', 'default' => ':NULL'),
        array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
        array('name' => 'modified', 'type' => 'datetime', 'default' => ':NOW()'),
    );

    public static function factory($attr = array())
    {
        return new pjAuctionModel($attr);
    }
}
?>