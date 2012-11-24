<?php
/*
Nav config
*/
$navArray = array("dash.php" => "Dashboard",
                  "problems.php" => "Problems",
                  "log.php" => "Log",
                  );


function renderNav()
{
    global $navArray;
    ?>
    <div id="breadcrumb">
    <?
    $thisPage = basename($_SERVER['PHP_SELF']);
    foreach($navArray as $page => $title)
    {
        $class = $thisPage == $page ? 'current' : '' ;
        echo "<a href=\"{$page}\" class=\"{$class}\" >{$title}</a>\n";
    }
    ?></div><?
}
?>
