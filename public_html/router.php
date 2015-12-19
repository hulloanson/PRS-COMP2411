<?php

$postData = json_decode(file_get_contents("php://input"));


if (isset($postData["token"]) and gettype($postData["token"]) == 'string') {
    if (isset($postData["action"]) and gettype($postData["action"]) == 'string') {
        switch ($postData["action"]) {
            case "login":
                if (FormValidator::formContentValid($postData["content"])) {
                    if (FormValidator::validateLoginForm($postData["content"])) {
                        $loginHandler = new Person();
                        $token = $loginHandler->assignToken(
                            $postData["content"]["email"],
                            $postData["content"]["password"]);
                        echo $token;
                    } else {
                        echo PRSErr::INCORRECT_CONTENT;
                    }
                } else {
                    echo PRSErr::INCORRECT_CONTENT;
                }
                break;

            case "add-paper":
                $person = new Person();
                $result = $person->getConferenceManger($postData["token"]);
                if ($result === PRSErr::NOTHING_FOUND or $result === PRSErr::DB_ERR) {
                    echo $result;
                    break;
                } else {
                    $confManager = $result;
                }
                if (FormValidator::formContentValid($postData["content"])) {
                    $validate = FormValidator::validatePaperSubmitForm($postData["content"]);
                    if ($validate === false) {
                        echo PRSErr::INCORRECT_CONTENT;
                    } else {
                        $result = $confManager->addPaper(
                            $postData["content"]["title"],
                            $postData["content"]["authors"],
                            $postData["content"]["keywords"],
                            $postData["content"]["file"]);
                    }
                } else {
                    echo PRSErr::INCORRECT_CONTENT;
                }
                break;

            case "add-reviewer":
                $person = new Person();
                $result = $person->getConferenceManger($postData["token"]);
                if ($result === PRSErr::NOTHING_FOUND or $result === PRSErr::DB_ERR) {
                    echo $result;
                    break;
                } else {
                    $confManager = $result;
                }
                if (FormValidator::formContentValid($postData["content"])) {
                    if (FormValidator::validateAddReviewerForm($postData["content"]) === false) {
                        echo PRSErr::INCORRECT_CONTENT;
                    } else {
                        $result = $confManager->addReviewer($postData["content"]) != -1;
                        echo $result;
                    }
                } else {
                    echo PRSErr::INCORRECT_CONTENT;
                }
                break;

            case "remove-reviewer":

                break;

            case "add-submission":
                $person = new Person();
                $result = $person->getConferenceManger($postData["token"]);
                if ($result === PRSErr::NOTHING_FOUND or $result === PRSErr::DB_ERR) {
                    echo $result;
                    break;
                } else {
                    $confManager = $result;
                }
                if (FormValidator::formContentValid($postData["content"])) {
                    if (FormValidator::validateAddSubmissionForm($postData["content"])) {
                        $result = $confManager->addSubmission(
                            $postData["content"]["paperId"],
                            $postData["content"]["file"],
                            $postData["content"]["fileMime"]
                        );
                        echo $result;
                    }
                }

                break;

            case "get-review-jobs":
                $person = new Person();
                $result = $person->getReviewer($token);
                if ($result === PRSErr::NOTHING_FOUND or $result === PRSErr::DB_ERR) {
                    echo $result;
                    break;
                } else {
                    $reviewer = $result;
                }
                $result = $reviewer->getReviewJobs();
                if ($result == PRSErr::NOTHING_FOUND or $result == PRSErr::DB_ERR) {
                    echo $result;
                } else {
                    echo json_encode($result);
                }
                break;

            case "get-review-job":
                $person = new Person();
                $result = $person->getReviewer($token);
                if ($result === PRSErr::NOTHING_FOUND or $result == PRSErr::DB_ERR) {
                    echo $result;
                    break;
                } else {
                    $reviewer = $result;
                }
                if (FormValidator::formContentValid($postData["content"])) {
                    $keys = $postData["content"];
                    if ($keys[0] == "reviewId") {
                        if (gettype($postData["content"]["reviewId"]) == 'integer') {
                            $result = $reviewer->getReviewJob($postData["content"]["reviewId"]);
                            if ($result == PRSErr::NOTHING_FOUND or $result == PRSErr::NOTHING_FOUND) {
                                echo Util::sendError($result);
                            } else {
                                echo Util::sendResult($result);
                            }
                        } else {
                            echo Util::sendError(PRSErr::INCORRECT_CONTENT);
                        }
                    } else {
                        echo Util::sendError(PRSErr::INCORRECT_CONTENT);
                    }
                }
                break;

            case "do-review":
                $person = new Person();
                $result = $person->getReviewer($token);
                if ($result === PRSErr::NOTHING_FOUND
                    or $result === PRSErr::DB_ERR
                ) {
                    echo $result;
                    break;
                } else {
                    $reviewer = $result;
                }
                if (FormValidator::formContentValid($postData["content"])) {
                    $result = FormValidator::validateDoReviewForm($postData["content"]);
                    if ($result === true) {
                        $result = $reviewer->doReview(
                            $postData["reviewId"],
                            $postData["file"],
                            $postData["fileMime"],
                            $postData["rating"]
                        );
                        if ($result === PRSErr::DB_ERR) {
                            echo Util::sendError($result);
                        } else {
                            echo Util::sendResult("success");
                        }
                    } else {
                        echo Util::sendError($result);
                    }
                } else {
                    echo Util::sendError(PRSErr::INCORRECT_CONTENT);
                }
                break;

            case "get-acc-info":
                $person = new Person();
                $userId = $person->tokenToUserId($postData["token"]);
                if ($userId === PRSErr::DB_ERR or $userId === PRSErr::NOTHING_FOUND) {
                    echo Util::sendError($userId);
                } else {
                    $user = new User($userId);
                    $result = $user->getOwnInformation();
                    if ($result === PRSErr::DB_ERR or $result === PRSErr::NOTHING_FOUND) {
                        echo Util::sendError($result);
                    } else {
                        echo Util::sendResult($result);
                    }
                }
                break;

//            case

        }
    } else {
        echo Util::sendError(PRSErr::INCORRECT_CONTENT);
    }
} else {
    echo Util::sendError(PRSErr::INCORRECT_CONTENT);
}