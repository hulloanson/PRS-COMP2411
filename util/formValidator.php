<?php


class FormValidator
{

    static function validatePaperSubmitForm($array)
    {
        if (gettype($array) != 'array' and self::isAssoc($array)) {
            return false;
        }
        $paperProps = array("authors", "file", "keywords", "title");
        $keys = array_keys($array);
        sort($keys);

        if ($keys == $paperProps) {
            if (gettype($array["authors"]) == 'array' and self::isAssoc($array["authors"]) === false
                and gettype($array["keywords"]) == 'array' and self::isAssoc($array["keywords"]) == false
            ) {
                return true;
            }
        }

        return false;
    }

    static function validateAddReviewerForm($array)
    {
        if (gettype($array) != 'array' and self::isAssoc($array)) {
            return false;
        }
        $reviewerProps = array(
            "address",
            "areas",
            "city",
            "country",
            "department",
            "email",
            "fax",
            "firstName",
            "gender",
            "lastName",
            "organisations",
            "password",
            "phone",
            "title"
        );
        $keys = array_keys($array);
        sort($keys);

        if ($keys == $reviewerProps) {
            if (gettype($array["areas"]) == 'array' and self::isAssoc($array["areas"]) === false
                and gettype($array["organisations"] == 'array') and self::isAssoc($array["organisations"] === false)
            ) {
                return true;
            }
        }

        return false;
    }

    static function validateAddSubmissionForm($array)
    {

        if (gettype($array) != 'array') {
            return false;
        }

        $submissionProps = array("file", "fileMime", "paperId");
        $keys = array_keys($array);
        sort($keys);

        if ($keys == $submissionProps) {
            return true;
        }
        return false;
    }

    private static function isAssoc($arr)
    {
        if (gettype($arr) != 'array') {
            return -1;
        } else {
            return array_keys($arr) !== range(0, count($arr) - 1);
        }
    }
}