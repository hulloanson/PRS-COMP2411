<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../util/Util.php';
include_once '../util/DBHelper.php';

//$paperDB = new PaperDB();
//$rows = $paperDB->getPaperToJudge();
//var_dump($rows);

//echo Decision::getPaperToJudgeRows();

//$reviewDB = new ReviewDB();
//$rows = $reviewDB->getReviewJobs(1);
//var_dump($rows);

$sql = "INSERT INTO Session (id, user_id, issued_time) VALUES (UNHEX(:token), 1, NOW())
                ON DUPLICATE KEY UPDATE id = :token AND issued_time = now();";

$token = Util::guidv4();

try {

    $stmt = DBHelper::getConnection()->prepare($sql);

    $stmt->bindValue(":token", $token);

    if ($stmt->execute()) {
        echo $stmt->rowCount();
    } else {
        echo -1;
    }

} catch (PDOException $e) {
    return -1;
}
//if (!0) {
//    echo "false";
//} else {
//    echo "true";
//}

//echo json_encode(12);

//echo json_encode("paragraph");

//$reviewer = array();
//
//echo empty($reviewer['areas']);

//try {
//    $sql = "SELECT (if((SELECT MAX(type) FROM Submission WHERE paper_id = 1) = NULL , 0, (SELECT MAX(type) FROM Submission WHERE paper_id = 1)+1)) AS 'max_type';";
//
//    $stmt = DBHelper::getConnection()->prepare($sql);
//
//    $result = 0;
//
//    if ($stmt->execute()) {
//        $stmt->bindColumn("max_type", $maxType, PDO::PARAM_INT);
//        if ($stmt->fetch()) {
//            echo $maxType;
//        } else {
//            echo "Lamer";
//        }
//    } else {
//        echo -1;
//    }
//} catch (PDOException $e) {
//    echo -1;
//}

//echo gettype(array());
//echo gettype(0);