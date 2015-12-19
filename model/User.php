<?php


class User extends DB
{
    protected $userId;

    function __construct($userId)
    {
        parent::__construct();
        $this->userId = $userId;
    }



    function getAllOrganisations() {
        $allRows = array();
        $sql = "SELECT * FROM organisation;";

        try {
            $stmt = $this->conn->prepare($sql);

            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC) != false) {
                    array_push($allRows, $row);
                }
                if (empty($allRows)) {
                    return 0;
                } else {
                    return $allRows;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnOrganisation() {
        $orgs = array();
        $sql = "SELECT name AS 'org_name' FROM organisation, Account_organisation WHERE acc_id = :accId
                AND organisation.id = Account_organisation.org_id";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("org_name", $orgName);
                while ($stmt->fetch(PDO::FETCH_BOUND)) {
                    array_push($orgs, $orgName);
                }
                if (sizeof($orgs) == 0) {
                    return 0;
                } else {
                    return $orgs;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnGender() {
        $sql = "SELECT gender FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("gender", $gender, PDO::PARAM_INT);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $gender;
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

    function setOwnGender($gender) {

    }

    function getOwnFirstName()
    {
        $sql = "SELECT first_name FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("first_name", $firstName);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $firstName;
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

    function setOwnFirstName($value)
    {
        $sql = "UPDATE Account SET first_name = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnLastName()
    {
        $sql = "SELECT last_name FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("last_name", $lastName);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $lastName;
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

    function setOwnLastName($lastName)
    {
        $sql = "UPDATE Account SET last_name = :lastName WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":lastName", $lastName);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnEmail()
    {
        $sql = "SELECT email FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("email", $email);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $email;
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

    function setOwnEmail($value)
    {
        $sql = "UPDATE Account SET first_name = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnAddress()
    {
        $sql = "SELECT address FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("address", $address);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $address;
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

    function setOwnAddress($value)
    {
        $sql = "UPDATE Account SET address = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnDepartment()
    {
        $sql = "SELECT department FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("department", $department);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $department;
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

    function setOwnDeparment($value)
    {
        $sql = "UPDATE Account SET department = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnCity()
    {
        $sql = "SELECT country FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("city", $city);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $city;
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

    function setOwnCity($value)
    {
        $sql = "UPDATE Account SET city = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnCountry()
    {
        $sql = "SELECT country FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("phone", $country);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $country;
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

    function setOwnCountry($value)
    {
        $sql = "UPDATE Account SET country = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnFax()
    {
        $sql = "SELECT fax FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("fax", $fax);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $fax;
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

    function setOwnFax($value)
    {
        $sql = "UPDATE Account SET fax = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnPhone()
    {
        $sql = "SELECT phone FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("phone", $phone);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $phone;
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

    function setOwnPhone($value)
    {
        $sql = "UPDATE Account SET phone = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnTitle()
    {
        $sql = "SELECT title FROM Account WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("title", $title);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $title;
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

    function setOwnTitlte($value)
    {
        $sql = "UPDATE Account SET title = :valuee WHERE id = :accId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":valuee", $value);
            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getOwnRole()
    {
        $sql = "SELECT name FROM Role, Account WHERE Account.id = :accId AND Account.role_id = Role.id";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":accId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("name", $role);
                if ($stmt->fetch(PDO::FETCH_BOUND)) {
                    return $role;
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

    function getOwnInformation()
    {
        $result = array(
            'email' => $this->getOwnEmail(),
            'role' => $this->getOwnRole(),
            'title' => $this->getOwnTitle(),
            'firstName' => $this->getOwnFirstName(),
            'lastName' => $this->getOwnLastName(),
            'phone' => $this->getOwnPhone(),
            'fax' => $this->getOwnFax(),
            'department' => $this->getOwnDepartment(),
            'gender' => $this->getOwnGender(),
            'address' => $this->getOwnAddress(),
            'city' => $this->getOwnCity(),
            'country' => $this->getOwnCountry()
        );
        $error = null;

        foreach ($result as $item) {
            if ($item === PRSErr::NOTHING_FOUND or $item === PRSErr::DB_ERR) {
                $error = $item;
                return $error;
            }
        }

        return $result;
    }
}