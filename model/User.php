<?php


class User extends DB
{
    protected $userId;

    function __construct($userId)
    {
        parent::__construct();
        $this->userId = $userId;
    }

//    public function getSubmission($submissionId)
//    {
//        if ($auth = Util::getAuth('submission', $submissionId, $this->userId) != -1) {
//            if (!$auth) {
//                return 0;
//            }
//        } else {
//            return -1;
//        }
//
//        $allRows = array();
//        $sql = "SELECT Paper.title AS 'paperTitle', group_concat(Author.name) AS 'authorsName',
//                Submission.type AS 'submitType', Submission.time AS 'submitTime'
//                FROM Paper, Author, Author_Paper, Submission
//                WHERE Paper.id = Author_Paper.paper_id AND Author.id = Author_Paper.author_id
//                AND Submission.paper_id = Paper.id
//                AND Submission.id = :submissionId";
//
//        try {
//            $stmt = $this->conn->prepare($sql);
//
//            $stmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);
//
//            if ($stmt->execute()) {
//                while ($row = $stmt->fetch(PDO::FETCH_ASSOC) != false) {
//                    array_push($allRows, $row);
//                }
//                return $allRows;
//            } else {
//                return -1;
//            }
//
//        } catch (PDOException $e) {
//            return -1;
//        }
//    }
//
//    public function getSubmissionFile($submissionId)
//    {
//        if ($auth = Util::getAuth('submission', $submissionId, $this->userId) != -1) {
//            if (!$auth) {
//                return 0;
//            }
//        } else {
//            return -1;
//        }
//
//        $sql = "SELECT file, file_mime FROM Submission WHERE Submission.id = :submissionId";
//
//        try {
//            $stmt = $this->conn->prepare($sql);
//
//            $stmt->bindValue(":submissionId", $submissionId, PDO::PARAM_INT);
//
//            if ($stmt->execute()) {
//                $stmt->bindColumn("file", $file, PDO::PARAM_LOB);
//                $stmt->bindColumn("file_mime", $fileMime);
//                if ($stmt->fetch(PDO::FETCH_BOUND)) {
//                    return array($file, $fileMime);
//                } else {
//                    return 0;
//                }
//            } else {
//                return -1;
//            }
//        } catch (PDOException $e) {
//            return -1;
//        }
//    }

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

    }

    function getOwnGender() {

    }

    function setOwnGender($gender) {

    }

    function getOwnName() {

    }

    function setOwnFirstName() {

    }


}