<?php

    class cApi
    {
        
        public $t_mirrors = array();
        private $apiKey="7723773B81E35AE7";
        public $t_sResults=array();
        private $serverTime;
        
        
        //db Access
        private $host="";
	private $user="";
	private $passwd="";
        
	private $base="";
	private $con;
	private $online=false;
	
	
	/*private $host = "mysql51-60.perso";
	private $user="tvshowpltvsp";
	private $passwd="tn5Ij2i7";
        
	private $base="tvshowpltvsp";
	private $con;*/
	
        
        
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
        }
        
        
        function __destruct() {
	    //echo 'destructor : FIRE ';
            //@, Error when no linked to db open. 
	    @mysql_close($this->con);
	    
	}
        
        
        public function retrieveMirrors(){
            /*
             Retrieve the server list.
            */
            
            $url="http://www.thetvdb.com/api/".$this->apiKey."/mirrors.xml";
            
            $xmlStr=$this->curl_get_file_contents($url);
            
            $mir = new SimpleXMLElement($xmlStr);
            
            /* For each mirror node */
            foreach ($mir->Mirror as $m) {
                
                /*
                 To modify if tvdb make a real use of the mirror typemask.
                 
                 The value of typemask is the sum of whichever file types that mirror holds:
                 1 xml files
                 2 banner files
                 4 zip files
                */
                
               $this->t_mirrors[]=$m->mirrorpath;
            }
	    
	    $myAPI->testMirrors();
	    
        }
        
        public function search($terms){
            /*
             Example can be seen here :
             http://www.thetvdb.com/api/GetSeries.php?seriesname=dexter
            */
	    
            if(!$this->t_mirrors){
		@$this->retrieveMirrors;
	    }
	    if(!$this->t_mirrors){
		return false;
	    }
	    
	    $terms=str_replace(' ', '%20', $terms);
	    $terms=str_replace("'", '%27', $terms);
	    
	    
            $url = $this->t_mirrors[0].'/api/GetSeries.php?seriesname='.$terms;

            $xmlStr=$this->curl_get_file_contents($url);
            
            $results = new SimpleXMLElement($xmlStr);
            
            $i=0;
            
            foreach ($results->Series as $s) {
                
               $this->t_sResults[$i]=array('seriesid' => $s->seriesid,
                                          'SeriesName' => $s->SeriesName,
                                          'banner' => $s->banner,
                                          'FirstAired' => $s->FirstAired,
                                          'Overview' => $s->Overview);
               
               $i++;
            }
            
            return $this->t_sResults;
        
        }
        
        public function retrieveSerieZip($sId){
            //thetvdb.com/api/7723773B81E35AE7/series/79349/all/en.zip
	    
            if(!$this->t_mirrors){
		$this->retrieveMirrors;
	    }
	    
            set_time_limit(0);//Often Exceed 30sec, so make it unlimited. 
            
            $url = $this->t_mirrors[0]."/api/".$this->apiKey."/series/".$sId."/all/en.zip";
            $tempDir='temp/';
            $serieId;
            
            //create temp dir
            if(!is_dir($tempDir)){
                mkdir('./temp/', 0700);    
            }
            
            //retrieve remote .zip
            file_put_contents($tempDir.'/'.$sId.'.zip', file_get_contents($url));
            
            //unzip
            $zip = new ZipArchive;
            $res = $zip->open($tempDir.'/'.$sId.'.zip');
            
            if ($res === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                echo 'zip extract failed';
            }
            
            /*
             zip is supposed to contain those 3 files :
                actors.xml
                banners.xml
                en.xml (english datas)
            */
            
            
            //insert data in db
            if(is_file($tempDir.'en.xml')){
                $shw = simplexml_load_file($tempDir.'en.xml');
                
                foreach ($shw->Series as $s) {
                   mysql_query("INSERT INTO `t_shows` (`idt_shows`, `SeriesName`, `Airs_Time`, `ContentRating`, `FirstAired`, `IMDB_ID`,
                               `Language`, `Network`, `NetworkID`, `Overview`, `SeriesID`, `Runtime`, `banner`, `fanart`,
                               `lastupdated`, `poster`, `Airs_DayOfWeek`, `Rating`, `RatingCount`, `Status`, `added`,
                               `timestampSrvLastUpdate`)
                               VALUES ('".$s->id."', '".addslashes($s->SeriesName)."', '".$s->Airs_Time."', '".$s->ContentRating."', '".$s->FirstAired."', '".$s->IMDB_ID."',
                               '".$s->Language."', '".addslashes($s->Network)."', '".$s->NetworkID."', '".addslashes($s->Overview)."', '".$s->SeriesID."', '".$s->Runtime."', '".$s->banner."', '".$s->fanart."',
                               '".$s->lastupdated."', '".$s->poster."', '".$s->Airs_DayOfWeek."', '".$s->Rating."', '".$s->RatingCount."', '".$s->Status."', '".$s->added."',
                               '".$this->retrieveServerTime()."');");
                   
                   
                   $serieId=$s->id;
                   
                   $genresList=explode('|',$s->Genre);
                   
                   foreach($genresList as $valeur)
                    {
                        if(!$valeur==""){
                            //check if exist in db. if not, insert.
                            
                            $result = mysql_query("SELECT count(*) AS col FROM `t_genres` WHERE `genreName`='".$valeur."';");
                            while($row = mysql_fetch_array($result))
                            {
                                $i=$row['col'];
                            }
                            
                            if($i=='0'){
                                //insert
                                mysql_query("INSERT INTO t_genres(idt_genres, genreName) VALUES ('', '".addslashes($valeur)."')");
                            }
                            
                            unset($result); //for further clean use
                        }
                        
                        
                        //link shows to genres
                        ////retrieve genre id
                        $result2 = mysql_query("SELECT idt_genres FROM `t_genres` WHERE `genreName`='".$valeur."';");
                        while($row2 = mysql_fetch_array($result2))
                        {
                            $i2=$row2['idt_genres'];
                            ////insert in table t_show_has_t_genres
                            mysql_query("INSERT INTO `t_shows_has_t_genres` (`t_shows_idt_shows` ,`t_genres_idt_genres`)VALUES ('".$s->id."', '".$i2."') ");
                        }
                    }
                   
                   
                }
                
                //add episodes
                foreach ($shw->Episode as $e){
                    mysql_query("INSERT INTO `t_episode`
                                (`idt_episode`, `Combined_episodenumber`, `Combined_season`, `Director`, `EpImgFlag`,
                                `EpisodeName`, `EpisodeNumber`, `FirstAired`, `GuestStars`, `IMDB_ID`, `Language`,
                                `Overview`, `ProductionCode`, `Rating`, `RatingCount`, `SeasonNumber`, `Writer`,
                                `absolute_number`, `filename`, `lastupdated`, `seasonid`, `seriesid`, `timestampSrvLastUpdate`)
                                VALUES ('".$e->id."', '".$e->Combined_episodenumber."', '".$e->Combined_season."', '".addslashes($e->Director)."', '".$e->EpImgFlag."',
                                '".addslashes($e->EpisodeName)."', '".$e->EpisodeNumber."', '".$e->FirstAired."', '".addslashes($e->GuestStars)."', '".$e->IMDB_ID."', '".$e->Language."',
                                '".addslashes($e->Overview)."', '".$e->ProductionCode."', '".$e->Rating."', '".$e->RatingCount."', '".$e->SeasonNumber."', '".addslashes($e->Writer)."',
                                '".$e->absolute_number."', '".addslashes($e->filename)."', '".$e->lastupdated."', '".$e->seasonid."', '".$e->seriesid."', '".$this->retrieveServerTime()."');");
                }
            }
            
            
            if(is_file($tempDir.'actors.xml')){
                
                $act = simplexml_load_file($tempDir.'actors.xml');
                
                foreach ($act->Actor as $a) {
                   
                   mysql_query("INSERT INTO `t_actors` (`idt_actors`, `image`, `name`, `role`, `sortOrder`, `t_shows_idt_shows`)
                               VALUES ('".$a->id."', '".$a->Image."', '".addslashes($a->Name)."', '".addslashes($a->Role)."', '".$a->SortOrder."', '".$serieId."');");
                   
                   echo mysql_error();
                }
            }
            

            /*
             Can be used in the future if we want to retrieve more banners. 
            if(is_file($tempDir.'banners.xml')){
                echo 'banners.xml ok<br />';
            }
            */
            
            set_time_limit(30);//Restore time limit
            
        }
        
        
        
        public function retrieveServerTime(){
            //retrieve server timestamp for further updates. 
            //http://www.thetvdb.com/api/Updates.php?type=none
	    
	    if(!$this->t_mirrors){
		$this->retrieveMirrors;
	    }
	    
            $url = $this->t_mirrors[0].'/api/Updates.php?type=none';
            
            //$xmlStr=$this->curl_get_file_contents($url);
	    $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $url);
            $xmlStr = curl_exec($c);
            curl_close($c);
            
            $xmlServerTime = new SimpleXMLElement($xmlStr);
            return $xmlServerTime->Time;
        }
        
        
        private function testMirrors(){
            /*
             Keep first valid mirror. 
            */
            
            while(list($idx,$val)= each($this->t_mirrors)){
		if(!$this->ping(str_replace("http://", "", $this->t_mirrors[$idx]), 80, 10)){
                   unset($this->t_mirrors[$idx]);
                }else{
                    $mirInUse=array();
                    $mirInUse[]=$this->t_mirrors[$idx];
                    $this->t_mirrors=$mirInUse;
                    //var_dump($this->t_mirrors);
                    return;
                }
            }
        }
        
	public function retrieveSerieName($id){
	    
	    $result = mysql_query("SELECT `SeriesName` FROM `t_shows` WHERE `idt_shows`='".$id."'");
		
	    while($row = mysql_fetch_array($result)){
		return $row['SeriesName'];
	    }
	}
	
	public function retrieveEpName($id){
	    
	    $result = mysql_query("SELECT `EpisodeName` FROM `t_episode` WHERE `idt_episode` = '".$id."'");
		
	    while($row = mysql_fetch_array($result)){
		return $row['EpisodeName'];
	    }
	}
	
	public function dayOfWeekFr($d){
	    $s;
	    switch ($d){
	    case 'Monday':
		$s="Lundi";
	        break;
	    case 'Tuesday':
		$s="Mardi";
	        break;
	    case 'Wednesday':
		$s="Mercredi";
	        break;
	    case 'Thursday':
		$s="Jeudi";
	        break;
	    case 'Friday':
		$s="Vendredi";
	        break;
	    case 'Saturday':
		$s="Samedi";
	        break;
	    case 'Sunday':
		$s="Dimanche";
	        break;
	    }
	    
	    return $s;
	}
        
	
	public function StatusFr($st){
	    $s;
	    switch ($st){
	    case 'Ended':
		$s="Termin&eacute;e";
	        break;
	    case 'Continuing':
		$s='En cours';
		break;
	    }
	    
	    return $s;
	}
	
	
	public function getNbOfSeasons($id){
	    $result=mysql_query("SELECT `SeasonNumber` FROM `t_episode` WHERE `seriesid` = '".$id."' ORDER BY `seasonid` DESC LIMIT 1");
	    
	    while($row = mysql_fetch_array($result)){
		return $row['SeasonNumber'];
	    }
	    
	}
	
	public function getGenre($id){
	    return mysql_query("SELECT g.genreName FROM t_genres g, t_shows_has_t_genres tg WHERE t_shows_idt_shows = '".$id."' AND tg.t_genres_idt_genres = g.`idt_genres`");
	}
	
	public function retrieveSerieInfos($id){
	    return $result=mysql_query("SELECT * FROM `t_shows` WHERE `idt_shows`=".$id);
	}
	
	public function retrieveBanner($id){
	    $str;
	    $imgDir='serieBanner';
	    
	    //if not local, retrieve it. 
	    if(!file_exists($imgDir.'/'.$id.'.jpg')){
		$result = mysql_query("SELECT `banner` FROM `t_shows` WHERE `idt_shows`='".$id."'");
	    
		while($row = mysql_fetch_array($result)){
		    if(!$this->t_mirrors){
			@$this->retrieveMirrors;
		    }
		    if(!$this->t_mirrors){
			return $imgDir.'/default.png';
		    }
		    var_dump($this->t_mirrors);
		    $path=$this->t_mirrors[0].'/banners/'.$row['banner'];
		}
	    
		file_put_contents($imgDir.'/'.$id.'.jpg', file_get_contents($path));
	    }
	    
	    return $imgDir.'/'.$id.'.jpg';//local path
	    
	}	
        
	
	public function retrievePoster($id){
	    $str;
	    $imgDir='posters/';
	    
	    //if not local, retrieve it. 
	    if(!file_exists($imgDir.'/'.$id.'.jpg')){
		$result = mysql_query("SELECT `poster` FROM `t_shows` WHERE `idt_shows`='".$id."'");
	    
		while($row = mysql_fetch_array($result)){
		    if(!$this->t_mirrors){
			@$this->retrieveMirrors;
		    }
		    if(!$this->t_mirrors){
			//If tvdb down. 
			return false;
		    }
		    $path=$this->t_mirrors[0].'/banners/'.$row['poster'];
		}
		
		file_put_contents($imgDir.'/'.$id.'.jpg', file_get_contents($path));
	    }
	    
	    return $imgDir.'/'.$id.'.jpg';//local path
	    
	}	
	
	
	public function retrieveEpImg($id){
	    $imgDir='epImgs/';
	    
	    
		$resEpHasImg=mysql_query("SELECT `filename` FROM `t_episode` WHERE `idt_episode`='".$id."';");
		while($rowEpHasImg = mysql_fetch_array($resEpHasImg)){
		    if(!empty($rowEpHasImg['filename'])){
			if(!file_exists($imgDir.$id.'.jpg')){
			    if(!$this->t_mirrors){
				@$this->retrieveMirrors;
			    }
			    if(!$this->t_mirrors){
				return $imgDir.'/default.png';
			    }
			    $path=$this->t_mirrors[0].'/banners/'.$rowEpHasImg['filename'];
			    file_put_contents($imgDir.'/'.$id.'.jpg', file_get_contents($path));
			    return $imgDir.$id.'.jpg';
			}else{
			    return $imgDir.$id.'.jpg';
			}
		    }else{
			return 'epImgs/default.png';
		    }
		}
	    
	}
	
	/*
	 UPDATE
	*/
	public function updateAllBase(){
	    set_time_limit(0);
	    
	    if(!$this->t_mirrors){
		$this->retrieveMirrors;
	    }
	    
	    
	    $TIMECURUPDATE=$this->retrieveServerTime();
	    
	    $t_serieIDs;
	    $t_episodeIds;
	    
	    $lastTimestamp; //last update timestamp. 
	    $resTs=mysql_query("SELECT numIntValue FROM `t_config` WHERE `name` = 'lastUpdate'");
	    while($rowTs = mysql_fetch_array($resTs)){
		$lastTimestamp=$rowTs['numIntValue'];
	    }
	    
	    
	    /*
	     Populate id arrays. Limit db access -> execution time. 
	    */
	    $resEp=mysql_query("SELECT `idt_episode` FROM `t_episode` WHERE 1");
	    while($rowEp = mysql_fetch_array($resEp)){
		$t_episodeIds[]=$rowEp['idt_episode'];
	    }
	    $resSe=mysql_query("SELECT `idt_shows` FROM `t_shows` WHERE 1");
	    while($rowSe = mysql_fetch_array($resSe)){
		$t_serieIDs[]=$rowSe['idt_shows'];
	    }
	    
	    
	    $urlUpdateXml="http://www.thetvdb.com/api/Updates.php?type=all&time=".$lastTimestamp;
	    $xmlUpdate=$this->curl_get_file_contents($urlUpdateXml);
	    
	    $resultsUpdate = new SimpleXMLElement($xmlUpdate);
		    
		    
	    foreach ($resultsUpdate->Series as $s) {
		
		foreach($t_serieIDs as $curSeId){
		    if($s==$curSeId){ //update the serie base record infos. 
			
			$urlSerieXml=$this->t_mirrors[0]."/api/".$this->apiKey."/series/".$s."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/series/75760/en.xml
			
			$xmlSerie=$this->curl_get_file_contents($urlSerieXml);
			$resultsSerie = new SimpleXMLElement($xmlSerie);
			foreach ($resultsSerie->Series as $oneSerie){
			    
			    /*
			     xml fields :
			     $oneSerie->id
			     $oneSerie->Actors
			     $oneSerie->Airs_DayOfWeek
			     $oneSerie->Airs_Time
			     $oneSerie->ContentRating
			     $oneSerie->FirstAired
			     $oneSerie->Genre
			     $oneSerie->IMDB_ID
			     $oneSerie->Language
			     $oneSerie->Network
			     $oneSerie->NetworkID
			     $oneSerie->Rating
			     $oneSerie->RatingCount
			     $oneSerie->Runtime
			     $oneSerie->SeriesID
			     $oneSerie->SeriesName
			     $oneSerie->Status
			     $oneSerie->added
			     $oneSerie->addedBy
			     $oneSerie->banner
			     $oneSerie->fanart
			     $oneSerie->lastupdated
			     $oneSerie->poster
			     $oneSerie->zap2it_id
			    */			    
			    
			    $sqlUpdateSerie="UPDATE `t_shows` SET ";
			     
			    if(!empty($oneSerie->Airs_Time)){
				$sqlUpdateSerie.="`Airs_Time` = '".addslashes($oneSerie->Airs_Time)."', ";
			    }
			    if(!empty($oneSerie->ContentRating)){
				$sqlUpdateSerie.="`ContentRating` = '".addslashes($oneSerie->ContentRating)."', ";
			    }
			    if(!empty($oneSerie->FirstAired)){
				$sqlUpdateSerie.="`FirstAired` = '".addslashes($oneSerie->FirstAired)."', ";
			    }
			    if(!empty($oneSerie->IMDB_ID)){
				$sqlUpdateSerie.="`IMDB_ID` = '".addslashes($oneSerie->IMDB_ID)."', ";
			    }
			    if(!empty($oneSerie->Language)){
				$sqlUpdateSerie.="`Language` = '".addslashes($oneSerie->Language)."', ";
			    }
			    if(!empty($oneSerie->Network)){
				$sqlUpdateSerie.="`Network` = '".addslashes($oneSerie->Network)."', ";
			    }
			    if(!empty($oneSerie->NetworkID)){
				$sqlUpdateSerie.="`NetworkID` = '".addslashes($oneSerie->NetworkID)."', ";
			    }
			    if(!empty($oneSerie->Overview)){
				$sqlUpdateSerie.="`Overview` = '".addslashes($oneSerie->Overview)."', ";
			    }
			    if(!empty($oneSerie->SeriesID)){
				$sqlUpdateSerie.="`SeriesID` = '".addslashes($oneSerie->SeriesID)."', ";
			    }
			    if(!empty($oneSerie->Runtime)){
				$sqlUpdateSerie.="`Runtime` = '".addslashes($oneSerie->Runtime)."', ";
			    }
			    if(!empty($oneSerie->banner)){
				$sqlUpdateSerie.="`banner` = '".addslashes($oneSerie->banner)."', ";
			    }
			    if(!empty($oneSerie->fanart)){
				$sqlUpdateSerie.="`fanart` = '".addslashes($oneSerie->fanart)."', ";
			    }
			    if(!empty($oneSerie->poster)){
				$sqlUpdateSerie.="`poster` = '".addslashes($oneSerie->poster)."', ";
			    }
			    if(!empty($oneSerie->Airs_DayOfWeek)){
				$sqlUpdateSerie.="`Airs_DayOfWeek` = '".addslashes($oneSerie->Airs_DayOfWeek)."', ";
			    }
			    if(!empty($oneSerie->Rating)){
				$sqlUpdateSerie.="`Rating` = '".addslashes($oneSerie->Rating)."', ";
			    }
			    if(!empty($oneSerie->RatingCount)){
				$sqlUpdateSerie.="`RatingCount` = '".addslashes($oneSerie->RatingCount)."', ";
			    }
			    if(!empty($oneSerie->RatingCount)){
				$sqlUpdateSerie.="`RatingCount` = '".addslashes($oneSerie->RatingCount)."', ";
			    }
			    if(!empty($oneSerie->Status)){
				$sqlUpdateSerie.="`Status` = '".addslashes($oneSerie->Status)."', ";
			    }
			    if(!empty($oneSerie->added)){
				$sqlUpdateSerie.="`added` = '".addslashes($oneSerie->added)."', ";
			    }
			    
			    $sqlUpdateSerie.="`timestampSrvLastUpdate` = '".$this->retrieveServerTime();
			    
			    $sqlUpdateSerie.="' WHERE `idt_shows` = '".$oneSerie->id."'";
			    
			    mysql_query($sqlUpdateSerie);
			}
			
		    }
		}
		
	    }
	    
	    //$i=0;
	    
	    foreach($resultsUpdate->Episode as $e) {
		/*TRAITEMENT DES EPISODES*/
		$flag=false;
		
		echo $e.'<br />';
		/*if($i<100){
		$i++;
		*/
		
		foreach($t_episodeIds as $curEpId){ //recherche dans le tableau des id episodes
		    
		    
		    if($e==$curEpId){ //si on a cet épisode en base, on update. 
			$flag=true; //ce flag nous permet de savoir que c'est un qu'on a updaté. 
			
			$urlEpXml=$this->t_mirrors[0]."/api/".$this->apiKey."/episodes/".$e."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/episodes/4244057/en.xml
			
			$xmlEp=$this->curl_get_file_contents($urlEpXml);
			$resultsEp = new SimpleXMLElement($xmlEp);
			
			foreach ($resultsEp->Episode as $oneEp){
			    $sqlUpdateEp="UPDATE `t_episode` SET ";
			    if(!empty($oneEp->seasonid)){
				$sqlUpdateEp.="`seasonid` = '".addslashes($oneEp->seasonid)."', ";
			    }
			    if(!empty($oneEp->EpisodeNumber)){
				$sqlUpdateEp.="`EpisodeNumber` = '".addslashes($oneEp->EpisodeNumber)."', ";
			    }
			    if(!empty($oneEp->EpisodeName)){
				$sqlUpdateEp.="`EpisodeName` = '".addslashes($oneEp->EpisodeName)."', ";
			    }
			    if(!empty($oneEp->FirstAired)){
				$sqlUpdateEp.="`FirstAired` = '".addslashes($oneEp->FirstAired)."', ";
			    }
			    if(!empty($oneEp->GuestStars)){
				$sqlUpdateEp.="`GuestStars` = '".addslashes($oneEp->GuestStars)."', ";
			    }
			    if(!empty($oneEp->Director)){
				$sqlUpdateEp.="`Director` = '".addslashes($oneEp->Director)."', ";
			    }
			    if(!empty($oneEp->Writer)){
				$sqlUpdateEp.="`Writer` = '".addslashes($oneEp->Writer)."', ";
			    }
			    if(!empty($oneEp->Overview)){
				$sqlUpdateEp.="`Overview` = '".addslashes($oneEp->Overview)."', ";
			    }
			    if(!empty($oneEp->ProductionCode)){
				$sqlUpdateEp.="`ProductionCode` = '".addslashes($oneEp->ProductionCode)."', ";
			    }
			    if(!empty($oneEp->lastupdated)){
				$sqlUpdateEp.="`lastupdated` = '".addslashes($oneEp->lastupdated)."', ";
			    }
			    if(!empty($oneEp->flagged)){
				$sqlUpdateEp.="`flagged` = '".addslashes($oneEp->flagged)."', ";
			    }
			    if(!empty($oneEp->absolute_number)){
				$sqlUpdateEp.="`absolute_number` = '".addslashes($oneEp->absolute_number)."', ";
			    }
			    if(!empty($oneEp->filename)){
				$sqlUpdateEp.="`filename` = '".addslashes($oneEp->filename)."', ";
			    }
			    if(!empty($oneEp->IMDB_ID)){
				$sqlUpdateEp.="`IMDB_ID` = '".addslashes($oneEp->IMDB_ID)."', ";
			    }
			    if(!empty($oneEp->EpImgFlag)){
				$sqlUpdateEp.="`EpImgFlag` = '".addslashes($oneEp->EpImgFlag)."', ";
			    }
			    if(!empty($oneEp->Rating)){
				$sqlUpdateEp.="`Rating` = '".addslashes($oneEp->Rating)."', ";
			    }
			    if(!empty($oneEp->SeasonNumber)){
				$sqlUpdateEp.="`SeasonNumber` = '".addslashes($oneEp->SeasonNumber)."', ";
			    }
			    if(!empty($oneEp->Language)){
				$sqlUpdateEp.="`Language` = '".addslashes($oneEp->Language)."', ";
			    }
			    
			    $sqlUpdateEp.="`timestampSrvLastUpdate` = '".$this->retrieveServerTime();
			    
			    $sqlUpdateEp.="' WHERE `t_episode`.`idt_episode` = ".$oneEp->id." AND `t_episode`.`seriesid` = ".$oneEp->seriesid.";";
			    
			    mysql_query($sqlUpdateEp);
			    
			    
			}
			
			
		    }
		    
		    
		    
		    
		}
		
		/*
		    ouvre chaque xml épisode
		    test si appartient à une série qu'on a
		*/
		
		
		if($flag==false){
		    $urlEpXml=$this->t_mirrors[0]."/api/".$this->apiKey."/episodes/".$e."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/episodes/4244057/en.xml
		    
		    
		    $xmlEp=$this->curl_get_file_contents($urlEpXml);
		    $resultsEp = new SimpleXMLElement($xmlEp);
		    
		    foreach($resultsEp->Episode as $oneEp){
			$resSerieExist=mysql_query("SELECT count(*) as col  FROM `t_shows` WHERE `idt_shows`='".$oneEp->seriesid."'");
			
			while($rowSerieExist = mysql_fetch_array($resSerieExist)){
			    
			    if($rowSerieExist['col']==1){
				/*
				 = c'est un nouvel épisode d'une série qu'on a en base.
				 On insert dans notre base.
				*/
				
				mysql_query("INSERT INTO `t_episode`
                                (`idt_episode`, `Combined_episodenumber`, `Combined_season`, `Director`, `EpImgFlag`,
                                `EpisodeName`, `EpisodeNumber`, `FirstAired`, `GuestStars`, `IMDB_ID`, `Language`,
                                `Overview`, `ProductionCode`, `Rating`, `RatingCount`, `SeasonNumber`, `Writer`,
                                `absolute_number`, `filename`, `lastupdated`, `seasonid`, `seriesid`, `timestampSrvLastUpdate`)
                                VALUES ('".$oneEp->id."', NULL, NULL, '".addslashes($oneEp->Director)."', '".$oneEp->EpImgFlag."',
                                '".addslashes($oneEp->EpisodeName)."', '".$oneEp->EpisodeNumber."', '".$oneEp->FirstAired."', '".addslashes($oneEp->GuestStars)."', '".$oneEp->IMDB_ID."', '".$oneEp->Language."',
                                '".addslashes($oneEp->Overview)."', '".$oneEp->ProductionCode."', '".$oneEp->Rating."', NULL, '".$oneEp->SeasonNumber."', '".addslashes($oneEp->Writer)."',
                                '".$oneEp->absolute_number."', '".addslashes($oneEp->filename)."', '".$oneEp->lastupdated."', '".$oneEp->seasonid."', '".$oneEp->seriesid."', '".$this->retrieveServerTime()."');");
				
				/*
				 Les infos suivantes ne sont pas présentes dans l'episode.xml :
				    Combined_episodenumber
				    Combined_season
				    RatingCount
				 Elles seront remplacée par des NULL
				*/
			    }
			    
			}
			
		    }
		}
		
	    }
	    

	    /*
	     update timestamp t_config
	    */
	    mysql_query("update t_config set numIntValue='".$TIMECURUPDATE."' where `name`='lastUpdate'");
	    
	    
	    
	    
	    set_time_limit(30);
	}
	
	
	public function doesSerieExist($id){
	    $res=mysql_query("SELECT count(*) as col  FROM `t_shows` WHERE `idt_shows`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['col']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	
	public function doesSerieHavePoster($id){
	    $res=mysql_query("SELECT COUNT( * ) AS col FROM  `t_shows` WHERE  `idt_shows` =  '73762' AND poster <> ''");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['col']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	
	public function doesEpisodeExist($id){
	    $res=mysql_query("SELECT count(*) as col  FROM `t_episode` WHERE `idt_episode`='".$id."'");
	    
	    while($row = mysql_fetch_array($res)){
		if($row['col']=='1'){
		    return true;
		}else{
		    return false;
		}
	    }
	}
	
	
	
	
        /*
         UTILITIES
        */
        
        private function curl_get_file_contents($URL)
        {
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $URL);
            $contents = curl_exec($c);
            curl_close($c);
            
            if ($contents) return $contents;
                else return FALSE;
        }
        
        public function ping($host, $port, $timeout) {
            /*
             Test if mirrors are up.
             0 -> down
             1 -> up
             
             params :
             $host : server adress, without http://
             $port
             $timeout : ms
            */
            
            $tB = microtime(true); 
            $fP = @fSockOpen($host, $port, $errno, $errstr, $timeout); 
            if (!$fP) { return 0; } 
            $tA = microtime(true);
            return 1;//just return mirror state. 
            /*
             Use if multiple mirrors, and we want the best perfs. 
             return round((($tA - $tB) * 1000), 0)." ms";
            */
        }
    }
?>