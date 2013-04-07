<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="en">

<head>

<title>Tvsp - Mon planning</title>

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
                                <li id="current"><a href="./planning.php">Mon planning</a></li>
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
				if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
					echo '<h2><a href="./planning.php">Mon planning</a></h2>';
			
					echo '<div id="planning">';
				
					echo '<table>';
				
				
					$pMonth=date('m', time());
					$pYear=date('Y', time());
					
					$prevMonth;
					$prevYear;
					
					$nextMonth;
					$nextYear;
					
					if(isset($_GET['month'])){
						$pMonth=$_GET['month'];
					}
					if(isset($_GET['year'])){
						$pYear=$_GET['year'];
					}
					
					/*Previous month link*/
					if($pMonth-1<=0){
						$prevMonth=12;
						$prevYear=$myUser->previousYear($pYear);
					}else{
						$prevMonth=$myUser->previousMonth($pMonth);
						$prevYear=$pYear;
					}
					
					
					$prevLink='<a href="./planning.php?month='.$prevMonth.'&year='.$prevYear.'">&lt;</a>';
					
					
					/*Next Month link*/
					if($pMonth+1>=13){
						$nextMonth='01';
						$nextYear=$pYear+1;
					}else{
						$nextMonth=$myUser->nextMonth($pMonth);
						$nextYear=$pYear;
					}
					
					$nextLink='<a href="./planning.php?month='.$nextMonth.'&year='.$nextYear.'">&gt;</a>';
					
					
					
					echo '<tr><th colspan="7" align="center">'.$prevLink.'  '.$myUser->getMonthName($pMonth).' '.$pYear.'  '.$nextLink.'</th></tr>
					       
					       <tr>
					       <th>Lundi</th>
					       <th>Mardi</th>
					       <th>Mercredi</th>
					       <th>Jeudi</th>
					       <th>Vendredi</th>
					       <th>Samedi</th>
					       <th>Dimanche</th>
					       </tr>';
					
					
					
					//Nombre de jour dans le mois*/
					$nbDaysInMonth=date('t', strtotime($pYear.'-'.$pMonth.'-01'));
					$decalage= $myUser->calculDecalage($pMonth,$pYear);
					//echo 'decal='.$decalage;
					
					
					$uIsAdmin=$myUser->isAdmin($_SESSION['userID']);
					$t_episodes=$myUser->buildPlanning($_SESSION['userID'], $pMonth, $pYear);
					
					
					$nbLignes=5;
					$nbTD=0;
					
					$epNo;
					$sNo;
					
					//define nb of rows in table.
					if($nbDaysInMonth+$decalage>35){
						$nbLignes=6;
					}
					
					
					//($decalage+$nbDaysInMonth)
					for($i=1;$i<=$nbLignes;$i++){
						echo '<tr>';
						
						$colorCols=0;
						if(($i%2)==0){
							$colorCols=1;
						}else{
							$colorCols=0;
						}
						
						for($j=1;$j<8;$j++){
							
							
							
							//Write in td.
							$nbTD++;
							
							if(($nbTD-$decalage)==date('j', time()) && $pMonth==date('m', time())){
								echo '<td style="background-color:#CAE6B6">';
							}elseif((($nbTD-$decalage+$colorCols)%2)==0){
								//d day
								echo '<td style="background-color:#F5F4F3">';
							}else{
								echo '<td>';
							}
							
							if($nbTD-$decalage>0 && $nbTD-$decalage<=$nbDaysInMonth){

								echo '<div id="numJour">'.($nbTD-$decalage).'</div>';
								
								if(isset($t_episodes[$nbTD][0])){
									foreach($t_episodes[$nbTD] as $valeur)
									{
										if($valeur['SeasonNumber']!=0){
											echo '<strong><a href="./oneSerie.php?id='.$valeur['serieId'].'" style="color: #666666; ">'.$valeur['SerieName'].'</a></strong>';
											echo '<br />';
											
											echo '<a href="./episode.php?id='.$valeur['idt_episode'].'">';
											$sNo=$valeur['SeasonNumber'];
											if($valeur['SeasonNumber']<10){
												$sNo='0'.$valeur['SeasonNumber'];
											}
											
											$epNo=$valeur['EpisodeNumber'];
											if($valeur['EpisodeNumber']<10){
												$epNo='0'.$valeur['EpisodeNumber'];
											}
											
											echo 'S'.$sNo.'E'.$epNo;
											
											
											if($myUser->userTPB($_SESSION['userID'])){
												echo '&nbsp;<a title="Rechercher sur The Pirate Bay" href="http://thepiratebay.se/search/'.$valeur['SerieName'].' '.'S'.$sNo.'E'.$epNo.'" TARGET="_blank" style="padding: 0px; border: 0px;"><img src="./images/tpb.ico" style="padding: 0px; border: 0px;"></a>';
											}
											
											if($myUser->userSubtitles($_SESSION['userID'])){
												if($myUser->userSubFr($_SESSION['userID'])){
													echo '&nbsp;<a title="Rechercher des sous-titres Fran&ccedil;ais" href="http://www.google.fr/search?q='.$valeur['SerieName'].' '.'S'.$sNo.'E'.$epNo.' french subtitle" TARGET="_blank" style="padding: 0px; border: 0px;"><img src="./images/fr.png" style="padding: 0px; border: 0px;"></a>';
												}else{
													echo '&nbsp;<a title="Rechercher des sous-titres Anglais" href="http://www.google.fr/search?q='.$valeur['SerieName'].' '.'S'.$sNo.'E'.$epNo.' english subtitle" TARGET="_blank" style="padding: 0px; border: 0px;"><img src="./images/eng.png" style="padding: 0px; border: 0px;"></a>';
												}
											}
											
											echo '</a>';
											
											echo '<br />';
											echo '<br />';
										}
									}
								}
								
							}
							
							echo '</td>';
						}
						
						echo '</tr>';
					}
					echo '</table>';
					echo '<a href="./logout.php">Se d&eacute;connecter</a>';
				}else{
					echo '<h2><a href="./planning.php">Planning de d&eacute;monstration</a></h2>';
					
					echo '<br />Ceci est un planning de <strong>d&eacute;monstration</strong>. <br />Veuillez vous <strong><a href="./login.php">connecter</a></strong> ou vous <strong><a href="./register.php">inscrire</a></strong> pour voir le planning de <strong>vos s&eacute;ries. </strong><br /><br />';
					
					echo '<div id="planning">';
				
					echo '<table>';
				
				
					$pMonth=date('m', time());
					$pYear=date('Y', time());
					
					$prevMonth;
					$prevYear;
					
					$nextMonth;
					$nextYear;
					
					if(isset($_GET['month'])){
						$pMonth=$_GET['month'];
					}
					if(isset($_GET['year'])){
						$pYear=$_GET['year'];
					}
					
					/*Previous month link*/
					if($pMonth-1<=0){
						$prevMonth=12;
						$prevYear=$myUser->previousYear($pYear);
					}else{
						$prevMonth=$myUser->previousMonth($pMonth);
						$prevYear=$pYear;
					}
					
					
					$prevLink='<a href="./planning.php?month='.$prevMonth.'&year='.$prevYear.'">&lt;</a>';
					
					
					/*Next Month link*/
					if($pMonth+1>=13){
						$nextMonth='01';
						$nextYear=$pYear+1;
					}else{
						$nextMonth=$myUser->nextMonth($pMonth);
						$nextYear=$pYear;
					}
					
					$nextLink='<a href="./planning.php?month='.$nextMonth.'&year='.$nextYear.'">&gt;</a>';
					
					
					
					echo '<tr><th colspan="7" align="center">'.$prevLink.'  '.$myUser->getMonthName($pMonth).' '.$pYear.'  '.$nextLink.'</th></tr>
					       
					       <tr>
					       <th>Lundi</th>
					       <th>Mardi</th>
					       <th>Mercredi</th>
					       <th>Jeudi</th>
					       <th>Vendredi</th>
					       <th>Samedi</th>
					       <th>Dimanche</th>
					       </tr>';
					
					
					
					//Nombre de jour dans le mois*/
					$nbDaysInMonth=date('t', strtotime($pYear.'-'.$pMonth.'-01'));
					$decalage= $myUser->calculDecalage($pMonth,$pYear);
					//echo 'decal='.$decalage;
					
					$t_episodes=$myUser->buildPlanning('1', $pMonth, $pYear);
					
					
					$nbLignes=5;
					$nbTD=0;
					
					$epNo;
					$sNo;
					
					//define nb of rows in table.
					if($nbDaysInMonth+$decalage>35){
						$nbLignes=6;
					}
					
					
					//($decalage+$nbDaysInMonth)
					for($i=1;$i<=$nbLignes;$i++){
						echo '<tr>';
						
						$colorCols=0;
						if(($i%2)==0){
							$colorCols=1;
						}else{
							$colorCols=0;
						}
						
						for($j=1;$j<8;$j++){
							
							
							
							//Write in td.
							$nbTD++;
							
							if(($nbTD-$decalage)==date('j', time()) && $pMonth==date('m', time())){
								echo '<td style="background-color:#CAE6B6">';
							}elseif((($nbTD-$decalage+$colorCols)%2)==0){
								//d day
								echo '<td style="background-color:#F5F4F3">';
							}else{
								echo '<td>';
							}
							
							if($nbTD-$decalage>0 && $nbTD-$decalage<=$nbDaysInMonth){

								echo '<div id="numJour">'.($nbTD-$decalage).'</div>';
								
								if(isset($t_episodes[$nbTD][0])){
									foreach($t_episodes[$nbTD] as $valeur)
									{
										if($valeur['SeasonNumber']!=0){
											echo '<strong><a href="./oneSerie.php?id='.$valeur['serieId'].'" style="color: #666666; ">'.$valeur['SerieName'].'</a></strong>';
											echo '<br />';
											
											echo '<a href="./episode.php?id='.$valeur['idt_episode'].'">';
											$sNo=$valeur['SeasonNumber'];
											if($valeur['SeasonNumber']<10){
												$sNo='0'.$valeur['SeasonNumber'];
											}
											
											$epNo=$valeur['EpisodeNumber'];
											if($valeur['EpisodeNumber']<10){
												$epNo='0'.$valeur['EpisodeNumber'];
											}
											
											echo 'S'.$sNo.'E'.$epNo;
											
											
											echo '</a>';
											
											echo '<br />';
											echo '<br />';
										}
									}
								}
								
							}
							
							echo '</td>';
						}
						
						echo '</tr>';
					}
					echo '</table>';
				}
				
				?>
				
				
			</div>
			
			<div class="clear">&nbsp;</div>
			
		
		<!-- main ends -->
		</div>
		
	
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
