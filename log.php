<?php
require_once('includes/init.php');
require_once('includes/nagios.class.php');
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>Nagios NG - LINX </title>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap-responsive.css">
		<link rel="stylesheet" href="css/unicorn.css">
		<link rel="stylesheet" href="css/unicorn_002.css" class="skin-color">	
        <link href="css/nagios-ng.css" rel="stylesheet">
        <link href="css/jquery.dataTables.css" rel="stylesheet">
        <link href="css/jquery.dataTables_themeroller.css" rel="stylesheet">
        <link href="css/font-awesome.css" rel="stylesheet">
        <link href="css/custom-theme/jquery-ui-1.8.23.custom.css" rel="stylesheet">
        </head>
<body>
		<div id="header">
		</div>
		

		<div id="content">
			<div id="content-header">
				<h1>Nagios NG</h1>
				<div class="btn-group">
					<a data-original-title="Manage Files" class="btn btn-large tip-bottom"><i class="icon-file"></i></a>
					<a data-original-title="Manage Users" class="btn btn-large tip-bottom"><i class="icon-user"></i></a>
					<a data-original-title="Manage Comments" class="btn btn-large tip-bottom"><i class="icon-comment"></i><span class="label label-important">5</span></a>
					<a data-original-title="Manage Orders" class="btn btn-large tip-bottom"><i class="icon-shopping-cart"></i></a>
				</div>
			</div>
            <?php
            renderNav();
            ?>

<br />
                                    <table class="table table-condensed table-bordered">
                                    <thead><tr><th>Host</th>
                                    <th>Info</th><th>Time</th></tr></thead>
                                    <tbody>
                                    <?
                                    foreach($logArray as $info => $time)
                                    {
                                        $showRow=0;
                                        preg_match("/(.*?): (.*?);(.*?)$/",$info,$detailsMatch);
                                        $lineType=$detailsMatch[1];
                                        $lineHost=$detailsMatch[2]; 
                                        $lineInfo=$detailsMatch[3]; 
                                        if(preg_match("/CURRENT SERVICE STATE: (.*?);(.*?);(.*?);(.*?);(.*?);(.*?)$/",$info,$infoMatch))
                                        {
                                            $showRow=1;
                                            $host=$infoMatch[1];
                                            $service=$infoMatch[2];
                                            $state=$infoMatch[3];
                                            $type=$infoMatch[4];
                                            $status=$infoMatch[5];
                                            $details=$infoMatch[2];
                                        }
                                        elseif(preg_match("/CURRENT HOST STATE: (.*?);(.*?);(.*?);(.*?);(.*?)$/",$info,$infoMatch))
                                        {
                                            $showRow=1;
                                            $host=$infoMatch[1];
                                            $service=$infoMatch[2];
                                            $state=$infoMatch[3];
                                            $type=$infoMatch[4];
                                            $status=$infoMatch[5];
                                            $details=$infoMatch[6];
                                            if(preg_match("/PING OK/",$info))
                                            {
                                                $state=$infoMatch[2];
                                                $details=$infoMatch[5];

                                            }
                                        }
                                        elseif(preg_match("/SERVICE ALERT: (.*?);(.*?);(.*?);(.*?);(.*?);(.*?)$/",$info,$infoMatch))
                                        {
                                            $showRow=1;
                                            $host=$infoMatch[1];
                                            $service=$infoMatch[2];
                                            $state=$infoMatch[3];
                                            $type=$infoMatch[4];
                                            $status=$infoMatch[3];
                                            $details=$infoMatch[6];
                                            if(preg_match("/\(index [0-9]+\)/",$details ))
                                            {
                                                $details=$infoMatch[2];
                                            }
                                        }
                                        elseif(preg_match("/SERVICE FLAPPING ALERT: (.*?);(.*?);(.*?);(.*?)$/",$info,$infoMatch))
                                        {
                                            $showRow=1;
                                            $host=$infoMatch[1];
                                            $service=$infoMatch[2];
                                            $state=$infoMatch[3];
                                            $type=$infoMatch[4];
                                            $status=$infoMatch[3];
                                            $details=$infoMatch[4];
                                        }
                                        if($showRow==1)
                                        {
                                            $details=str_replace("OK -","",$details);
                                            $details=str_replace("OK:","",$details);
                                            $details=str_replace("CRITICAL:","",$details);
                                            $details=str_replace("Warning : Highest component temperature is","",$details);
                                            $details=str_replace("Highest component temperature is","",$details);
                                            $state=str_replace("STARTED","FLAP",$state);
                                            switch($state)
                                            {
                                                case 'OK':
                                                    $class = "rowOk";
                                                break;
                                                case 'WARNING':
                                                    $class = "rowWarning";
                                                break;
                                                case 'CRITICAL':
                                                    $class = "rowCritical";
                                                break;
                                                default:
                                                    $class='';
                                                break;    
                                            }
                                            echo "<tr rel=\"popover\" data-content=\"{$lineInfo}\" data-original-title=\"Hostname\" class=\"{$class} popRow\">\n";
                                            echo "<td>{$host}</td>\n";
                                            // echo "<td>{$state}</td>\n";
                                            echo "<td>{$details}</td>\n";
                                            echo "<td>".date("d/m H:i",$time)."</td>\n";
                                            echo "</tr>\n";

                                        }
                                        elseif(preg_match("/Auto-save/",$info))
                                        {
                                        }
                                        elseif(preg_match("/Caught SIGHUP/",$info))
                                        {
                                        }

                                        elseif(preg_match("/Nagios.*?starting/",$info))
                                        {
                                        }
                                        elseif(preg_match("/has no services associated/",$info))
                                        {
                                        }
                                        elseif(preg_match("/Local time/",$info))
                                        {
                                        }
                                        elseif(preg_match("/LOG VERSION/",$info))
                                        {
                                        }
                                        elseif(preg_match("/LOG ROTATION/",$info))
                                        {
                                        }
                                        else
                                        {
                                            echo $info;
                                            exit;
                                            /*
                                            echo "<tr>\n";
                                            echo "<td>Host</td>\n";
                                            echo "<td>UP</td>\n";
                                            echo "<td>{$info}</td>\n";
                                            echo "<td>".date("d/m H:i",$time)."</td>\n";
                                            echo "</tr>\n";
                                            */
                                        }
                                        if($logShow>40)
                                        {
                                            break;
                                        }
                                        $logShow++;
                                    }
                                    ?>
                                    </tbody>
                                    </table>

</div>
</body>
</html>

<script src="js/jquery-1.8.2.min.js"></script>
<script src="js/jquery.dataTables.js"></script>
<script>

$(document).ready(function() {
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "formatted-num-pre": function ( a ) {
        a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
        return parseFloat( a );
    },
 
    "formatted-num-asc": function ( a, b ) {
        return a - b;
    },
 
    "formatted-num-desc": function ( a, b ) {
        return b - a;
    }
} );


jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "date-uk-pre": function ( a ) {
        var ukDatea = a.split('/');
        var ukTime = ukDatea[2].split(' ');
        var ukTime2 = ukTime[1].split(':');
        return (ukTime[0] + ukDatea[1] + ukDatea[0] + ukTime2[0] + ukTime2[1] ) * 1;
    },
 
    "date-uk-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "date-uk-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );


    $('#probTable').dataTable( {
        "sWrapper": "dataTables_wrapper form-inline",
                "aoColumnDefs" : [
                   {"aTargets" : [3] , "sType" : "date-uk"},
                ],
    } );
} );
</script>
