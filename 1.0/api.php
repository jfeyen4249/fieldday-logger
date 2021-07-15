<?php

include('config.php');
// array holding allowed Origin domains
$allowedOrigins = array(
  '(http(s)://)?(www\.)?my\-domain\.com'
);
 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
  foreach ($allowedOrigins as $allowedOrigin) {
    if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      header('Access-Control-Max-Age: 1000');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      break;
    }
  }
}
$dup = 0;
if(isset($_GET["api"])) {
        if($_GET["api"] == 'logs'){
        
        if ($db->connect_errno) {
            printf("Failed to connect to database");
            exit();
        }
        $result = $db->query("SELECT * FROM logs ORDER BY id DESC");
        $dbdata = array();
        while ( $row = $result->fetch_assoc())  {
            $dbdata[]=$row;
        }
        echo json_encode($dbdata);

    }  elseif($_GET["api"] == 'lastcontacts') {
        if ($db->connect_errno) {
            printf("Failed to connect to database");
            exit();
        }
        $result = $db->query("SELECT * FROM logs ORDER BY id DESC LIMIT 10");
        $dbdata = array();
        while ( $row = $result->fetch_assoc())  {
            $dbdata[]=$row;
        }
        echo json_encode($dbdata);
        

    
    
    }  elseif($_GET["api"] == 'datacheck') {

        $callsign = mysqli_real_escape_string($db, $_GET['callsign']);
        $band = mysqli_real_escape_string($db, $_GET['band']);
        $mode = mysqli_real_escape_string($db, $_GET['mode']);

        if ($db->connect_errno) {
            printf("Failed to connect to database");
            exit();
        }
        $result = $db->query("SELECT * FROM logs WHERE callsign ='$callsign' AND band ='$band' AND mode = '$mode'");
        $dbdata = array();
        while ( $row = $result->fetch_assoc())  {
            $dbdata []=$row;
        }
        echo json_encode($dbdata);


    }elseif($_GET["api"] == 'counts') {

            //$park = mysqli_real_escape_string($db, $_GET['park']);
    
            if ($db->connect_errno) {
                printf("Failed to connect to database");
                exit();
            }
            $result = $db->query("SELECT ssb,cw,digital,count,M6,M10,M12,M15,M17,M20,M30,M40,M80,M160 FROM counts WHERE id ='1'");
            $dbdata = array();
            while ( $row = $result->fetch_assoc())  {
                $dbdata =$row;
            }
            echo json_encode($dbdata);
    
      
            
    
    }  elseif($_GET["api"] == 'logcontact') {

        $callsign = mysqli_real_escape_string($db, $_GET['callsign']);
        $class = mysqli_real_escape_string($db, $_GET['class']);
        $section = mysqli_real_escape_string($db, $_GET['section']);
        $band = mysqli_real_escape_string($db, $_GET['band']);
        $mode = mysqli_real_escape_string($db, $_GET['mode']);
        $operator = mysqli_real_escape_string($db, $_GET['operator']);
        $lat = mysqli_real_escape_string($db, $_GET['lat']);
        $lng = mysqli_real_escape_string($db, $_GET['lng']);
        $name = mysqli_real_escape_string($db, $_GET['name']);

        if (mysqli_connect_errno()) 
        { 
            echo "Database connection failed."; 
        } 
        
        // query to fetch Username and Password from 
        // the table geek 
        $query = "INSERT INTO `logs` (`id`, `callsign`, `class`, `section`, `operator`, `mode`, `band`, `lat`, `lng`, `name`) VALUES (NULL, '$callsign', '$class', '$section', '$operator', '$mode', '$band', '$lat', '$lng', '$name')";

        //$query = "INSERT INTO logs (callsign,class,section,operator,mode,band,lat,lng,name) VALUES ('$callsign', '$class', '$section', '$operator', '$mode', '$band', '$lat', '$lng', '$name')"; 
        
        // Execute the query and store the result set 
        $result = mysqli_query($db, $query); 
        if($result) 
        { 
             
            $row = mysqli_num_rows($result); 
            printf($row); 
            mysqli_free_result($result); 
        }
    
        // Connection close  
        mysqli_close($db); 

    }  elseif($_GET["api"] == 'check') {

        $callsign = mysqli_real_escape_string($db, $_GET['callsign']);
        $band = mysqli_real_escape_string($db, $_GET['band']);
        $mode = mysqli_real_escape_string($db, $_GET['mode']);
        if (mysqli_connect_errno()) 
        { 
            echo "Database connection failed."; 
        } 
        $query = "SELECT * FROM logs WHERE callsign ='$callsign' AND band ='$band' AND mode = '$mode'"; 

        $result = mysqli_query($db, $query); 
        if($result) 
        {    
            $row = mysqli_num_rows($result); 
            //printf($row); 
            $xml = simplexml_load_file('https://xmldata.qrz.com/xml/current/?username=' . $qrzusername .';password='. $qrzpassword') or die("Error");
       
                $ApiKey = $xml->Session[0]->Key;
                //echo $ApiKey;
                $url = "https://xmldata.qrz.com/xml/current/?s=" . $ApiKey .";callsign=". $callsign;
                
                $xml1 = simplexml_load_file($url) or die ("Error");
                $name = $xml1->Callsign[0]->name_fmt;
                $name = str_replace('"', "|", $name);
                $lat = $xml1->Callsign[0]->lat;
                $lon = $xml1->Callsign[0]->lon;
                $state = $xml1->Callsign[0]->state;
                $country = $xml1->Callsign[0]->country;

                if($row == 0) {
                $myoutput = '{"status":"New Contact","name":"' . $name . '","lat":"' . $lat . '","lon":"'. $lon .'","country":"' . $country . '","state":"' . $state . '"}';
                echo $myoutput;
                } else {
                $myoutput = '{"status":"Duplicate Contact","name":"' . $name . '","lat":"' . $lat . '","lon":"'. $lon .'","country":"' . $country . '","state":"' . $state . '"}';
                echo $myoutput;
                }
            mysqli_free_result($result); 
        }
        mysqli_close($db); 
    } elseif($_GET["api"] == 'checklogfile') {

        $callsign = mysqli_real_escape_string($db, $_GET['callsign']);
        $band = mysqli_real_escape_string($db, $_GET['band']);
        $mode = mysqli_real_escape_string($db, $_GET['mode']);
        $callclass = mysqli_real_escape_string($db, $_GET['class']);
        $operator = mysqli_real_escape_string($db, $_GET['operator']);
        $section = mysqli_real_escape_string($db, $_GET['section']);

        if (mysqli_connect_errno()) 
        { 
            echo "Database connection failed."; 
        } 
        $query = "SELECT * FROM logs WHERE callsign ='$callsign' AND band ='$band' AND mode = '$mode'"; 

        $result = mysqli_query($db, $query); 
        if($result) 
        {    
            $row = mysqli_num_rows($result); 
            //printf($row); 
            $xml = simplexml_load_file('https://xmldata.qrz.com/xml/current/?username=kd9hae;password=ArmY1234$$') or die("Error");
       
                $ApiKey = $xml->Session[0]->Key;
                //echo $ApiKey;
                $url = "https://xmldata.qrz.com/xml/current/?s=" . $ApiKey .";callsign=". $callsign;
                
                $xml1 = simplexml_load_file($url) or die ("Error");
                $name = $xml1->Callsign[0]->name_fmt;
                $name = str_replace('"', "|", $name);
                $lat = $xml1->Callsign[0]->lat;
                $lon = $xml1->Callsign[0]->lon;
                $state = $xml1->Callsign[0]->state;
                $country = $xml1->Callsign[0]->country;

                if($row == 0) {
                $myoutput = '{"status":"New Contact","name":"' . $name . '","lat":"' . $lat . '","lon":"'. $lon .'","callsign":"' . $callsign . '","section":"' . $section . '","class":"' . $callclass . '","mode":"' . $mode . '","operator":"' . $operator . '","band":"' . $band .'"}';
                echo $myoutput;
                } else {
                $myoutput = '{"status":"Duplicate Contact","name":"' . $name . '","lat":"' . $lat . '","lon":"'. $lon .'","callsign":"' . $callsign . '","section":"' . $section . '","class":"' . $callclass . '","mode":"' . $mode . '","operator":"' . $operator . '","band":"' . $band .'"}';
                echo $myoutput;
                }
            mysqli_free_result($result); 
        }
        mysqli_close($db); 
    }
}
?>
