<?php
            include("./class/cApi.php");            
            $myAPI=new cApi();
            $myAPI->retrieveMirrors();
            $myAPI->testMirrors();
            
            
            set_time_limit(0);
	    
	    $apiKey="7723773B81E35AE7";
            
	    $TIMECURUPDATE=$myAPI->retrieveServerTime();
	    
	    $t_serieIDs;
	    $t_episodeIds;
	    
	    $lastTimestamp; //last update timestamp. 
	    $resTs=mysql_query("SELECT numIntValue FROM `t_config` WHERE `name` = 'lastUpdate'");
	    while($rowTs = mysql_fetch_array($resTs)){
		$lastTimestamp=$rowTs['numIntValue'];
	    }
	    
	    
	    
	    $urlUpdateXml="http://www.thetvdb.com/api/Updates.php?type=all&time=".$lastTimestamp;
	    //$xmlUpdate=curl_get_file_contents($urlUpdateXml);
	    $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $urlUpdateXml);
            $xmlUpdate = curl_exec($c);
            curl_close($c);
            
            
            
	    $resultsUpdate = new SimpleXMLElement($xmlUpdate);
		    
	    echo 'Series : <br />';
	    foreach ($resultsUpdate->Series as $s) {
                
		    if($myAPI->doesSerieExist($s)){ //Si la série est présente en base, on l'update. 
			echo $s.'<br />';
                        
			$urlSerieXml=$myAPI->t_mirrors[0]."/api/".$apiKey."/series/".$s."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/series/75760/en.xml
			
			//$xmlSerie=curl_get_file_contents($urlSerieXml);
                        $c = curl_init();
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_URL, $urlSerieXml);
                        $xmlSerie = curl_exec($c);
                        curl_close($c);
                        
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
			    
			    $sqlUpdateSerie.="`timestampSrvLastUpdate` = '".$myAPI->retrieveServerTime();
			    
			    $sqlUpdateSerie.="' WHERE `idt_shows` = '".$oneSerie->id."'";
			    
			    mysql_query($sqlUpdateSerie);
			}
			
		    }
                
            
	    }
            
            
            
            
            
            
            /******************************************************************************************/
            /**********************************EPISODES************************************************/
            /******************************************************************************************/
            //doesEpisodeExist
            echo 'Episodes : <br />';
            foreach($resultsUpdate->Episode as $e) {
		/*TRAITEMENT DES EPISODES*/
                
                
		    if($myAPI->doesEpisodeExist($e)){ //si on a cet épisode en base, on update.
                        echo 'Update : '.$e.'<br />';
			$urlEpXml=$myAPI->t_mirrors[0]."/api/".$apiKey."/episodes/".$e."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/episodes/161312/en.xml
                        
			//$xmlEp=curl_get_file_contents($urlEpXml);
                        $c = curl_init();
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_URL, $urlEpXml);
                        $xmlEp = curl_exec($c);
                        curl_close($c);
                        
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
			    
			    $sqlUpdateEp.="`timestampSrvLastUpdate` = '".$myAPI->retrieveServerTime();
			    
			    $sqlUpdateEp.="' WHERE `t_episode`.`idt_episode` = ".$oneEp->id." AND `t_episode`.`seriesid` = ".$oneEp->seriesid.";";
			    
                            
			    mysql_query($sqlUpdateEp);
			    
			    
			}
			
			
		    }else{
                        /*
                            ouvre chaque xml épisode
                            test si appartient à une série qu'on a
                        */
                        
                        
                        $urlEpXml=$myAPI->t_mirrors[0]."/api/".$apiKey."/episodes/".$e."/en.xml"; //http://www.thetvdb.com/api/7723773B81E35AE7/episodes/161312/en.xml
                        
			//$xmlEp=curl_get_file_contents($urlEpXml);
                        $c = curl_init();
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($c, CURLOPT_URL, $urlEpXml);
                        $xmlEp = curl_exec($c);
                        curl_close($c);
                        
			$resultsEp = new SimpleXMLElement($xmlEp);
                        foreach ($resultsEp->Episode as $oneEp){
                            if($myAPI->doesSerieExist($oneEp->seriesid)){ //si serie spécifié dans cet episode.xml existe en base = nouvel episode
                                echo 'New : '.$e.'<br />';
                                mysql_query("INSERT INTO `t_episode`
                                (`idt_episode`, `Combined_episodenumber`, `Combined_season`, `Director`, `EpImgFlag`,
                                `EpisodeName`, `EpisodeNumber`, `FirstAired`, `GuestStars`, `IMDB_ID`, `Language`,
                                `Overview`, `ProductionCode`, `Rating`, `RatingCount`, `SeasonNumber`, `Writer`,
                                `absolute_number`, `filename`, `lastupdated`, `seasonid`, `seriesid`, `timestampSrvLastUpdate`)
                                VALUES ('".$oneEp->id."', NULL, NULL, '".addslashes($oneEp->Director)."', '".$oneEp->EpImgFlag."',
                                '".addslashes($oneEp->EpisodeName)."', '".$oneEp->EpisodeNumber."', '".$oneEp->FirstAired."', '".addslashes($oneEp->GuestStars)."', '".$oneEp->IMDB_ID."', '".$oneEp->Language."',
                                '".addslashes($oneEp->Overview)."', '".$oneEp->ProductionCode."', '".$oneEp->Rating."', NULL, '".$oneEp->SeasonNumber."', '".addslashes($oneEp->Writer)."',
                                '".$oneEp->absolute_number."', '".addslashes($oneEp->filename)."', '".$oneEp->lastupdated."', '".$oneEp->seasonid."', '".$oneEp->seriesid."', '".$myAPI->retrieveServerTime()."');");
                            }
                        }
                        
                        
                    }
		    
		    
		    
		    
		
		
		
		
	    }
	    
            
            
	    /*
	     update timestamp t_config
	    */
	    mysql_query("update t_config set numIntValue='".$TIMECURUPDATE."' where `name`='lastUpdate'");
	    
            
            
	    
	    
	    set_time_limit(30);
?>