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
            <?
            renderNav();
            ?>
			<div class="container-fluid">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
					        <div class="span12">
                                <div class="widget-box">
                                <div class="widget-title ">
                                    <span class="icon">
                                         <span class="badge badge-important">6</span> 
                                    </span>
                                    <h5>Hosts Down</h5>
                                </div>
                                <div class="widget-content">
                                <br />
                                    <table class="table table-bordered table-condensed table-striped"><thead><tr><th>Hostname</th><th>Output</th><th>Down Since</th></tr></thead>
                                    <tbody>
                                    <?
                                    foreach($statusArray['down_hosts'] as $hostName => $output)
                                    {
                                        $niceTime = nicetime($output['down_since']); 
                                        echo "<tr>";
                                        echo "<td><a href=\"#\">{$hostName}</a></td><td>{$output['last_out']}</a></td><td>".date("d/m/Y H:i",$output['down_since'])." ({$niceTime}) </td>\n";
                                        echO "</tr>\n";
                                    }
                                    ?>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                                            </div>
                </div>    


            </div>
                <div class="row-fluid">
					<div class="span12">
                        <div class="row-fluid">
                            <div class="span12">
                                <div class="widget-box">
                                    <div class="widget-title ">
                                        <span class="icon">
                                             <span class="badge badge-warning">6</span> 
                                        </span>
                                        <h5>Service Problems</h5>
                                    </div>
                                        <?
                                        
                                        $shownArray=array();
                                        echo "<div class=\"widget-content\">\n";
                                        echo "<div class=\"row-fluid\">\n";
                                        foreach($statusArray['down_services'] as $hostName => $serviceArray)
                                        {
                                            if(!in_array($hostName,$shownArray))
                                            {
                                                echo "<div class=\"span3\">\n";
                                                echo "<div class=\"hostBox-{$hostName} widget-box\">\n";
                                                echo "<div class=\"widget-title\">\n";
                                                echo "<a id=\"{$hostName}\" name=\"{$hostName}\"></a>\n";
                                                echo "<h5>{$hostName}</h5>\n";
                                                echO "</div>\n";
                                                echo "<div class=\"hostBox-{$hostName} widget-content\">\n";
                                                echo "<table class=\"table table-condensed\">\n";
                                                foreach($serviceArray as $serviceName => $serviceDetails)
                                                {
                                                    $niceTime = nicetime($serviceDetails['down_since']); 
                                                    $serviceName = str_replace($hostName."-",'',$serviceName);
                                                    echo "<tr><td width=\"33%\" >\n";
                                                    echo "<a data-content=\"{$serviceDetails['last_out']}
                                                    <br /><textarea></textarea><br /><a class='btn btn-mini'>Add Note</a>\" rel=\"popover\" class=\"showPop\" href=\"#\" data-original-title=\"{$niceTime}: ".date("d/m H:i",$serviceDetails['down_since'])."\">\n";
                                                    echo $serviceName."</a> ";
                                                    echo "</td><td>\n";
                                                    switch($serviceName)
                                                    {
                                                        case 'Chassis':
                                                            if(preg_match("/Chassis reports (.*?) NOT running!/",$serviceDetails['last_out'],$psuArray))
                                                            {
                                                                echo "<span class=\"label label-warning\">{$psuArray[1]}</span> ";
                                                            }
                                                            else
                                                            {
                                                                echo $serviceDetails['last_out'];
                                                            }
                                                        break;
                                                        case 'Temperature':
                                                            if(preg_match("/temperature is ([0-9]+) C/",$serviceDetails['last_out'],$tempArray))
                                                            {
                                                                echo "<span class=\"label label-warning\">{$tempArray[1]} C</span> ";
                                                            }
                                                            else
                                                            {
                                                                echo $serviceDetails['last_out'];
                                                            }
                                                        break;
                                                        default:
                                                            if(preg_match("/is administratively down/",$serviceDetails['last_out']))
                                                            {
                                                                echo "<span class=\"label label-warning\">Admin {$serviceDetails['index']} Down</span> ";
                                                            }elseif(preg_match("/Either a valid snmp key/",$serviceDetails['last_out']))
                                                            {
                                                                echo "<span class=\"label label-important\">SNMP Error</span> ";
                                                            }elseif(preg_match("/is down/",$serviceDetails['last_out']))
                                                            {
                                                                echo "<span class=\"label label-important\">Down {$serviceDetails['index']}</span> ";
                                                            }
                                                        break;
                                                    }
                                                    echo "</td><td width=\"20%\" >\n";
                                                    echo $niceTime;
                                                    echo "</td></tr>\n";
                                                }
                                                echo "</table>\n";
                                                echo "</div>\n";
                                                echo "</div>\n";
                                                echo "</div>\n";
                                                $shownArray[] = $hostName;
                                                $numShown++;
                                                if(($numShown % 4) == 0)
                                                {
                                                    echo "</div><div class=\"row-fluid\">\n";
                                                }
                                            }
                                            /*
                                            foreach($serviceArray as $serviceName => $serviceDetails)
                                            {
                                                $niceTime = nicetime($serviceDetails['down_since']); 
                                                echo "<tr class=\"serviceRow hostrow-{$hostName}\" data-hostname=\"{$hostName}\">";
                                                echo "<td><a href=\"#\">{$hostName}</a></td><td>{$serviceName}</td>";
                                                echo "<td> <span class=\"badge badge-warning\">&nbsp;</span> {$serviceDetails['last_out']}</a></td>";
                                                echo "<td>".date("d/m/Y  H:i",$serviceDetails['down_since'])."</td>\n";
                                                echO "</tr>\n";
                                            }
                                            */
                                        }
                                        echo "</div>\n";
                                        echo "</div>\n";
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
					                </div>
                <div class="row-fluid">
					<div class="span6">
				    </div>
					</div>
					<div class="span6">

					</div>

				</div>

				<div class="row-fluid">
					<div id="footer" class="span12">
						Theme inspired by Unicorn Admin. Brought to you by <a href="https://wrapbootstrap.com/user/diablo9983">diablo9983</a>

        <pre>
        <?php
        print_r($statusArray['down_index']);
        ?>
					</div>
				</div>
			</div>

		</div>
		
		
            
</body></html>

<script src="js/jquery-1.8.1.min.js"></script>
<script src="js/jquery-ui-1.8.23.custom.min.js"></script>

<script src="js/jquery.dataTables.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.tablesorter.js"></script>
<script>
$(document).ready(function() {
    $(".hostLink").click(function() {
        var hostName = $(this).attr('rel');
         $('html, body').animate({
             scrollTop: $("#"+hostName).offset().top
         }, 200);
         $('.hostBox-'+hostName).effect("highlight", {}, 3000);

     });

    $('.showPop').popover({
    }).click(function(e)
    {
                e.preventDefault();
    });
    $('.popRow').popover({
        placement:'left',
        trigger:'hover',
        delay:800
    }).click(function(e)
    {
    e.preventDefault();
    });

    $.tablesorter.addParser({
        id: "datetime",
        is: function(s) {
            return false; 
        },
        format: function(s,table) {
            s = s.replace(/\-/g,"/");
            s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1");
            return $.tablesorter.formatFloat(new Date(s).getTime());
        },
        type: "numeric"
    });

    $('#serviceTable').tablesorter({
        dateFormat: 'dd/mm/yyyy', 
        headers: 
            {
                3:{sorter:'datetime'}
            } ,
        sortList: [[3,1]]
    });
    $('.serviceRow').click(function()
    {
        $('.highlightRow').removeClass('highlightRow');
        var hostName = $(this).attr('data-hostname');
        $('.hostrow-'+hostName).addClass('highlightRow');
        $(this).addClass('highlightRow');
    });

});
</script>
