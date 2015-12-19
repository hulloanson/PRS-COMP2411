<?php


class PrivilegedUser extends User
{
    function __construct($userId)
    {
        parent::__construct($userId);
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

    function getREviewRecords($arrayOfContents)
    {
        $sql = "SELECT Review_Record_View.paper_id AS 'paper_id',
		Review_Record_View.paper_title AS 'paper_title',
		Review_Record_View.authors_name AS 'authors_name',
		Review_Record_View.submission_id AS 'submission_id',
		Review_Record_View.review_id AS 'review_id',
		Review_Record_View.reviewer_id AS 'reviewer_id',
		Review_Record_View.reviewer_name AS 'reviewer_name',
		Review_Record_View.rating AS 'rating',
		Review_Record_View.assigned_time AS 'assigned_time',
		Review_Record_View.completed AS 'completed',
		Review_Record_View.completed_time AS 'completed_time'
			FROM Review_Record_View
			WHERE Review_Record_View.invalidated = 0";

        if (isset($arrayOfContents['reviewer_name'])) {
            $sql .= "AND reviewer_name = :reviewer_name ";
        }

        if (isset($arrayOfContents['paper_title'])) {
            $sql .= "AND paper_title = :paper_title ";
        }

        if (isset($arrayOfContents['assigned_timeBefore'])) {
            $sql .= "AND assigned_time = :before BETWEEN :after ";
        }

        if (isset($arrayOfContents['completed'])) {
            $sql .= "AND completed = :completed ";
        }
        $sql .= ";";//end of creating sql statement

        try {
            $stmt = $this->conn->prepare($sql);
            //binding values if isset() is true
            if (isset($arrayOfContents['reviewer_name'])) {
                $stmt->bindValue(":reviewer_name", $arrayOfContents['reviewer_name']);
            }
            if ($arrayOfContents['paper_title']) {
                $stmt->bindValue(":paper_title", $arrayOfContents['paper_title']);
            }
            if ($arrayOfContents['assigned_timeBefore']) {
                $stmt->bindValue(":before", $arrayOfContents['assigned_timeBefore']);
                $stmt->bindValue(":after", $arrayOfContents['assigned_timeAfter']);
            }
            if ($arrayOfContents['completed']) {
                $stmt->bindValue(":completed", $arrayOfContents['completed'], PDO::PARAM_BOOL);
            }

            //finishing binding
            if ($stmt->execute()) {
                while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (gettype($result) != 'boolean' && $result["paper_title"] != null) {
                        array_push($allResults, $result);
                    }
                }
                return $allResults;
            } else {
                return -1;
            }

        } catch (PDOException $e) {
            return -1;
        }
    }

}