<?php


class DB
{
    protected $conn;

    function __construct()
    {
        $this->conn = DBHelper::getConnection();
    }
}