<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Connexion</title>

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
		<div id="main" class="grid_16">
				
                        <?php
                    
                            /*
                             Paramètre get de la page : add=idSerie
                                                        del=idSerie
                            */
                            if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
                                echo 'Vous &ecirc;tes d&eacute;j&agrave; connect&eacute;. ';
                                    
                            }else{
                                include('./logbox.php');
                            }
                            
                        
                        ?>
                        
			
			
		
		<!-- main ends -->
		</div>
		
		<!-- left-columns starts -->
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
