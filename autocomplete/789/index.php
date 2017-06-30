<?php
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>

        <title>jQuery Autocomplete Plugin</title>
        <script type="text/javascript" src="js/jquery-1.4.2.js"></script>
        <script type='text/javascript' src="js/jquery.autocomplete.js"></script>
        <link rel="stylesheet" type="text/css" href="js/jquery.autocomplete.css" />
        <script type="text/javascript">
            $().ready(function() {
                $("#course").autocomplete("autoComplete.php", {
                    width: 260,
                    matchContains: true,
                    //mustMatch: true,
                    //minChars: 0,
                    //multiple: true,
                    //highlight: false,
                    //multipleSeparator: ",",
                    selectFirst: false
                });
            });
        </script>
    </head>
    <body>
        <h2>Autocomplete usando jQuery, Ajax, PHP</h1>
        <div id="content">
            <form autocomplete="off">
                <p>
                    Digite um nome:
                    <input type="text" name="course" id="course" />
                </p>

            </form>
        </div>
</body>
</html>
