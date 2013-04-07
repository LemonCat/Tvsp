<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Accueil</title>

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
	
		<!-- main -->
		<div id="main" class="grid_8">
				
			<h2><a href="index.php">Site en construction</a></h2>
			
			<p><img src="images/exPlanning.png" width="300" height="300" alt="Exemple de planning" class="float-left" />
			<strong>Ce site est d&eacute;di&eacute; &agrave; ceux qui suivent leurs s&eacute;ries en meme temps que leur premi&egrave;re diffusion (que ce soit au Etats-Unis ou autre part).<br /></strong>
			<br />
			<strong>Guide d'utilisation en 3 points :<br /></strong>
			1./ Cr&eacute;ation d'un compte utilisateur<br />
			2./ Recherche de s&eacute;ries<br />
			3./ Ajout &agrave; votre compte des s&eacute;ries<br />
			<br />
			<strong>Un fois ces 3 &eacute;tapes r&eacute;alis&eacute;es, vous aurez &agrave; disposition :<br /></strong>
			* Votre planning personalis&eacute;<br />
			* Des emails de rappels des vos &eacute;pisodes passant dans la journ&eacute;e/la semaine/le mois (pas encore fonctionnel)<br />
			* Un fichier calendrier de vos &eacute;pisodes de type ICS, lisible par la plupart des gestionnaires d'agenda (Google Agenda, smartphone ...)<br />
			
			
			
			</p>			
			<br />
			
			
			<div class="clear">&nbsp;</div>
			
		<!-- main ends -->
		</div>
		
		<!-- left-columns starts -->
		<div id="left-columns" class="grid_4">
		
			<?php include('./logbox.php');?>
			



			
		<!-- end left-columns -->
		</div>		
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
