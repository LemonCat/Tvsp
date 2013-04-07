<?php
    /*For testing purpose*/
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime; 
    /*For testing purpose*/
    
    
    include("./class/user.php");
    include("./class/cApi.php");

    $myAPI=new cApi();
    
    //$myAPI->retrieveMirrors();
    //$myAPI->testMirrors();
    
    
    //$myAPI->search('Dexter');
    
    
    //251406 I just want my pants back
    //79349 dexter
    //152831 adventure time
    //129261 spartacus
    //79488 30 rock
    //94571 Community
    //167571 Happy endings
    //248682 new girl
    //248951 Whitney
    //82467 Eastbound and Down
    //83237 Stargate universe
    //$myAPI->retrieveSerieZip('83237');
    //echo $myAPI->retrieveSerieName('83237');
    
    //var_dump($myAPI->search('Dexter'));
    //echo $myAPI->retrieveEpImg(4282113);
    
    
    //$myUser=new cUser();
    
    //validé
    //$myUser->newAcc('a','a');
    //$myUser->changeMail(1,"antoine@gmail.com");
    //$myUser->changePwd(1,'a','b');
    //$myUser->changeSubs(1,1,0,0);
    //$myUser->delAcc(6);
    //echo $myUser->login('c', 'c');
    
    //$myUser->delShowFromUser(6,79337);
    //$myUser->addShowToUser(1,152831);
    //$tab=$myUser->buildPlanning(1,'3','2012');
    
    //var_dump($t_test);
    
    //posters
    echo $_SERVER['HTTP_HOST'].'<br />';
    echo date('h', 1334230114);
    
    /*For testing purpose*/
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $endtime = $mtime;
    $totaltime = ($endtime - $starttime);
    echo '<center><font style="font-size:20px;">Page générée en ',number_format($totaltime,4,',',''),' s</font></center>'; 
    /*For testing purpose*/
            
            
    unset($myUser);//close con
    
?>