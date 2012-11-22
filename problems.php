<?
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
        <!-- <link href="css/font-awesome.css" rel="stylesheet"> -->
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
            <table id="probTable" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"><thead><tr><th>Hostname</th><th>Service</th><th>Status</th><th>Date</th><th>Options</th></tr></thead>
            <tbody>
            <?
            foreach($statusArray['down_services'] as $hostName => $serviceArray)
            {
                foreach($serviceArray as $serviceName => $serviceDetail)
                {
                    echo "<tr>\n";
                    echo "<td>{$hostName}</td>\n";
                    echo "<td>{$serviceName}</td><td>{$serviceDetail['last_out']}</td>\n";
                    echo "<td>".date("d/m/Y H:i",$serviceDetail['down_since'])."</td>\n";
                    echo "<td>\n";
                    ?>
                    <button class="btn btn-small"><i class="size18 icon-comment"></i> Comment</button>

                    <?
                    echo "</td>\n";
                    echo "</tr>\n";
                }
            }
            ?>
</tbody>
</table>
<br />
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
