<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Stats</title>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Erwin Aligam - styleshout.com" />
<meta name="description" content="Site Description Here" />
<meta name="keywords" content="keywords, here" />
<meta name="robots" content="index, follow, noarchive" />
<meta name="googlebot" content="noarchive" />

<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />

<?php

    $t_values;
    $result=mysql_query("SELECT * FROM `t_genres` WHERE 1 order by genreName");
    while($row = mysql_fetch_array($result)){
        $t_temp['gName']=$row['genreName'];
        
        
        $resultVal=mysql_query("SELECT count(*) as col FROM `t_shows_has_t_genres` WHERE `t_genres_idt_genres`='".$row['idt_genres']."'");
        while($rowVal = mysql_fetch_array($resultVal)){
            $t_temp['gValue']=$rowVal['col'];
        }
        
        $t_values[]=$t_temp;
    }
?>


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
				<li id="current"><a href="./stats.php">Stats</a></li>
			</ul>		
		</div>		
		
		<div id="header-image"></div> 		
		
		<?php include('./searchBox.php');?>					
	
	<!-- header ends here -->
	</div></div>
	
	<!-- content starts -->
	<div id="content-outer"><div id="content-wrapper" class="container_16">
            <div id="main" class="grid_16">
                <h2><a href="./stats.php">Statistiques</a></h2>
                <br />
                
                <h3>&Eacute;tat de la base de donn&eacute;es</h3>
                
                <?php
                    $resNbUsers=mysql_query("SELECT count(*) as col FROM `t_users` WHERE 1");
                    while($rNbUsers = mysql_fetch_array($resNbUsers)){
                        echo '<strong>Nombre d\'utilisateurs : </strong>'.$rNbUsers['col'];
                    }
                    
                    $nbShows;
                    $resNbShows=mysql_query("SELECT count(*) as col FROM `t_shows` WHERE 1 ");
                    while($rNbShows = mysql_fetch_array($resNbShows)){
                        $nbShows=$rNbShows['col'];
                        echo '<br /><strong>Nombre de s&eacute;ries suivies par nos utilisateurs : </strong>'.$rNbShows['col'];
                    }
                    
                    $resNbEp=mysql_query("SELECT count( * ) AS col FROM `t_episode` WHERE 1 ");
                    while($rNbEp = mysql_fetch_array($resNbEp)){
                        echo '<br /><strong>Nombre d\'&eacute;pisodes sur ces '.$nbShows.' s&eacuteries : </strong>'.$rNbEp['col'];
                    }
                    
                    
                    $resTotalUser=mysql_query("SELECT count( * ) as col FROM `t_users` ");
                    $resTotalSouscriptions=mysql_query("SELECT count( * ) as col FROM `t_users_has_t_shows` ");
                    
                    while($rTotalUser = mysql_fetch_array($resTotalUser)){
                        $a=$rTotalUser['col'];
                    }
                    while($rTotalSouscriptions = mysql_fetch_array($resTotalSouscriptions)){
                        $b=$rTotalSouscriptions['col'];
                    }
                    
                    echo '<br /><strong>Nombre moyen de s&eacute;ries par utilisateur : </strong>'.number_format($b/$a,2);
                
                ?>
                <h3>R&eacute;partition des s&eacute;ries par genre</h3>
                <?php
                
                    //http://colorschemedesigner.com/#2C41Tc5s-w0w0
                    $strGenreValues='';
                    $strGenreNames='';
                    $strGenreLegend='';
                    
                    for($i=0;$i<count($t_values);$i++){
                        if($i==(count($t_values)-1)){
                            $strGenreValues=$strGenreValues.$t_values[$i]['gValue'];
                            $strGenreNames=$strGenreNames.$t_values[$i]['gName'];
                            
                            $strGenreLegend=$strGenreLegend.$t_values[$i]['gName'].' ('.$t_values[$i]['gValue'].')';
                        }else{
                            $strGenreValues=$strGenreValues.$t_values[$i]['gValue'].',';
                            $strGenreNames=$strGenreNames.$t_values[$i]['gName'].'|';
                            
                            $strGenreLegend=$strGenreLegend.$t_values[$i]['gName'].' ('.$t_values[$i]['gValue'].')'.'|';
                        }
                        
                        //Pour la légende, tenter de concaténer en plus le nom. Et voir si c'est pas trop dégueu. 
                    }
                    //chdl=30&deg;|40&deg;|50&deg;|60&deg
                    $terms=str_replace(' ', '%20', $strGenreNames);
                    
                    if($myAPI->ping("http://chart.apis.google.com/", 80, 10)){
                        echo '<img src="http://chart.apis.google.com/chart?cht=p3&chd=t:'.$strGenreValues.
                             '&chs=920x300&chl='.$strGenreNames.'&chdl='.$strGenreLegend.'&chco=662060" />';
                    }else{
                        echo "Graphique momentan&eacute;ment indisponible : WebService Google Charts indisponible. "; 
                    }
                    
                    
                ?>
            </div>
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
