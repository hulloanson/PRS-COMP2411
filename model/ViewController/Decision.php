<?php


class Decision
{
    static function getPaperToJudgeRows()
    {
        $paperDB = new PaperDB();
        $rows = $paperDB->getPaperToJudge();
//        var_dump($rows[0]);
//        var_dump($rows);
//        echo sizeof($rows);
//        if (sizeof($rows) == 0) {
//            return "";
//        }
        $htmlToReturn = "";
        for ($i = 0; $i < sizeof($rows); $i++) {
//            echo "loop called";
            $authors = explode(",", $rows[$i]["authorsName"]);
            $htmlToReturn .= "<tr>";
            $htmlToReturn .= "<td>" . $rows[$i]["paperTitle"] . "</td>";
            $htmlToReturn .= "<td>" . Util::printPrettyAuthorNames($authors) . "</td>";
            $htmlToReturn .= "<td>" . Util::submitTypeToString($rows[$i]['submitType']) . "</td>";
            $htmlToReturn .= "</tr>";
        }
        return $htmlToReturn;
    }
}