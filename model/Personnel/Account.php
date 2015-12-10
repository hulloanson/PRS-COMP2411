<?php


class Account
{
    private $conn;

    function __construct()
    {
        $this->conn = DBHelper::getConnection();
    }

    function getUserType($userId)
    {
        $sql = "SELECT id, name FROM Role, Account WHERE Account.id = :userId AND Account.role_id = Role.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);

    }
}