<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - DropD Test</title>

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
            <div id="main" class="grid_16">
                <h2><a href="./dropd.php">DropD</a></h2>
                
                <div><!--GLOBAL DIV FOR EP DISPLAY-->
                
                <?php
                    $result=mysql_query("SELECT DISTINCT `SeasonNumber` FROM `t_episode` WHERE `seriesid`='79349' and SeasonNumber !=0 order by SeasonNumber");
                    
                    $i=0;
                    
                    while($row = mysql_fetch_array($result)){
                        
                        //circle through season
                        echo '<div id="epListSeasonHeader"><a id="toogle'.$i.'" style="cursor:pointer">Saison '.$row['SeasonNumber'].'</a></div>';
                        echo '<div id="slide'.$i.'"><table>';
                        $resultEp=mysql_query("SELECT `EpisodeName`, `EpisodeNumber`, `FirstAired` FROM `t_episode` WHERE `seriesid`='79349'and `SeasonNumber`='".$row['SeasonNumber']."' order by `EpisodeNumber`");
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
                            echo '<td style="width: 800px;">'.$rowEp['EpisodeName'].'</td>';
                            echo '<td>'.$rowEp['FirstAired'].'</td></tr>';
                            
                        }
                        echo '</table></div>';
                        $i++;
                    }
                    
                    
                ?>
                </div>

                
                

                
            </div>
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
