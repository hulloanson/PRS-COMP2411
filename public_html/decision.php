<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Conference Paper Review System</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div id="wrap">
    <div id="regbar">
        <table width="1200px" class="tableCentre">
            <tr>
                <td width="90%">
                    <div id="leftmargin"><h2>Paper Review System</h2></div>
                </td>
                <td align="right">
                    <div id="rightmargin">
                        <h2><a href="#" id="loginform">Login</a></h2>
                </td>
            <tr>
                <td><p>&nbsp </p></td>
                <td>
                    <div class="login">
                        <div class="arrow-up"></div>
                        <div class="formholder">
                            <div class="randompad">
                                <fieldset>
                                    <label name="email">Email</label>
                                    <input type="email"/>
                                    <label name="password">Password</label>
                                    <input type="password"/>
                                    <input type="submit" value="Login"/>

                                </fieldset>
                            </div>
                        </div>
                    </div>
    </div>
    </td></tr>

    </table>
</div>
</div>
<script src='jquery-2.1.4.min.js'></script>
<script src="js/index.js"></script>
<table class="table1">
    <tr>
        <td>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="work.php">Review</a></li>
                <li class="inpage"><a href="decision.php">Decision</a></li>
                <li><a href="search.php">Search paper</a></li>


            </ul>
        </td>
        <td width="80%">
            <table width="100%" class="table3">
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Type</th>
                </tr>
                <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                include_once '../model/util/autoload.php';
                $rows = Decision::getPaperToJudgeRows();
                if (!empty($rows)) {
                    echo $rows;
                } else {
//                    echo "No paper in waiting queue. Come back later ^^";
                }
                ?>
                <tr>
                    <td><a href="decisionMaking.php">Neuro-fuzzy and soft computing; a computational approach to
                            learning and
                            machine intelligence</a></td>
                    <td>Jang, J.S.R., Sun, C.T. and Mizutani, E.</td>
                    <td>Abstract</td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
