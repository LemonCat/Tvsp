<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="fr">

<head>

<title>Tvsp - Recherche</title>

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
			if(!empty($_GET['qsearch'])){
				$terms=$_GET['qsearch'];
				$terms=str_replace('\\', '', $terms);
				
				echo '<h2>R&eacute;sultat de recherche : '.$terms.'</h2>';
		
				if($t_results=$myAPI->search($terms)){
				if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
					echo '<table width="100%">
					<tr><th colspan="2" style="text-align: center">Indications : </th></tr>
					
					<tr>
						<td style="text-align: center" width="50%">
							<img src="./images/plus.png" style="padding: 0px; border: 0px;" /><br />
							Cette s&eacute;rie existe d&eacute;j&agrave; en base de donn&eacute;e, l\'ajout sera rapide. 
						</td>
						<td style="text-align: center" width="50%">
							<img src="./images/plusNotInDb.png" style="padding: 0px; border: 0px;" /><br />
							Cette s&eacute;rie n\'existe par encore dans notre base de donn&eacute;e. Veuillez patienter lors de la r&eacute;cup&eacute;ration des donn&eacute;es si vous souhaitez suivre cette s&eacute;rie. 
						</td>
					</tr>
					<tr>
					<td colspan="2" style="text-align: center">Cliquez sur <img src="./images/plus.png" style="padding: 0px; border: 0px;" />/<img src="./images/plusNotInDb.png" style="padding: 0px; border: 0px;" /> pour ajouter une s&eacute;rie &agrave; votre compte. </td>
					</tr>
				</table>';
				}
				
			
				echo '<br />
				
				<div id="searchResult"><table>';
			
			
			    
				
					
					/*
					$this->t_sResults[$i]=array(	'seriesid' => $s->seriesid,
									'SeriesName' => $s->SeriesName,
									'banner' => $s->banner,
									'FirstAired' => $s->FirstAired,
									'Overview' => $s->Overview);
					*/
					
					
					$banner; //758px × 140px
					$colorAlt=0;
					foreach($t_results as $val){
						/*
						 Weird display for background images. 
						 $banner=$myAPI->t_mirrors[0].'/banners/'.$val['banner'];
						 echo '<div style="background-image: url('.$banner.'); height: 140px; width: 758px; border: 1px solid black;">';
						*/
						if(($colorAlt%2)==0){
							echo '<tr style="background: #EFFAE6;">';
						}else{
							echo '<tr style="background: #fff;">';
						}
						
						
						echo '<td>';
						if($myAPI->doesSerieExist($val['seriesid'])){
							echo '<a href="./oneSerie.php?id='.$val['seriesid'].'">'.$val['SeriesName'].' ('.date('Y', strtotime($val['FirstAired'])).')</a>';
						}else{
							echo $val['SeriesName'].' ('.date('Y', strtotime($val['FirstAired'])).')';
						}
						echo '</td>';
						
						if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
							//lire l'id série et le lier sur ./series.php?new=id
							echo '<td>';
							if($myAPI->doesSerieExist($val['seriesid'])){
								echo '<a href="./series.php?add='.$val['seriesid'].'"><img src="./images/plus.png" style="padding: 0px; border: 0px;" alt="Add" title="Suivre cette s&eacute;rie"/></a>';
							}else{
								echo '<a href="./series.php?add='.$val['seriesid'].'"><img src="./images/plusNotInDb.png" style="padding: 0px; border: 0px;" alt="Add" title="Suivre cette s&eacute;rie"/></a>';
							}
							
							
							echo '</td>';
						}
						
						
						
						/*
						 Si logué, on affiche un bouton pour ajouter cette série à l'user. 
						*/
						
						echo '</tr>';
						$colorAlt++;
					}
		
					if($_GET['qsearch']=='Rechercher'){
					    echo '<tr><td>Veuillez d\'abord sp&eacute;cifier des termes de recherches.</td></tr>';
					}
				    }else{
					echo '<br />Recherche impossible, TVDB.com est actuellement hors ligne. ';
				    }
				    
			}	
                ?>
		</table></div>
		</div>
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
