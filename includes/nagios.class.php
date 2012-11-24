<?php
$logFile = '/var/log/nagios3/nagios.log';
$statusFile = '/var/cache/nagios3/status.dat';

class nagiosNG {

    function nagiosNG()
    {
        global $statusFile;
        $this->statusFile = $statusFile;
    }

        function print_tree($data)
    {


    }



    // parse nagios3 status.dat
    function getData3($statusFile)
    {
      global $debug;
        // the keys to get from host status:
      $host_keys = array('host_name', 'modified_attributes', 'check_command', 'check_period', 'notification_period', 'check_interval', 'retry_interval', 'event_handler', 'has_been_checked', 'should_be_scheduled', 'check_execution_time', 'check_latency', 'check_type', 'current_state', 'last_hard_state', 'last_event_id', 'current_event_id', 'current_problem_id', 'last_problem_id', 'plugin_output', 'long_plugin_output', 'performance_data', 'last_check', 'next_check', 'check_options', 'current_attempt', 'max_attempts', 'state_type', 'last_state_change', 'last_hard_state_change', 'last_time_up', 'last_time_down', 'last_time_unreachable', 'last_notification', 'next_notification', 'no_more_notifications', 'current_notification_number', 'current_notification_id', 'notifications_enabled', 'problem_has_been_acknowledged', 'acknowledgement_type', 'active_checks_enabled', 'passive_checks_enabled', 'event_handler_enabled', 'flap_detection_enabled', 'failure_prediction_enabled', 'process_performance_data', 'obsess_over_host', 'last_update', 'is_flapping', 'percent_state_change', 'scheduled_downtime_depth');
      // keys to get from service status:
      $service_keys = array('host_name', 'service_description', 'modified_attributes', 'check_command', 'check_period', 'notification_period', 'check_interval', 'retry_interval', 'event_handler', 'has_been_checked', 'should_be_scheduled', 'check_execution_time', 'check_latency', 'check_type', 'current_state', 'last_hard_state', 'last_event_id', 'current_event_id', 'current_problem_id', 'last_problem_id', 'current_attempt', 'max_attempts', 'state_type', 'last_state_change', 'last_hard_state_change', 'last_time_ok', 'last_time_warning', 'last_time_unknown', 'last_time_critical', 'plugin_output', 'long_plugin_output', 'performance_data', 'last_check', 'next_check', 'check_options', 'current_notification_number', 'current_notification_id', 'last_notification', 'next_notification', 'no_more_notifications', 'notifications_enabled', 'active_checks_enabled', 'passive_checks_enabled', 'event_handler_enabled', 'problem_has_been_acknowledged', 'acknowledgement_type', 'flap_detection_enabled', 'failure_prediction_enabled', 'process_performance_data', 'obsess_over_service', 'last_update', 'is_flapping', 'percent_state_change', 'scheduled_downtime_depth');

        # open the file
        $fh = fopen($this->statusFile, 'r');

        # variables to keep state
        $inSection = false;
        $sectionType = "";
        $lineNum = 0;
        $sectionData = array();
        $numProblems = 0;

        $hostStatus = array();
        $serviceStatus = array();
        $programStatus = array();

        #variables for total hosts and services
        $typeTotals = array();
        
        # loop through the file
        while($line = fgets($fh))
        {
        $lineNum++; // increment counter of line number, mainly for debugging
        $line = trim($line); // strip whitespace
        if($line == ""){ continue;} // ignore blank line
        if(substr($line, 0, 1) == "#"){ continue;} // ignore comment
        
        // ok, now we need to deal with the sections
        if(! $inSection)
        {
            // we're not currently in a section, but are looking to start one
          if(substr($line, strlen($line)-1, 1) == "{") // space and ending with {, so it's a section header
            {
            $sectionType = substr($line, 0, strpos($line, " ")); // first word on line is type
            $inSection = true;
            // we're now in a section
            $sectionData = array();

            // increment the counter for this sectionType
            if(isset($typeTotals[$sectionType])){$typeTotals[$sectionType]=$typeTotals[$sectionType]+1;}else{$typeTotals[$sectionType]=1;}
            
            }
        }
            elseif($inSection && trim($line) == "}") // closing a section
        {
            if($sectionType == "servicestatus")
            {
            $serviceStatus[$sectionData['host_name']][$sectionData['service_description']] = $sectionData;
            }
            elseif($sectionType == "hoststatus")
            {
            $hostStatus[$sectionData["host_name"]] = $sectionData;
            }
            elseif($sectionType == "programstatus")
              {
            $programStatus = $sectionData;
              }
            $inSection = false;
            $sectionType = "";
            continue;
        }
        else
        {
            // we're currently in a section, and this line is part of it
            $lineKey = substr($line, 0, strpos($line, "="));
            $lineVal = substr($line, strpos($line, "=")+1);
            if($lineKey=='current_state' && ($lineVal == 1 || $lineVal == 2 ) )
            {
                $numProblems++;
                $retArray['problems'][$sectionData['host_name']]['state'] = $lineVal; 
                // echo "[{$numProblems}] ".$sectionData['host_name'] .": {$lineVal}<br />";
            }
            if($lineKey=='plugin_output')
            {
                $retArray['problems'][$sectionData['host_name']]['last_out'] = $lineVal; 
                // echo "[{$numProblems}] ".$sectionData['host_name'] .": {$lineVal}<br />";
            }


            // add to the array as appropriate
            if($sectionType == "servicestatus" || $sectionType == "hoststatus" || $sectionType == "programstatus")
            {
              if($debug){ echo "LINE ".$lineNum.": lineKey=".$lineKey."= lineVal=".$lineVal."=\n";}
              $sectionData[$lineKey] = $lineVal;
            }
            // else continue on, ignore this section, don't save anything
        }

        }
        
        fclose($fh);

        $retArray = array("hosts" => $hostStatus, "services" => $serviceStatus, "program" => $programStatus);

        return $retArray;    
    }


    // this formats the age of a check in seconds into a nice textual description
    function ageString($seconds)
    {
        $age = "";
        if($seconds > 86400)
        {
            $days = (int)($seconds / 86400);
            $seconds = $seconds - ($days * 86400);
            $age .= $days." days ";
        }
        if($seconds > 3600)
        {
            $hours = (int)($seconds / 3600);
            $seconds = $seconds - ($hours * 3600);
            $age .= $hours." hours ";
        }
        if($seconds > 60)
        {
            $minutes = (int)($seconds / 60);
            $seconds = $seconds - ($minutes * 60);
            $age .= $minutes." minutes ";
        }
        $age .= $seconds." seconds ";
        return $age;
    }
    function loadObjects()
        {
            // Many ways to skin a cat...

            $i=0;
            $fh = fopen($this->objectFile, "r");
            while (!feof($fh)) {
               $line = fgets($fh);
               if(preg_match("/^define (.*?) {/",$line,$defMatch))
               {
                   $currentDefine = $defMatch[1];
                   $this->nagios->defines[$currentDefine]++;
                   $i++;
               }
               switch($currentDefine)
               {
                   case 'hostgroup':
                        if(preg_match("/hostgroup_name\t(.*?)/U",$line,$groupMatch))
                        {
                            $currentGroupName = $groupMatch[1];
                            $this->nagios->hostGroups[$currentGroupName] = array();
                        }
                        if(preg_match("/alias\t(.*?)/U",$line,$match))
                        {
                            $alias = $match[1];
                            $this->nagios->hostGroups[$currentGroupName]['alias'] = $alias;
                        }
                        if(preg_match("/members\t(.*?)/U",$line,$match))
                        {
                            $members = $match[1];
                            $this->nagios->hostGroups[$currentGroupName]['members'] = explode(",",$members);
                        }
                   break;
                   case 'servicegroup':
                        if(preg_match("/servicegroup_name\t(.*?)/U",$line,$groupMatch))
                        {
                            $currentGroupName = $groupMatch[1];
                            $this->nagios->serviceGroups[$currentGroupName] = array();
                        }
                        if(preg_match("/alias\t(.*?)/U",$line,$match))
                        {
                            $alias = $match[1];
                            $this->nagios->serviceGroups[$currentGroupName]['alias'] = $alias;
                        }
                        if(preg_match("/members\t(.*?)/U",$line,$match))
                        {
                            $members = $match[1];
                            $this->nagios->serviceGroups[$currentGroupName]['members'] = explode(",",$members);
                        }
                   break;

               }
            }
        }

}


# CONFIG
$nagios = new nagiosNG();
$nagios->statusFile = $statusFile;
$debug = false;
$data = $nagios->getData3($statusFile); // returns an array
$numProblems=0;
$statusArray=array();
foreach($data['hosts'] as $hostName => $hostArray)
{
    $problem=0;
    foreach($hostArray as $field => $value)
    {
        switch($field)
        {
            case 'current_state':
                switch($value)
                {
                    case 1: // Down
                        // echo "Host: {$hostName} DOWN: ";
                        $numProblems++;
                        $problem=1;
                    break;
                    case 2: // Unreachable
                        // echo "Host: {$hostName} UNREACHABLE: ";
                        $numProblems++;
                        $problem=1;
                    break;
                    case 3: // Unknown
                        // echo "Host: {$hostName} UNKOWN: ";
                        $numProblems++;
                        $problem=1;
                    break;

                }
            break;
            case 'plugin_output':
                switch($problem)
                {
                    case 1:
                        $statusArray['down_hosts'][$hostName]['last_out']=$value;
                        // echo $value."\n";
                    break;
                }
            break;
            case 'last_state_change':
                switch($problem)
                {
                    case 1:
                        $statusArray['down_hosts'][$hostName]['down_since']=$value;
                        // echo $value."\n";
                    break;
                }
            break;

        }
    }
}

foreach($data['services'] as $hostName => $serviceArray)
{
    foreach($serviceArray as $serviceName => $serviceDetails)
    {
        $problem=0;
        foreach($serviceDetails as $field => $value)
        {
            switch($field)
            {
                case 'current_state':
                    switch($value)
                    {
                        case 0: // UP
                            // echo "Host: {$hostName} Service: {$serviceName} DOWN: ";
                            $problem=0;
                            $statusArray['up_services'][$hostName][$serviceName]['current_state']=$value;
                        break;    
                        case 1: // Down
                            // echo "Host: {$hostName} Service: {$serviceName} DOWN: ";
                            $numProblems++;
                            $problem=1;
                            $statusArray['down_services'][$hostName][$serviceName]['current_state']=$value;
                        break;
                        case 2: // Unreachable
                            // echo "Host: {$hostName} Service: {$serviceName} UNREACHABLE: ";
                            $numProblems++;
                            $problem=1;
                            $statusArray['down_services'][$hostName][$serviceName]['current_state']=$value;
                        break;
                        case 3: // Unknown
                            // echo "Host: {$hostName} Service: {$serviceName} UNKOWN: ";
                            $numProblems++;
                            $problem=1;
                            $statusArray['down_services'][$hostName][$serviceName]['current_state']=$value;
                        break;

                    }
                break;
                case 'last_state_change':
                    switch($problem)
                    {
                        case 1:
                            $statusArray['down_services'][$hostName][$serviceName]['down_since']=$value;
                            // echo $value."\n";
                        break;
                        case 0:
                            $statusArray['up_services'][$hostName][$serviceName]['up_since']=$value;
                            // echo $value."\n";
                        break;
                    }
                break;

                case 'plugin_output':
                    if(preg_match("/index ([0-9]+)/",$value,$indexArr))
                    {
                        $index=$indexArr[1];
                    }
                    else
                    {
                        $index=0;
                    }
                    switch($problem)
                    {
                        case 1:
                            $statusArray['down_services'][$hostName][$serviceName]['last_out'] = $value;
                            $statusArray['down_services'][$hostName][$serviceName]['index'] = $index;
                            $statusArray['down_index'][$index]=1;
                            // echo $value."\n";
                        break;
                        case 0:
                            $statusArray['up_services'][$hostName][$serviceName]['last_out'] = $value;
                            $statusArray['up_services'][$hostName][$serviceName]['index'] = $index;
                            // echo $value."\n";
                        break;
                    }
                break;

            }
        }
    }
}
// LOg file
$hideLine=array();
$handle = @fopen($logFile, "r");
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
        if(!preg_match("/\[(.*?)\] (.*?)$/",$buffer,$lineMatch))
        {
            // Who error line
        }
        else
        {
            $time = $lineMatch[1];
            $info = $lineMatch[2];
            if(is_array($hideLine) && !in_array($info,$hideLine) && !preg_match("/localhost/", $buffer))
            {
                /*
                echo "<pre>\n";
                print_r($lineMatch);
                echO "</pre>\n";
                */
                $logArray[$info]= $time;
            }
        }

        //echo $buffer."<br />\n";
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}
arsort($logArray);
// echo "Total Problems: {$numProblems}\n";
function nicetime($unix_date)
{
    if(empty($unix_date)) {
        return "No date provided";
    }

    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");

    $now             = time();
    // $unix_date         = strtotime($date);

       // check validity of date
    if(empty($unix_date)) {
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {
        $difference     = $now - $unix_date;
        //$tense         = "ago";

    } else {
        $difference     = $unix_date - $now;
        // $tense         = "from now";
    }

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";
}



?>
