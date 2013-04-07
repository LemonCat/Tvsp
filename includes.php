<?php
    
    include("./class/user.php");
    include("./class/cApi.php");
    
    $myAPI=new cApi();
    //$myAPI->retrieveMirrors();
    //$myAPI->testMirrors();
    
    $myUser=new cUser();
    
    session_start();
    
    $mtime = microtime();
	    $mtime = explode(" ",$mtime);
	    $mtime = $mtime[1] + $mtime[0];
	    $starttime = $mtime; 
?>