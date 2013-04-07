<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - &Eacute;pisode</title>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Erwin Aligam - styleshout.com" />
<meta name="description" content="Site Description Here" />
<meta name="keywords" content="keywords, here" />
<meta name="robots" content="index, follow, noarchive" />
<meta name="googlebot" content="noarchive" />

<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />

</head>
<body>

	<!-- header starts-->
	<div id="header-wrap"><div id="header" class="container_16">						
		
		<h1 id="logo-text"><a href="index.php" title="">TV Show Planning</a></h1>
		<p id="intro">VOS s&eacute;ries, VOTRE planning ...</p>
		
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
		
		<div id="header-image"></div> 		
		
		<?php include('./searchBox.php');?>
	
	<!-- header ends here -->
	</div></div>
	
	<!-- content starts -->
	<div id="content-outer"><div id="content-wrapper" class="container_16">
		<!-- main -->
		<?php
                    
                    $sName=$myAPI->retrieveEpName($_GET['id']);
                    echo '<div id="main" class="grid_16">';
                        if(!empty($_GET['id'])){
                            $idt_shows;
                            
                            //SELECT s.SeriesName FROM t_shows s, t_episode e WHERE e.idt_episode='184603' and e.seriesid=s.`idt_shows`
                            $res=mysql_query("SELECT s.idt_shows, s.SeriesName, e.EpisodeNumber, e.SeasonNumber FROM t_shows s, t_episode e WHERE e.idt_episode='".$_GET['id']."' and e.seriesid=s.`idt_shows`");
                            while($row = mysql_fetch_array($res)){
                                $idt_shows=$row['idt_shows'];
                                
                                $sNo=$row['SeasonNumber'];
				if($sNo<10){
                                    $sNo='0'.$row['SeasonNumber'];
				}
				
				$epNo=$row['EpisodeNumber'];
				if($epNo<10){
				    $epNo='0'.$row['EpisodeNumber'];
				}
                                
                                echo '<h2>'.$row['SeriesName'].' : '.$sName.' (S'.$sNo.'E'.$epNo.')</h2>';
                                echo '<h3></h3>';
                            }
                        }
                    echo '</div>';
                    
                    echo '<div id="main" style="text-align:right; width: 420px; display: inline; float: left; margin-left: 10px; margin-right: 10px;"><img src="'.$myAPI->retrieveEpImg($_GET['id']).'" />';
                    
                    echo '<br /><br /><a href="./oneSerie.php?id='.$idt_shows.'">Voir la fiche s&eacute;rie.</a>';
                    //echo '<br /><br /><a href="javascript:history.back()">Retour. </a>'; <-on ne peut venir que du planning, et il est dans le menu
                    
                    echo '</div>';
                    
                    echo '<div id="main" style="width: 500px; display: inline; float: left; margin-left: 10px; margin-right: 10px;">';
                    $resEpInfos=mysql_query("SELECT * FROM t_episode WHERE idt_episode='".$_GET['id']."'");
                    
                    while($rowEpInfos = mysql_fetch_array($resEpInfos)){
                        echo '<h3>R&eacute;sum&eacute; : </h3>'.$rowEpInfos['Overview'];
                        echo '<h3>Premi&egrave;re diffusion : </h3>'.date('d', strtotime($rowEpInfos['FirstAired'])).'-'.date('m', strtotime($rowEpInfos['FirstAired'])).'-'.date('Y', strtotime($rowEpInfos['FirstAired']));
                        
                        if($rowEpInfos['Writer']!=''){
                            echo '<h3>Sc&eacute;naristes : </h3>';
                            $writers=explode('|', $rowEpInfos['Writer']);
                            foreach($writers as $v){
                                echo '<ul>';
                                if($v!=""){
                                    echo '<li>'.$v.'</li>';
                                }
                                echo '</ul>';
                            }
                        }
                        
                        if($rowEpInfos['GuestStars']!=''){
                            echo '<h3>Guests Stars: </h3>';
                            $guests=explode('|', $rowEpInfos['GuestStars']);
                            foreach($guests as $v){
                                echo '<ul>';
                                if($v!=""){
                                    echo '<li>'.$v.'</li>';
                                }
                                echo '</ul>';
                            }
                        }
                        
                        
                    }
                    
                    echo '</div>';
                    
                ?>

	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
