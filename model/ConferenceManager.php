<?php


class ConferenceManager extends PrivilegedUser
{
    function __construct($userId)
    {
        parent::__construct($userId);
    }

    private function getRandTrackChair()
    {
        $sql = "SELECT Track_Chair.id AS 'id' FROM Track_Chair, Account
                WHERE Account.id = Track_Chair.id ORDER BY rand() LIMIT 1";

        try {
            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute()) {
                $stmt->bindColumn("id", $id, PDO::PARAM_INT);
                if ($stmt->fetch(PDO::FETCH_BOUND) != false) {
                    return $id;
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

    private function getRandReviewers()
    {
        $reviewerIds = array();
        $sql = "SELECT Reviewer.id AS 'id' FROM Reviewer, Account
                WHERE Account.invalidated = FALSE AND Account.id = Reviewer.id
                ORDER BY rand() LIMIT 3";

        try {
            $stmt = $this->conn->prepare($sql);

            if ($stmt->execute()) {
                $stmt->bindColumn("id", $id, PDO::PARAM_INT);
                while ($stmt->fetch(PDO::FETCH_BOUND) != false) {
                    array_push($reviewerIds, $id);
                }
                if (sizeof($reviewerIds) == 0) {
                    return 0;
                } else {
                    return $reviewerIds;
                }
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function addPaper($title, $authors, $keywords, $file)
    {
        $randTChairId = $this->getRandTrackChair();

        if ($randTChairId == -1 or $randTChairId == 0) {
            return $randTChairId;
        }

        try {
            $this->conn->beginTransaction();
            //Title
            $paperSql = "INSERT INTO Paper (title, status, responsible_chair)
                         VALUES (:title, 0, :tcId);";
            $paperStmt = $this->conn->prepare($paperSql);
            $paperStmt->bindValue(":title", $title);
            $paperStmt->bindValue(":tcId", $randTChairId, PDO::PARAM_INT);
            if ($paperStmt->execute()) {
                $paperId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            //Keywords
            $keywordSql = "INSERT INTO Keyword (keyword) VALUES (:keyword)
                       ON DUPLICATE KEY UPDATE id = last_insert_id(id);";
            $paperKeywordsql = "INSERT INTO Paper_Keyword (paper_id, keyword_id)
                            VALUES (:paperId, last_insert_id());";
            $keywordStmt = $this->conn->prepare($keywordSql);
            $paperKeywordStmt = $this->conn->prepare($paperKeywordsql);

            for ($i = 0; $i < sizeof($keywords); $i++) {
                $keywordStmt->bindValue(":keyword", $keywords[$i]);
                if (!($keywordStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
                $paperKeywordStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);
                if (!($paperKeywordStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
            }

            //Authors
            $authorSql = "INSERT INTO Author (name, address, city, country)
                              VALUES (:namee, :address, :city, :country)
                              ON DUPLICATE KEY UPDATE id = last_insert_id(id);";
            $authorPaperSql = "INSERT INTO Author_Paper (paper_id, author_id)
                    VALUES (:paperId, last_insert_id());";
            $authorStmt = $this->conn->prepare($authorSql);
            $authorPaperStmt = $this->conn->prepare($authorPaperSql);

            for ($i = 0; $i < sizeof($authors); $i++) {
                $authorStmt->bindValue(":namee", $authors[$i]["name"]);
                $authorStmt->bindValue(":address", $authors[$i]["address"]);
                $authorStmt->bindValue(":city", $authors[$i]["city"]);
                $authorStmt->bindValue(":country", $authors[$i]["country"]);
                if (!($authorStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
                $authorPaperStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);
                if (!($authorPaperStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
            }

            if (!($this->conn->commit())) {
                $this->conn->rollBack();
                return -1;
            }

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }

        $addSubmission = $this->addSubmission($paperId, $file[0], $file[1]);

        return array($paperId, $addSubmission);
    }

    private function getPaperStatus($paperId)
    {
        $checkOpSql = "SELECT status FROM Paper WHERE id = :paperId";

        try {
            $checkOpStmt = $this->conn->prepare($checkOpSql);

            $checkOpStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);

            if ($checkOpStmt->execute()) {
                $checkOpStmt->bindColumn("status", $status, PDO::PARAM_INT);
                if ($checkOpStmt->fetch(PDO::FETCH_BOUND) != false) {
                    return $status;
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

    function addSubmission($paperId, $file, $fileMime)
    {
        $status = $this->getPaperStatus($paperId);

        if ($status == 10 or $status == 12 or $status == 20 or $status == 21 or $status == 22
            or floor($status / 10) == 3 or $status = 0 or $status == -1
        ) {
            return $status;
        }

        $submissionSql = "INSERT INTO Submission (reviewStatus, type, file, file_mime, paper_id)
                VALUES (
                  0,
                  (if((SELECT count(type) FROM Submission WHERE paper_id = :paperId) = 0, 1, (
                  SELECT MAX(type) FROM Submission WHERE paper_id = :paperId) + 1)),
                  :filee, :fileMime, :paperId)";

        $paperSql = "UPDATE Paper SET status = (floor(status/10)+1)*10 WHERE id = :paperId";

        try {
            $this->conn->beginTransaction();

            $submissionStmt = $this->conn->prepare($submissionSql);

            $submissionStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);
            $submissionStmt->bindValue(":filee", $file, PDO::PARAM_LOB);
            $submissionStmt->bindValue(":fileMime", $fileMime);

            if ($submissionStmt->execute()) {
                $submissionId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            $paperStmt = $this->conn->prepare($paperSql);

            $paperStmt->bindValue(":paperId", $paperId, PDO::PARAM_INT);

            if (!($paperStmt->execute())) {
                $this->conn->rollBack();
                return -1;
            }

            if (!($this->conn->commit())) {
                $this->conn->rollBack();
                return -1;
            }

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }

        $assignReview = $this->assignReviewJob($submissionId, $this->getRandReviewers());

        return array($submissionId, $assignReview);

    }

    function assignReviewJob($submissionId, $reviewerIds)
    {
        if (gettype($reviewerIds) != 'array') {
            return -1;
        } else {
            if (empty($reviewerIds)) {
                return 0;
            }
        }

        $sql = "INSERT INTO Review_Record (submission_id, reviewer_id) VALUES";

        for ($i = 0; $i < sizeof($reviewerIds); $i++) {
            if ($i != 0) {
                $sql .= ",";
            }
            $sql .= " (:submissionId, :reviewerId" . $i . ")";
        }

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);
            for ($i = 0; $i < sizeof($reviewerIds); $i++) {
                $stmt->bindValue(":reviewerId" . $i, $reviewerIds[$i], PDO::PARAM_INT);
            }

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function addTrackChair($trackChair)
    {
        $rowCount = array();
        $accSql = "INSERT INTO Account (email, password, role_id, title, first_name, last_name, phone,
                    fax, department, gender, address, city, country)
                   VALUES (:email, :passwordd, 2, :title, :firstName, :lastName, :phone, :fax,
                        :department, :gender, :address, :city, :country)";

        $trackChairSql = "INSERT INTO Track_Chair (id) VALUES (:accId)";

        if (!empty($trackChair['areas'])) {
            $areaSql = "INSERT INTO Account_Area (area_id, acc_id) VALUES";

            for ($i = 0; $i < sizeof($trackChair['areas']); $i++) {
                if ($i != 0) {
                    $areaSql .= ",";
                }
                $areaSql .= " (:areaId" . $i . ", :reviewerId)";
            }

            $areaSql .= ";";
        }

        if (!empty($trackChair['organisations'])) {
            $orgSql = "INSERT INTO Account_organisation (org_id, acc_id) VALUES";

            for ($i = 0; $i < sizeof($trackChair['organisations']); $i++) {
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

            $accStmt->bindValue(":email", $trackChair['email']);
            $accStmt->bindValue(":password", $trackChair['password']);
            $accStmt->bindValue(":title", $trackChair['title']);
            $accStmt->bindValue(":firstName", $trackChair['firstName']);
            $accStmt->bindValue(":lastName", $trackChair['lastName']);
            $accStmt->bindValue(":phone", $trackChair['phone']);
            $accStmt->bindValue(":fax", $trackChair['fax']);
            $accStmt->bindValue(":department", $trackChair['department']);
            $accStmt->bindValue(":gender", $trackChair['gender']);
            $accStmt->bindValue(":address", $trackChair['address']);
            $accStmt->bindValue(":city", $trackChair['city']);
            $accStmt->bindValue(":country", $trackChair['country']);

            if ($accStmt->execute()) {
                $accId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            $trackChairStmt = $this->conn->prepare($trackChairSql);

            $trackChairStmt->bindValue(":accId", $accId, PDO::PARAM_INT);

            if ($trackChairStmt->execute()) {
                $reviewerId = $this->conn->lastInsertId();
            } else {
                $this->conn->rollBack();
                return -1;
            }

            if (!empty($trackChair['areas'])) {
                $areaStmt = $this->conn->prepare($areaSql);

                $areaStmt->bindValue(":reviewerId", $reviewerId, PDO::PARAM_INT);
                for ($i = 0; $i < sizeof($trackChair['areas']); $i++) {
                    $areaStmt->bindValue(":areaId" . $i, $trackChair['areas'][$i], PDO::PARAM_INT);
                }
                if (!($areaStmt->execute())) {
                    array_push($rowCount, $areaStmt->rowCount());
                    $this->conn->rollBack();
                    return -1;
                }
            }

            if (!empty($trackChair['organisations'])) {
                $orgStmt = $this->conn->prepare($orgSql);

                $orgStmt->bindValue(":reviewerId", $reviewerId, PDO::PARAM_INT);
                for ($i = 0; $i < sizeof($trackChair['organisations']); $i++) {
                    $orgStmt->bindValue(":orgId" . $i, $trackChair['organisations'][$i], PDO::PARAM_INT);
                }
                if (!($orgStmt->execute())) {
                    $this->conn->rollBack();
                    return -1;
                }
            }

            return $reviewerId;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }
    }

    function removeTrackChair()
    {

    }

    function addConferenceChair()
    {

    }

    function removeConferenceChair()
    {

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
            $areaSql = "INSERT INTO Account_Area (area_id, acc_id) VALUES";

            for ($i = 0; $i < sizeof($reviewer['areas']); $i++) {
                if ($i != 0) {
                    $areaSql .= ",";
                }
                $areaSql .= " (:areaId" . $i . ", :reviewerId)";
            }

            $areaSql .= ";";
        }

        if (!empty($reviewer['organisations'])) {
            $orgSql = "INSERT INTO Account_organisation (org_id, acc_id) VALUES";

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

            return $reviewerId;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return -1;
        }
    }

    function removeReviewer($reviewerId)
    {
        $accountSql = "UPDATE Account SET invalidated = TRUE WHERE id = :userId";

        try {
            $accountStmt = $this->conn->prepare($accountSql);
            $accountStmt->bindValue(":userId", $reviewerId, PDO::PARAM_INT);
            if ($accountStmt->execute()) {
                if ($count = $accountStmt->rowCount() == 0) {
                    return 0;
                } else {
                    return $count;
                }
            } else {
                return -1;
            }

        } catch (PDOException $e) {
            return -1;
        }
    }

}