<?php

$xml = simplexml_load_file('https://xmldata.qrz.com/xml/current/?username=kd9hae;password=ArmY1234$$') or die("Error");

    print_r($xml[0]->Session->Key[0]);
    
    echo("\n\nThis is the end");
?>