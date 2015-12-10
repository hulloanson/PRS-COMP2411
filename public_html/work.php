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
    <script src='jquery-2.1.4.min.js'></script>
    <script src="js/index.js"></script>
    <script>
        $(function () {
            $('#header').load("header.html");
        });
    </script>
</head>
<body>
<!--<div id="header"></div>-->
<div id="wrap">
    <div id="regbar">
        <table width="1200px" class="tableCentre">
            <tr>
                <td width="90%">
                    <div id="leftmargin"><h2>Paper Review System</h2></div>
                </td>
                <td align="right">
                    <div id="rightmargin">
                        <?php
                        session_start();
                        if ($_SESSION['login'] == true) {
                            ?>
                            <form action="router.php" method="post">
                                <input name="original" value="work" style="display: none"/>
                                <input name="action" value="Logout" class="logout" type="submit"/>
                            </form>

                            <?php
                        } else {
                            ?>
                            <h2><a href='#' id="loginform">Login</a></h2>
                            <?php
                        }
                        ?>
                </td>
            <tr>
                <td><p>&nbsp </p></td>
                <td>
                    <div class="login">
                        <div class="arrow-up"></div>
                        <div class="formholder">
                            <div class="randompad">
                                <?php
                                if (!isset($_SESSION['login']) or !$_SESSION['login']) {
                                    ?>
                                    <form action="router.php" method="post">
                                        <fieldset>
                                            <label name="email">Email</label>
                                            <input type="email" name="email"/>
                                            <label name="password">Password</label>
                                            <input type="password" name="password"/>
                                            <input type="submit" value="Login"/>
                                            <input name="action" value="login" style="display: none"/>
                                        </fieldset>
                                    </form>
                                    <?php
                                } else {
                                    ?>
                                    <h2>HI</h2>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
    </div>
    </td></tr>

    </table>
</div>
</div>

<table class="table1">
    <tr>
        <td>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li class="inpage"><a href="work.php">Review</a></li>
                <li><a href="decision.php">Decision</a></li>
                <li><a href="search.php">Search paper</a></li>

            </ul>
        </td>
        <td width="80%">

            <h1>Your review jobs</h1>

            <table width="100%" class="table3">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Assigned Date</th>
                    <th>Type</th>
                    <th>Completed</th>
                </tr>

                <?php
                include_once '../model/util/autoload.php';
                $reviewerId = 1;
                echo Work::getWorkRows($reviewerId);
                ?>
                <tr>
                    <td>1</td>
                    <td><a href="review.php">Neuro-fuzzy and soft computing; a computational approach to learning and
                            machine intelligence</a></td>
                    <td>Jang, J.S.R., Sun, C.T. and Mizutani, E.</td>
                    <td>2015-12-09 13:12:41</td>
                    <td>Abstract</td>
                    <td>No</td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
