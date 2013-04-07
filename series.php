<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Mes s&eacute;ries</title>

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
				<li id="current"><a href="./series.php">Mes s&eacute;ries</a></li>
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
		<div id="main" class="grid_16">
		
                    <h2>Mes s&eacute;ries</h2>
                    <?php
                    
                    /*
                     Paramètre get de la page : add=idSerie
                                                del=idSerie
                    */
                        if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
                            if(!empty($_GET['add'])){
                                //ajouter serie à l'user.
                                $myUser->addShowToUser($_SESSION['userID'], $_GET['add']);
                            }
                            
                            if(!empty($_GET['del'])){
                                //ajouter serie à l'user.
                                $myUser->delShowFromUser($_SESSION['userID'], $_GET['del']);
                            }
                            
                            $result=mysql_query("select s.SeriesName, s.idt_shows from t_shows s, t_users_has_t_shows us where us.t_users_idt_users ='".$_SESSION['userID']."' and us.t_shows_idt_shows = s.idt_shows ORDER BY s.SeriesName");
                            
                            $colorAlt=0;
                            $nbSeries=mysql_num_rows($result);
                            echo '<br />';
                            if($nbSeries==0){
                                echo '<div style="margin-left: auto; margin-right: auto; width: 453px;"><strong>Vous ne suivez pas encore de s&eacute;ries. </strong><br />
                                        Pour suivre une s&eacute;rie, commencez effectuer une recherche. <br /><br />';
                                echo '<img src="./images/howToSearch.png" /></div>';
                            }else{
                                echo 'Vous suivez actuellement '.$nbSeries.' s&eacute;ries. <br /><br />';
                            }
                            
                            
                            echo '<div id="searchResult"><table>';
                            
                            
                            while($row = mysql_fetch_array($result)){
                                
                                if(($colorAlt%2)==0){
					echo '<tr style="background: #EFFAE6;">';
				}else{
					echo '<tr style="background: #fff;">';
				}
                                
                                echo '<td><a href="./oneSerie.php?id='.$row['idt_shows'].'">'.$row['SeriesName'].'</td><td>';
                                
                                echo '<a href="./series.php?del='.$row['idt_shows'].'"><img src="./images/moins.png" style="padding: 0px; border: 0px;" alt="Add" title="Supprimer cette s&eacute;rie"/></a>';
                                
                                
                                echo '</td>';
                                
                                echo '</tr>';
                                $colorAlt++;
                            }
                            
                            echo '</table></div>';
                        }else{
                            
			    echo '<br />Veuillez d\'abord vous connecter : ';
			    include('./logbox.php');
			}
                        
                    
                    ?>
                    
		
		</div>
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
