<?php


class Reviewer extends User
{
    function __construct($userId)
    {
        parent::__construct($userId);
    }

    function getReviewJobs() {
        $allRows = array();
        $sql = "SELECT paper_title, authors_name, review_id, completed
                FROM Review_Record_View WHERE reviewer_id = :userId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":userId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC) != false) {
                    array_push($allRows, $row);
                }
                if (sizeof($allRows) == 0) {
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

    function getReviewJob($reviewId) {
        $sql = "SELECT paper_title, authors_name, completed, completed_time, assigned_time, submission_id, review_id
                FROM Review_Record_View where review_id = :reviewId AND reviewer_id = :userId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":reviewId", $reviewId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC) != false) {
                    return $row;
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

    function doReview($reviewId, $file, $fileMime, $rating) {
        $sql = "UPDATE Review_Record SET file = :filee, file_mime = :fileMime, completed = true, rating = :rating,
                completed_time = NOW() where id = :reviewId and reviewer_id = :reviewerId";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":filee", $file, PDO::PARAM_LOB);
            $stmt->bindValue(":fileMime", $fileMime);
            $stmt->bindValue(":rating", $rating, PDO::PARAM_INT);
            $stmt->bindValue(":reviewId", $reviewId, PDO::PARAM_INT);
            $stmt->bindValue(":reviewerId", $this->userId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return -1;
            }
        } catch (PDOException $e) {
            return -1;
        }
    }
}