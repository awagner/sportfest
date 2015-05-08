<?php
require_once ('includes/config.php');
require_once ('includes/class.sportfest.php');
$sportfest = new sportfest();
$sportfest->doTask();
?>
<!DOCTYPE html>
<html class="ui-mobile"><head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sportfest</title>
        <link rel="stylesheet" href="jquerymobile/jquery.css">
        <link rel="stylesheet" href="jquerymobile/jqm-demos.css">
        <script src="jquerymobile/jquery_002.js"></script>
        <script src="jquerymobile/index.js"></script>
        <script src="jquerymobile/jquery.js"></script>
        <link rel="stylesheet" href="themes/sportfest-browser.css">
    </head>

    <body class="ui-mobile-viewport ui-overlay-a">

        <div style="min-height: 890px;" class="ui-page ui-page-theme-a ui-page-active" tabindex="0" data-role="page">

            <div role="main" class="ui-content">
                <?php echo $sportfest->display(); ?>
            </div><!-- /content -->
        </div><!-- /page -->
        <div class="ui-loader ui-corner-all ui-body-a ui-loader-default">
            <span class="ui-icon-loading"></span><h1>loading</h1>
        </div>
    </body>
</html>