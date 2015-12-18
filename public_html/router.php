<?php

$postData = json_decode(file_get_contents("php://input"));

if (isset($postData["action"])) {
    switch ($postData["action"]) {
        case "login":
            if (isset($postData["email"]) and isset($postData["password"])) {
                $loginHandler = new Person();
                $token = $loginHandler->assignToken($postData["email"], $postData["password"]);
                if ($token == -1) {
                    //tell client there's problems
                } elseif ($token == 0) {
                    //tell client credentials are incorrect
                } else {
                    //send token to client
                }

            } else {
                //tell client wrong information is sent.
            }
            break;

        case "add-paper":
            if (isset($postData["token"])) {
                $person = new Person();
                if ($person->getRole($postData["token"])) {
                    if ($person->isConferenceManager()) {
                        $userId = $person->tokenToUserId($postData["token"]);
                        $confManager = new ConferenceManager($userId);
                    } else {
                        //tell client that they don't fucking have the permission
                    }
                }
                if (isset($postData["content"])) {
                    $validate = FormValidator::validatePaperSubmitForm($postData["content"]);
                    if ($validate == 0) {
                        //tell client paper info is not correct.
                    } else {
                        $result = $confManager->addPaper(
                            $postData["content"]["title"],
                            $postData["content"]["authors"],
                            $postData["content"]["keywords"],
                            $postData["content"]["file"]);
                    }
                }
            } else {
                // tell client they are not logged-in
            }
            break;

        case "add-reviewer":

    }
}