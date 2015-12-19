<?php

$postData = json_decode(file_get_contents("php://input"));

if (isset($postData["action"])) {
    switch ($postData["action"]) {
        case "login":
            if (isset($postData["email"]) and isset($postData["password"])) {
                $loginHandler = new Person();
                $token = $loginHandler->assignToken($postData["email"], $postData["password"]);
                echo $token;
            } else {
                echo -2;
                break;
            }
            break;

        case "add-paper":
            $person = new Person();
            $result = $person->getConferenceManger($postData["token"]);
            if ($result === 0 or $result === -1) {
                echo $result;
                break;
            } else {
                $confManager = $result;
            }
            if (isset($postData["content"])) {
                $validate = FormValidator::validatePaperSubmitForm($postData["content"]);
                if ($validate === false) {
                    echo -2;
                } else {
                    $result = $confManager->addPaper(
                        $postData["content"]["title"],
                        $postData["content"]["authors"],
                        $postData["content"]["keywords"],
                        $postData["content"]["file"]);
                }
            } else {
                echo -2;
            }
            break;

        case "add-reviewer":
            $person = new Person();
            $result = $person->getConferenceManger($postData["token"]);
            if ($result === 0 or $result === -1) {
                echo $result;
                break;
            } else {
                $confManager = $result;
            }
            if (isset($postData["content"])) {
                $validate = FormValidator::validateAddReviewerForm($postData["content"]);
                if ($validate == 0) {
                    //tell client reviewer info is not correct.
                } else {
                    $result = $confManager->addReviewer($postData["content"]) != -1;
                    echo $result;
                }
            } else {
                echo -2;
            }
            break;

        case "remove-reviewer":

            break;

        case "add-submission":
            $person = new Person();
            $result = $person->getConferenceManger($postData["token"]);
            if ($result === 0 or $result === -1) {
                echo $result;
                break;
            } else {
                $confManager = $result;
            }
            if (isset($postData["content"])) {
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

    }
}