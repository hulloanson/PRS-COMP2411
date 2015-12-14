<?php


class PrivilegedUser extends User
{
    function __construct($userId)
    {
        parent::__construct($userId);
    }

    function addReviewer($reviewer)
    {
        $rowCount = array();
        $accSql = "INSERT INTO Account (email, password, role_id, title, first_name, last_name, phone,
                    fax, department, gender, address, city, country)
                   VALUES (:email, :passwordd, 1, :title, :firstName, :lastName, :phone, :fax,
                        :department, :gender, :address, :city, :country)";

        $reviewerSql = "INSERT INTO Reviewer (id) VALUES (:accId)";

        if (!empty($reviewer['areas'])) {
            $areaSql = "INSERT INTO Reviewer_Area (area_id, reviewer_id) VALUES";

            for ($i = 0; $i < sizeof($reviewer['areas']); $i++) {
                if ($i != 0) {
                    $areaSql .= ",";
                }
                $areaSql .= " (:areaId" . $i . ", :reviewerId)";
            }

            $areaSql .= ";";
        }

        if (!empty($reviewer['organisations'])) {
            $orgSql = "INSERT INTO Reviewer_Organisation (organisation_id, reviewer_id) VALUES";

            for ($i = 0; $i < sizeof($reviewer['organisations']); $i++) {
                if ($i != 0) {
                    $orgSql .= ",";
                }
                $orgSql .= " (:orgId" . $i . ", :reviewerId)";
            }

            $orgSql .= ";";
        }

        try {
            $this->conn->beginTransaction();

            $accStmt = $this->conn->prepare($accSql);

            $accStmt->bindValue(":email", $reviewer['email']);
            $accStmt->bindValue(":password", $reviewer['password']);
            $accStmt->bindValue(":title", $reviewer['title']);
            $accStmt->bindValue(":firstName", $reviewer['firstName']);
            $accStmt->bindValue(":lastName", $reviewer['lastName']);
            $accStmt->bindValue(":phone", $reviewer['phone']);
            $accStmt->bindValue(":fax", $reviewer['fax']);
            $accStmt->bindValue(":department", $reviewer['department']);
            $accStmt->bindValue(":gender", $reviewer['gender']);
            $accStmt->bindValue(":address", $reviewer['address']);
            $accStmt->bindValue(":city", $reviewer['city']);
            $accStmt->bindValue(":country", $reviewer['country']);

            if ($accStmt->execute()) {
                $accId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            $reviewerStmt = $this->conn->prepare($reviewerSql);

            $reviewerStmt->bindValue(":accId", $accId, PDO::PARAM_INT);

            if ($reviewerStmt->execute()) {
                $reviewerId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            if (!empty($reviewer['areas'])) {
                $areaStmt = $this->conn->prepare($areaSql);

                $areaStmt->bindValue(":reviewerId", $reviewerId, PDO::PARAM_INT);
                for ($i = 0; $i < sizeof($reviewer['areas']); $i++) {
                    $areaStmt->bindValue(":areaId" . $i, $reviewer['areas'][$i], PDO::PARAM_INT);
                }
                if (!($areaStmt->execute())) {
                    array_push($rowCount, $areaStmt->rowCount());
                    $this->conn->rollBack();
                    return -1;
                }
            }

            if (!empty($reviewer['organisations'])) {
                $orgStmt = $this->conn->prepare($orgSql);

                $orgStmt->bindValue(":reviewerId", $reviewerId, PDO::PARAM_INT);
                for ($i = 0; $i < sizeof($reviewer['organisations']); $i++) {
                    $orgStmt->bindValue(":orgId" . $i, $reviewer['organisations'][$i], PDO::PARAM_INT);
                }
                if (!($orgStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
            }

            return $rowCount;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }
    }

    function removeReviewer($reviewerId)
    {
        $rowCount = array();

        $reviewerSql = "UPDATE Reviewer SET deleted = TRUE WHERE id = :reviewerId";

        $accountSql = "UPDATE Account SET invalidated = TRUE WHERE id = :userId";

        try {
            $reviewerStmt = $this->conn->prepare($reviewerSql);
            $reviewerStmt->bindValue(":reviewerId", $reviewerId, PDO::PARAM_INT);
            if ($reviewerStmt->execute()) {
                if ($count = $reviewerStmt->rowCount() == 0) {
                    return 0;
                } else {
                    array_push($rowCount, $count);
                }
            } else {
                return -1;
            }

            $accountStmt = $this->conn->prepare($accountSql);
            $accountStmt->bindValue(":userId", $reviewerId, PDO::PARAM_INT);
            if ($accountStmt->execute()) {
                if ($count = $accountStmt->rowCount() == 0) {
                    return 0;
                } else {
                    array_push($rowCount, $count);
                }
            } else {
                return -1;
            }

            return $rowCount;

        } catch (PDOException $e) {
            return -1;
        }
    }

    function getAllPapers()
    {
        $allRows = array();
        $sql = "SELECT paper_id, paper_title, authors_name, paper_progress, review_status FROM
                Paper_View";

        try {
            $stmt = $this->conn->prepare($sql);

            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC) != false) {
                    array_push($allRows, $row);
                }
                return $allRows;
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getPaper($paperId)
    {
        $paperSql = "SELECT paper_id, paper_title, authors_name, paper_progress, review_status, responsible_chair
                     FROM Paper_View WHERE paper_id = :paperId";

        $submissionSql = "SELECT submission_id, submit_type, submit_time, review_status FROM Submission_View
                          WHERE paper_id = :paperId";

        try {
            $this->conn->beginTransaction();

            $paperStmt = $this->conn->prepare($paperSql);

            $paperStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);

            if ($paperStmt->execute()) {
                if ($result = $paperStmt->fetch(PDO::FETCH_ASSOC) != false) {
                    $result['submissions'] = array();
                } else {
                    return 0;
                }
            } else {
                $this->conn->rollBack();
                return -1;
            }

            $submissionStmt = $this->conn->prepare($submissionSql);

            $submissionStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);

            if ($submissionStmt->execute()) {
                while ($row = $submissionStmt->fetch(PDO::FETCH_ASSOC) != false) {
                    array_push($result['submissions'], $row);
                }
            } else {
                $this->conn->rollBack();
                return -1;
            }

            if ($this->conn->commit()) {
                return $result;
            } else {
                $this->conn->rollBack();
                return -1;
            }

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }
    }

    function getSubmission($submissionId)
    {
        $submissionSql = "SELECT paper_id, paper_title, authors_name, submission_id, submit_type, submit_time,
                          review_status, responsible_chair
                          FROM Submission_View
                          WHERE submission_id = :submissionId;";

        $reviewRecSql = "SELECT Review_Record_View.review_id AS 'review_id',
                         Review_Record_View.reviewer_name AS 'reviewer_name',
                         Review_Record_View.assigned_time AS 'assigned_time',
                         Review_Record_View.completed AS 'completed',
                         (if(completed_time = NULL, 'N/A', completed_time)) AS 'completed_time'
                         FROM Review_Record_View, Submission_View
                         WHERE Review_Record_View.submission_id = Submission_View.submission_id
                         AND Submission_View.submission_id = :submissionId";

        try {
            $this->conn->beginTransaction();

            $submissionStmt = $this->conn->prepare($submissionSql);

            $submissionStmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);

            if ($submissionStmt->execute()) {
                if ($result = $submissionStmt->fetch(PDO::FETCH_ASSOC) != false) {
                    $result['reviews'] = array();
                } else {
                    return 0;
                }
            } else {
                $this->conn->rollBack();
                return -1;
            }

            $reviewRecStmt = $this->conn->prepare($reviewRecSql);

            $submissionStmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);

            if ($reviewRecStmt->execute()) {
                while ($row = $reviewRecStmt->fetch(PDO::FETCH_ASSOC) != false) {
                    array_push($result['reviews'], $row);
                }
            } else {
                $this->conn->rollBack();
                return -1;
            }

            if ($this->conn->commit()) {
                return $result;
            } else {
                $this->conn->rollBack();
                return -1;
            }

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }
    }

    function getSubmissionFile($submissionID)
    {
        $sql = "SELECT Submission.file AS 'file', Submission.file_mime AS 'file_mime'
                FROM Submission, Submission_View WHERE Submission.id = :submissionId;";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":submissionId", $submissionID, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->bindColumn("file", $file, PDO::PARAM_LOB);
                $stmt->bindColumn("file_mime", $fileMime, PDO::PARAM_STR);
                if (!($stmt->fetch(PDO::FETCH_BOUND))) {
                    return array($file, $fileMime);
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