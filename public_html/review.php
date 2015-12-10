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
                <li><a href="decision.php">Decision</a></li>
                <li><a href="search.php">Search paper</a></li>
                <li><a href="#">TempButton 5</a></li>
                <li><a href="#">TempButton 6</a></li>
            </ul>
        </td>
        <td width="80%">
            <p>&nbsp </p>

            <h1>Review</h1>

            <p>&nbsp </p>
            <table width="100%" class="table2">
                <tr>
                    <th width="20%"><p>Title</p></th>
                    <td><p> <!--sql code--> Neuro-fuzzy and soft computing; a computational approach to learning and
                            machine intelligence </p></td>
                </tr>
                <tr>
                    <th><p>Author</p></th>
                    <td><p><!--sql code--> Jang, J.S.R., Sun, C.T. and Mizutani, E.</p></td>
                </tr>
                <tr>
                    <th><p>Submission Type</p></th>
                    <td><p> <!--sql code--> Paper</p></td>
                </tr>
                <tr>
                    <th><p>Keywords</p></th>
                    <td><p> <!--sql code--> Keywords</p></td>
                </tr>
                <tr>
                    <th><p>Download</p></th>
                    <td><a href="#"><img src="download.png" width="42" height="42" border"0"><font color="1a1aff"><u>Click
                                    here to download</u></a></td>
                </tr>
                <tr>
                    <th><p>Submit Review</p></th>
                    <td>
                        <form action="upload.php" method="post" enctype="multipart/form-data">
                            <p>Select file to upload:</p>
                            <input type="file" name="fileToUpload" id="fileToUpload"
                                   accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,text/plain">
                            <input type="submit" value="Upload review" name="submit">
                        </form>
                    </td>
                </tr>
            </table>
            <p>&nbsp </p>
            <a href="javascript:history.back()"><font color="1a1aff"><u>Back to the previous page</u></a>
        </td>
    </tr>
</table>

</body>
</html>
