<?php


class Person extends DB
{
    private $role_id;

    function __construct()
    {
        parent::__construct();
    }

    function assignToken($email, $password) {
        $matched = $this->credMatch($email, $password);
        if ($matched > 0) {
            $token = Util::guidv4();
        } else {
            return 0;
        }

        $sql = "INSERT INTO Session (id, user_id, issued_time) VALUES (UNHEX(:token), :userId, NOW())
                ON DUPLICATE KEY UPDATE id = :token AND issued_time = now();";

        try {

            $stmt = DBHelper::getConnection()->prepare($sql);

            $stmt->bindValue(":token", $token);

            if ($stmt->execute()) {
                return $token;
            } else {
                return -1;
            }

        } catch (PDOException $e) {
            return -1;
        }
    }

    function getRole($token) {
        $sql = "SELECT role_id as 'role' FROM Session, Account
                WHERE Session.id = UNHEX(:token) AND Account.id = Session.user_id;";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":token", $token);

            if ($stmt->execute()) {
                $stmt->bindColumn("role", $this->role_id, PDO::PARAM_INT);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return true;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function credMatch($email, $password) {
        $sql = "SELECT count(*) as 'matched' FROM Account WHERE email = :email AND password = :passwordd;";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":email", $email);
            $stmt->bindValue(":passwordd", $password);

            if ($stmt->execute()) {
                $stmt->bindColumn("matched", $matched, PDO::PARAM_INT);
                if ($stmt->fetch()) {
                    return $matched;
                } else {
                    return -1;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function isConferenceManager()
    {
        return ($this->role_id == 4);
    }

    function isReviewer()
    {
        return ($this->role_id === 1);
    }

    function isConferenceChair()
    {
        return ($this->role_id == 3);
    }

    function isTrackChair()
    {
        return ($this->role_id == 2);
    }

    function tokenToUserId($token)
    {
        $sql = "SELECT user_id FROM Session WHERE id = UNHEX(:token)";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":token", $token);

            if ($stmt->execute()) {
                $stmt->bindColumn("user_id", $userId, PDO::PARAM_INT);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $userId;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }
}