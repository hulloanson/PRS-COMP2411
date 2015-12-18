<?php


class FormValidator
{

    static function validatePaperSubmitForm($array)
    {
        if (gettype($array) != 'array') {
            return 0;
        }
        $paperProps = array("authors", "file", "keywords", "title");
        $keys = array_keys($array);
        sort($keys);

        if ($keys == $paperProps) {
            if (gettype($array["authors"]) == 'array'
                and gettype($array["keywords"]) == 'array'
            ) {
                return $array;
            }
        }

        return 0;
    }


}