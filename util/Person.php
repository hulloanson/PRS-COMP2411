<?php


class Person extends DB
{
    private $role_id;
    private $user_id;

    function __construct()
    {
        parent::__construct();
    }

    function assignToken($email, $password)
    {
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

    function getRole($token)
    {
        $sql = "SELECT Session.user_id AS 'user_id', Account.role_id AS 'role_id' FROM Session, Account
                WHERE Session.id = UNHEX(:token) AND Account.id = Session.user_id;";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":token", $token);

            if ($stmt->execute()) {
                $stmt->bindColumn("role_id", $this->role_id, PDO::PARAM_INT);
                $stmt->bindColumn("user_id", $this->user_id, PDO::PARAM_INT);
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

    function credMatch($email, $password)
    {
        $sql = "SELECT count(*) AS 'matched' FROM Account WHERE email = :email AND password = :passwordd;";

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

    function getConferenceManger($token)
    {
        $result = $this->getRole($token);
        if ($result == true and $this->role_id == 4) {
            $confManger = new ConferenceManager($this->user_id);
            return $confManger;
        } elseif ($result == 2 or $result == 3 or $result == 1) {
            return PRSErr::INCORRECT_CONTENT;
        } else {
            return $result;
        }
    }

    function getReviewer($token)
    {
        $result = $this->getRole($token);
        if ($result == true and $this->role_id == 1) {
            return new Reviewer($this->user_id);
        } elseif ($result == 2 or $result == 3 or $result == 4) {
            return PRSErr::NOTHING_FOUND;
        } else {
            return $result;
        }
    }

    function getTrackChair($token)
    {
        $result = $this->getRole($token);
        if ($result == true and $this->role_id == 2) {
            return new TrackChair($this->user_id);
        } elseif ($result == 1 or $result == 3 or $result == 4) {
            return PRSErr::INCORRECT_CONTENT;
        } else {
            return $result;
        }
    }

    function getConferenceChair($token)
    {
        $result = $this->getRole($token);
        if ($result == true and $this->role_id == 3) {
            return new ConferenceChair($this->user_id);
        } elseif ($result == 2 or $result == 1 or $result == 4) {
            return PRSErr::INCORRECT_CONTENT;
        } else {
            return $result;
        }
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