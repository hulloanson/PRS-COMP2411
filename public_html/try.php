<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', 100 * 1024 * 1024);

//include_once '../util/Util.php';
//include_once '../util/DBHelper.php';
//
//$paperDB = new PaperDB();
//$rows = $paperDB->getPaperToJudge();
//var_dump($rows);

//echo Decision::getPaperToJudgeRows();

//$reviewDB = new ReviewDB();
//$rows = $reviewDB->getReviewJobs(1);
//var_dump($rows);

//$sql = "INSERT INTO Session (id, user_id, issued_time) VALUES (UNHEX(:token), 1, NOW())
//                ON DUPLICATE KEY UPDATE id = :token AND issued_time = now();";
//
//$token = Util::guidv4();
//
//try {
//
//    $stmt = DBHelper::getConnection()->prepare($sql);
//
//    $stmt->bindValue(":token", $token);
//
//    if ($stmt->execute()) {
//        echo $stmt->rowCount();
//    } else {
//        echo -1;
//    }
//
//} catch (PDOException $e) {
//    return -1;
//}
//if (!0) {
//    echo "false";
//} else {
//    echo "true";
//}

//if (isset($_FILES['userfile']['name'])) {
//    echo "got it.";
//} else {
//    echo "huh?";
//}

//$str = base64_encode(str_repeat('a', 32 * 1024 * 1024));
//
//echo (strlen($str) / 1024 / 1024) / (strlen());


//$authors = array("one" => "ABC", "BCD");
//
//$authors_seq = array("ABC", "BCD");
//
//var_dump(isAssoc($authors));
//
//var_dump(isAssoc($authors_seq));
//
//var_dump(isAssoc(1));
//
//$lamer = "X";
//$n = true;
//$x = true;
//
//switch ($lamer) {
//    case "Y":
//        echo "kai";
//        break;
//    case "X":
//        if ($n) {
//            echo "shit!";
//            break;
//        }
//        if ($x) {
//            echo "omg!";
//        }
//        break;
//    default:
//        echo "eh.";
//        break;
//}

$submissionProps = array("file", "fileMime", "paperId");

sort($submissionProps);
var_dump($submissionProps);

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