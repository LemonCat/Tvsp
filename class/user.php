<?php

    class cUser
    {
	
        //db Access
        private $host="";
	private $user="";
	private $passwd="";
        
	private $base="";
	private $con;
	private $online=false;
	
	private $myApi;
	
        public function __construct() {
	    if($_SERVER['HTTP_HOST']=='127.0.0.1'){
		//local db
		$this->host= "localhost";
		$this->user="root";
		$this->passwd="root";
		
		$this->base="tvsp";
		$this->online=false;
	    }else{
		//Hosted db
		$this->host= "mysql51-60.perso";
		$this->user="tvshowpltvsp";
		$this->passwd="tn5Ij2i7";
		
		$this->base="tvshowpltvsp";
		$this->online=true;
	    }
	    
	    
	    $this->con= mysql_connect($this->host,$this->user,$this->passwd) or die("Connexion");
            mysql_select_db($this->base,$this->con) or die("Sélection de MaBase");
	    
	    $this->myApi=new cApi();
	    //$this->myApi->retrieveMirrors();
	    //$this->myApi->testMirrors();
        }
        
	function __destruct() {
	    @mysql_close($this->con);
	}
        
        public function newAcc($mail, $pwd){
            //Connexion
            mysql_query("INSERT INTO t_users(mail, pwd) VALUES ('".addslashes($mail)."', '".addslashes($pwd)."')");
        }
        
        
        public function login($mail, $pwd){
	    /*
	     Log function
	     1 if ok
	     0 if bad credentials
	    */
	    $result = mysql_query("SELECT count(*) AS col FROM `t_users` WHERE `mail`='".addslashes($mail)."' AND `pwd`='".md5($pwd)."';");
            while($row = mysql_fetch_array($result))
            {
                $i=$row['col'];
            }
	    if($i=='1'){
		$logged=1;
	    }else{
		$logged=0;
	    }
	    
	    return $logged;
        }
        
	public function getUserID($mail){
	    $userId;
	    $result = mysql_query("SELECT `idt_users` FROM `t_users` WHERE `mail`='".$mail."'");
	    
	    while($row = mysql_fetch_array($result)){
		$userId=$row['idt_users'];
	    }
	    
	    return $userId;
	}
	
        public function delAcc($id)
        {
            mysql_query("DELETE FROM t_users WHERE idt_users='".$id."'");
        }
        
        public function changePwd($id, $oldPwd, $newPwd){
	    $result = mysql_query("SELECT pwd FROM t_users WHERE idt_users='".$id."'");
            while($row = mysql_fetch_array($result))
            {
                $curPwd=$row['pwd'];
            }
            
            //If pwds match, update
            if($curPwd==$oldPwd){
                mysql_query("UPDATE t_users SET pwd='".addslashes($newPwd)."' WHERE idt_users='".$id."'");
            }
        }
        
        public function changeMail($id, $newMail){
            mysql_query("UPDATE t_users SET mail='".addslashes($newMail)."' WHERE idt_users='".$id."'");
        }
        
	
        public function addShowToUser($idUser, $idShow){
            //check if show present if db. If not, retrieve datas
	    $result = mysql_query("SELECT count(*) as col FROM `t_shows` WHERE idt_shows='".$idShow."';");
            while($row = mysql_fetch_array($result))
            {
                if($row['col']=='0'){
		    $this->myApi->retrieveSerieZip($idShow);
		}
            }
	    
	    //populate t_users_has_t_shows
	    mysql_query("INSERT INTO `t_users_has_t_shows` (`t_users_idt_users`, `t_shows_idt_shows`) VALUES ('".$idUser."', '".$idShow."');");
        }
        
        public function delShowFromUser($idUser, $idShow){
            //del from t_users_has_t_shows
	    mysql_query("DELETE FROM `t_users_has_t_shows` WHERE `t_users_has_t_shows`.`t_users_idt_users` = ".$idUser." AND `t_users_has_t_shows`.`t_shows_idt_shows` = ".$idShow);
        }

	public function calculDecalage($month, $year){
	    $decalage;
	    
	    //Définir décalage du 1er du mois.
	    $firstDayOfMonth = date('D', mktime(0, 0, 0, $month, 1, $year));
	    if($firstDayOfMonth=='Mon'){
		$decalage=0;
	    }
	    if($firstDayOfMonth=='Tue'){
		$decalage=1;
	    }
	    if($firstDayOfMonth=='Wed'){
		$decalage=2;
	    }
	    if($firstDayOfMonth=='Thu'){
		$decalage=3;
	    }
	    if($firstDayOfMonth=='Fri'){
		$decalage=4;
	    }
	    if($firstDayOfMonth=='Sat'){
		$decalage=5;
	    }
	    if($firstDayOfMonth=='Sun'){
		$decalage=6;
	    }
	    
	    return $decalage;
	}
	
	function findShowsForADay($uId, $day, $month, $year){
	    $t_day=null;
	    $t_oneinfo;
	    
	    //On peut passer du '05' ou '5' pour les comparaisons de date en sql.
	    $strSQL="SELECT `seriesid`, `EpisodeNumber`,`SeasonNumber`
		    FROM `t_episode`
		    WHERE (
		    MONTH( `FirstAired` ) = '".$month."'
		    AND DAY( `FirstAired` ) = '".$day."'
		    AND YEAR( `FirstAired` ) = '".$year."'
		    )AND (`seriesid`='";
		    //73762' OR `seriesid`='80379' OR `seriesid`='75760')

		    
	    $resShLst=mysql_query("SELECT `t_shows_idt_shows` FROM `t_users_has_t_shows` WHERE `t_users_idt_users`='".$uId."'");
	    while($rowShLst = mysql_fetch_array($resShLst)){
		$strSQL=$strSQL.$rowShLst['t_shows_idt_shows']."' OR `seriesid`='";
	    }
	    $strSQL=$strSQL."a')";//finir la chaine avec un cas qui est sûr de ne jamais arriver.
	    
	    
	    //process built query
	    $res=mysql_query($strSQL);
	    while($row = mysql_fetch_array($res)){
		$t_oneinfo['SerieName']=$this->retrieveSerieName($row['seriesid']);
		
		if($row['EpisodeNumber']<10){
		    $t_oneinfo['EpisodeNumber']='0'.$row['EpisodeNumber'];
		}else{
		    $t_oneinfo['EpisodeNumber']=$row['EpisodeNumber'];
		}
		
		if($row['SeasonNumber']<10){
		    $t_oneinfo['SeasonNumber']='0'.$row['SeasonNumber'];
		}else{
		    $t_oneinfo['SeasonNumber']=$row['SeasonNumber'];
		}
		
		$t_day[]=$t_oneinfo;
	    }
	    
	    if(!is_null($t_day)){
		return $t_day;
	    }else{
		return null;
	    }
	    
	}
    
    	public function retrieveSerieName($id){
	    
	    $result = mysql_query("SELECT `SeriesName` FROM `t_shows` WHERE `idt_shows`='".$id."'");
		
	    while($row = mysql_fetch_array($result)){
		return $row['SeriesName'];
	    }
	    
	}
    
    
	public function buildPlanning($idUser, $month, $year){
	    $t_planning42=null;
	    
	    $serieName;
	    $firstDayOfMonth;
	    $decalage=$this->calculDecalage($month,$year);
	    $serieName;
	    $serieId;
	    
	    $t_oneEpisode;
	    
	    //List of series watched by user
	    $result = mysql_query("SELECT `t_shows_idt_shows` AS col FROM `t_users_has_t_shows` WHERE `t_users_idt_users` = '".$idUser."'");
            while($row = mysql_fetch_array($result))
            {
		//retrieve episode from those series, for the selected month.                 
		$resultSe = mysql_query("SELECT `SeriesName` FROM `t_shows` WHERE `idt_shows`='".$row['col']."'");
		$serieId=$row['col'];
		
		while($rowSe = mysql_fetch_array($resultSe)){
		    $serieName=$rowSe['SeriesName'];
		}
		
		$str="SELECT `EpisodeNumber`, `EpisodeName`, `Overview`, `SeasonNumber`, `FirstAired`, idt_episode FROM `t_episode` WHERE MONTH( `FirstAired` ) = '".$month."' AND year( `FirstAired` ) = '".$year."' and `seriesid` ='".$row['col']."'";
		$resultEp = mysql_query($str);
		
		//echo $str.'<br />';
		while($rowEp = mysql_fetch_array($resultEp)){
		    /*
		    //DEBUG
		    echo $rowEp['EpisodeNumber'].'<br />';
		    echo $rowEp['EpisodeName'].'<br />';
		    echo $rowEp['Overview'].'<br />';
		    echo $rowEp['SeasonNumber'].'<br />';
		    echo $rowEp['FirstAired'].'<br />';
		    
		    echo $decalage+date('j', strtotime($rowEp['FirstAired']));
		    
		    echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';echo '<br />';
		    */
		    $t_oneEpisode['idt_episode']=$rowEp['idt_episode'];
		    $t_oneEpisode['EpisodeNumber']=$rowEp['EpisodeNumber'];
		    $t_oneEpisode['EpisodeName']=$rowEp['EpisodeName'];
		    $t_oneEpisode['Overview']=$rowEp['Overview'];
		    $t_oneEpisode['SeasonNumber']=$rowEp['SeasonNumber'];
		    $t_oneEpisode['FirstAired']=$rowEp['FirstAired'];
		    $t_oneEpisode['SerieName']=$serieName;
		    $t_oneEpisode['serieId']=$serieId;
		    
		    
		    $t_planning42[$decalage+date('j', strtotime($rowEp['FirstAired']))][]=$t_oneEpisode;
		    
		    //var_dump($t_planning42);
		}
            }
	    
	    
	    if(!is_null($t_planning42)){
		return $t_planning42;
	    }else{
		return null;
	    }
	    
	    
	    
	    
	}

	public function previousMonth($month){
	    $prMonth=$month-1;
	    
	    if($prMonth<10){
		$prMonth='0'.$prMonth;
	    }
	    
	    return $prMonth;
	}
	
	public function nextMonth($month){
	    $nxMonth=$month+1;
	    
	    if($nxMonth<10){
		$nxMonth='0'.$nxMonth;
	    }
	    
	    return $nxMonth;
	}
	
	public function previousYear($year){
	    $prYear=$year-1;
	    
	    return $prYear;
	}
	
	public function getMonthName($month){
	    $strFrMonth;
	    
	    switch ($month) {
	    case '01':
		$strFrMonth="Janvier";
	        break;
	    case '02':
	        $strFrMonth="F&eacute;vrier";
	        break;
	    case '03':
	        $strFrMonth="Mars";
	        break;
	    case '04':
	        $strFrMonth="Avril";
	        break;
	    case '05':
	        $strFrMonth="Mai";
	        break;
	    case '06':
	        $strFrMonth="Juin";
	        break;
	    case '07':
	        $strFrMonth="Juillet";
	        break;
	    case '08':
	        $strFrMonth="Ao&ucirc;t";
	        break;
	    case '09':
	        $strFrMonth="Septembre";
	        break;
	    case '10':
	        $strFrMonth="Octobre";
	        break;
	    case '11':
	        $strFrMonth="Novembre";
	        break;
	    case '12':
	        $strFrMonth="D&eacute;cembre";
	        break;
	    }
	    
	    return $strFrMonth;
	}


	public function userSubD($id){
	    $res=mysql_query("SELECT subD FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['subD']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function userSubW($id){
	    $res=mysql_query("SELECT subW FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['subW']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function userSubM($id){
	    $res=mysql_query("SELECT subM FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['subM']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function userSubtitles($id){
	    $res=mysql_query("SELECT subtitles FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['subtitles']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function userTPB($id){
	    $res=mysql_query("SELECT tpb FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['tpb']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function userSubFr($id){
	    $res=mysql_query("SELECT subLang FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['subLang']=='fr'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function isRightPwd($id, $pwd){
	    $res=mysql_query("SELECT count(*) as col FROM `t_users` WHERE `idt_users`='".$id."' AND pwd='".md5($pwd)."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['col']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	public function isAdmin($id){
	    $res=mysql_query("SELECT isAdmin as col FROM `t_users` WHERE `idt_users`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['col']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
        public function changeSubs($id, $d, $w, $m){
            /*
             change mail alert subscriptions.
             
             param :
             id user
             3 x booleans
            */
	    
            mysql_query("UPDATE t_users SET subD='".$d."', subW='".$w."', subM='".$m."' WHERE idt_users='".$id."'");
        }
        
    }

?>