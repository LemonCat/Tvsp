<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp -
<?php
    if(!empty($_GET['id'])){
        
        //retrieveSerieName
        $serieName = $myAPI->retrieveSerieName($_GET['id']);
        echo $serieName;
        
    
?>
</title> 

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Erwin Aligam - styleshout.com" />
<meta name="description" content="Site Description Here" />
<meta name="keywords" content="keywords, here" />
<meta name="robots" content="index, follow, noarchive" />
<meta name="googlebot" content="noarchive" />

<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />

<script type="text/javascript" src="lib/jquery.js"></script>

</head>
<body>

	<!-- header starts-->
	<div id="header-wrap">
            <div id="header" class="container_16" style="height: 220px;">						
		
		<!--<h1 id="logo-text"><a href="index.php" title="">TV Show Planning</a></h1>
		<p id="intro">VOS s&eacute;ries, VOTRE planning ...</p>-->
		
		<!-- navigation -->
		<div  id="nav">
			<ul>
				<li><a href="./index.php">Accueil</a></li>
                                <li><a href="./planning.php">Mon planning</a></li>
				<li><a href="./series.php">Mes s&eacute;ries</a></li>
				<li><a href="./flux.php">Flux</a></li>
				<li><a href="./compte.php">Mon compte</a></li>
				<li><a href="./stats.php">Stats</a></li>
			</ul>		
		</div>		
		
		
                    <?php
                        //758px × 140px
                        
                        $strBannerLocation = $myAPI->retrieveBanner($_GET['id']);
                        
                        $strStyle='position: absolute;
                        width: 760px;
                        height: 142px;	
                        right: 91px; top: 55px;';
                        //background: url('.$strBannerLocation.') no-repeat;
                        
                        
                        echo '<div style="'.$strStyle.'">';
                        echo '<img src="'.$strBannerLocation.'" alt="Banni&eacute;re"/>';
                        echo '</div>';
                    ?>
                
		
		<?php include('./searchBox.php');?>
	
	<!-- header ends here -->
            </div>
        </div>
	
	<!-- content starts -->
	<div id="content-outer"><div id="content-wrapper" class="container_16">
	
		<!-- main -->
		
                    <?php
                        echo '<div id="main" class="grid_16">';
                        echo '<h2>'.$serieName.'</h2>';
                        echo '</div>';
                    
                    
                    
                    $result = $myAPI->retrieveSerieInfos($_GET['id']);
                    
                    while($row = mysql_fetch_array($result)){
                        echo '<div id="left-columns" class="grid_4">';
                            //right column
                            echo '<br /><strong>Statut : </strong>'.$myAPI->StatusFr($row['Status']);
                            echo '<br /><strong>Nombre de saisons : </strong>'.$myAPI->getNbOfSeasons($_GET['id']);
                            echo '<br /><br /><strong>Diffus&eacute; les : </strong>'.$myAPI->dayOfWeekFr($row['Airs_DayOfWeek']).' &agrave; '.$row['Airs_Time'];
                            echo '<br /><strong>Diffus&eacute; par : </strong>'.$row['Network'];
                            echo '<br /><strong>Dur&eacute;e d\'un &eacute;pisode : </strong>'.$row['Runtime'].' mins.';
                            echo '<br /><br /><strong>Premi&egrave;re diffusion : </strong>'.$row['FirstAired']; //FORMAT US, A CHANGER.
                            echo '<br /><br /><strong>Genres : </strong>';
                            
                            $g=mysql_query("SELECT g.genreName FROM t_genres g, t_shows_has_t_genres tg WHERE t_shows_idt_shows = '".$_GET['id']."' AND tg.t_genres_idt_genres = g.`idt_genres`");
                            $cptG=0;
                            while($rowg = mysql_fetch_array($g)){
                                if($cptG>=1){
                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rowg['genreName'].'<br />';
                                }else{
                                    echo $rowg['genreName'].'<br />';
                                }
                                $cptG++;
                            }
                            
                        echo '</div>';
                    
                        
                    
                        echo '<div id="main" class="grid_8">';
                            //Poster if exist : 148*218
                            if($myAPI->doesSerieHavePoster($_GET['id'])){
                                if($strPosterLocation=$myAPI->retrievePoster($_GET['id'])){
                                    echo '<div style="float: right; margin: 20px 15px 15px 10px;">';
                                    echo '<a title="Poster" href="'.$strPosterLocation.'" TARGET="_blank"><img src="'.$strPosterLocation.'" style=" height: 218px; width: 148px;"></a>';
                                    echo '</div>';
                                }
                            }
                            
                            
                            echo '<h3>R&eacute;sum&eacute; : </h3><br />';
                            echo $row['Overview'];
                            echo '<br /><br />';
                            
                            echo '<img src="./images/tvdb.png" style="	background: #FAFAFA; border: 0; padding: 0;"><a href="http://thetvdb.com/?tab=series&id='.$row['idt_shows'].'" target="_blank">  Voir sur TheTVDB.com. </a><br />';
                            echo '<img src="./images/imdb.png" style="	background: #FAFAFA; border: 0; padding: 0;"><a href="http://www.imdb.com/title/'.$row['IMDB_ID'].'" target="_blank">  Voir sur IMDb. </a>';
                            
                            //echo '<br /><br /><a href="javascript:history.back()">Retour</a>';
                        echo '</div>';
                    }
                    
                    echo '<div id="main" class="grid_16">
                <h2>Liste des &eacute;pisodes</h2>
                
                <div><!--GLOBAL DIV FOR EP DISPLAY-->';
                
                $result=mysql_query("SELECT DISTINCT `SeasonNumber` FROM `t_episode` WHERE `seriesid`='".$_GET['id']."' and SeasonNumber !=0 order by SeasonNumber");
                    
                    $i=0;
                    
                    while($row = mysql_fetch_array($result)){
                        
                        //circle through season
                        echo '<div id="epListSeasonHeader"><a id="toogle'.$i.'" style="cursor:pointer">Saison '.$row['SeasonNumber'].'</a></div>';
                        echo '<div id="slide'.$i.'"><table>';
                        $resultEp=mysql_query("SELECT `EpisodeName`, `EpisodeNumber`, `FirstAired`, idt_episode FROM `t_episode` WHERE `seriesid`='".$_GET['id']."'and `SeasonNumber`='".$row['SeasonNumber']."' order by `EpisodeNumber`");
                        echo '  <script>
                                $("#slide'.$i.'").toggle();
                
                                $(document).ready(function(){
                                    $("#toogle'.$i.'").click(function(){
                                        $("#slide'.$i.'").slideToggle("slow");
                                    });
                                });
                                </script>';
                        
                        
                        while($rowEp = mysql_fetch_array($resultEp)){
                            //List episodes for cur season
                            
                            echo '<tr><td style="width: 20px; text-align: center;">'.$rowEp['EpisodeNumber'].'</td>';
                            echo '<td style="width: 800px;"><a href="./episode.php?id='.$rowEp['idt_episode'].'">'.$rowEp['EpisodeName'].'</a></td>';
                            echo '<td>'.$rowEp['FirstAired'].'</td></tr>';
                            
                        }
                        echo '</table></div>';
                        $i++;
                    }
                    
                    echo '</div></div>';
                    
                    
                    
                    
                    
                    
    } //fermage du if _get not null du bout d'au dessus. .     
?>
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
