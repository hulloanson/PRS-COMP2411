<?php

class TrackChair extends User
{
    function __construct($userId)
    {
        parent::__construct($userId);
    }

    function getReviewedSubmissions() {
        $allRows = array();

        $sql = "SELECT paper_id, paper_title, authors_name, submission_id, submit_type, submit_time
                FROM Submission_View WHERE review_status = 1 AND responsible_chair = :userId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

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

    function getSubmission($submissionId)
    {
        $submissionSql = "SELECT paper_id, paper_title, authors_name, submission_id, submit_type, submit_time,
                          review_status
                          FROM Submission_View
                          WHERE submission_id = :submissionId AND responsible_chair = :userId;";

        $reviewRecSql = "SELECT Review_Record_View.review_id as 'review_id',
                         Review_Record_View.reviewer_name as 'reviewer_name',
                         Review_Record_View.assigned_time as 'assigned_time',
                         Review_Record_View.completed as 'completed',
                         (if(completed_time = null, 'N/A', completed_time)) AS 'completed_time'
                         FROM Review_Record_View, Submission_View
                         WHERE Review_Record_View.submission_id = Submission_View.submission_id
                         AND Submission_View.responsible_chair = :userId
                         AND Submission_View.submission_id = :submissionId";

        try {
            $this->conn->beginTransaction();

            $submissionStmt = $this->conn->prepare($submissionSql);

            $submissionStmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);
            $submissionStmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

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
            $reviewRecStmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

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

    function getSubmissionFile($submissionID) {
        $sql = "SELECT Submission.file AS 'file', Submission.file_mime as 'file_mime'
                FROM Submission, Submission_View WHERE Submission_View.responsible_chair = :userId AND
                Submission.id = :submissionId;";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);
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

    function doFinalReview($submissionId, $decision) {
        $sql = "UPDATE Submission SET reviewStatus = :decision WHERE id = :submissionId
                AND id IN (SELECT Submission.id FROM Submission, Paper WHERE Submission.paper_id = Paper.id
                            AND Paper.responsible_chair = :userId)";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":decision", $decision, PDO::PARAM_INT);
            $stmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);
            $stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }

    function getResponsiblePapersList() {
        $allRows = array();
        $sql = "SELECT paper_id, paper_title, authors_name, paper_progress, review_status FROM
                Paper_View WHERE responsible_chair = :userId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

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

    function getPaperDetails($paperId) {
        $paperSql = "SELECT paper_id, paper_title, authors_name, paper_progress, review_status
                     FROM Paper_View where responsible_chair = :userId AND paper_id = :paperId";

        $submissionSql = "SELECT submission_id, submit_type, submit_time, review_status FROM Submission_View
                          WHERE paper_id = :paperId AND responsible_chair = :userId";

        try {
            $this->conn->beginTransaction();

            $paperStmt = $this->conn->prepare($paperSql);

            $paperStmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);
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

            $submissionStmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);
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

}