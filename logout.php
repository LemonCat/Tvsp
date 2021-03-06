<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">

<head>

<title>Tvsp - D&eacute;connexion</title>

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
				<li id="current"><a href="./index.php">Accueil</a></li>
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
                <?php session_destroy();
                
                    function redirect($url, $time=3)
                    {     
                       //On v�rifie si aucun en-t�te n'a d�j� �t� envoy�    
                       if (!headers_sent())
                       {
                         header("refresh: $time;url=$url");
                         exit;
                       }
                       else
                       {
                         echo '<meta http-equiv="refresh" content="',$time,';url=',$url,'">';
                       }
                    }
                    echo '<p align="center">Vous &ecirc;tes &agrave; pr&eacute;sent d&eacute;connect&eacute;. <br />';
                        
                    echo '<strong>Redirection vers l\'accueil dans 2 secondes ...</strong><br />';
                    
                    echo '<a href="./index.php">Retour &agrave; l\'accueil. </a></p>';
                    
                ?>
                
                
                
            </div>
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>
        
</body>
</html>
<?php redirect("./index.php","2");?>