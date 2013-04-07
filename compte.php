<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Mon compte</title>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<meta name="author" content="Erwin Aligam - styleshout.com" />
<meta name="description" content="Site Description Here" />
<meta name="keywords" content="keywords, here" />
<meta name="robots" content="index, follow, noarchive" />
<meta name="googlebot" content="noarchive" />

<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />

<script type="text/javascript" src="lib/jquery.js"></script>
<script src="lib/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="css/iphonelikeCb.css" type="text/css" media="screen" charset="utf-8" />

  <script type="text/javascript" charset="utf-8">
    $(window).load(function() {
      $('.on_off :checkbox').iphoneStyle({
        checkedLabel: 'OUI',
        uncheckedLabel: 'NON'
    });
      
      setInterval(function() {
        onchange_checkbox.prop('checked', !onchange_checkbox.is(':checked')).iphoneStyle("refresh");
        return
      }, 2500);
    });
    
    $(window).load(function() {
      $('.fr_en :checkbox').iphoneStyle({
        checkedLabel: 'FR',
        uncheckedLabel: 'EN'
    });
      
      setInterval(function() {
        onchange_checkbox.prop('checked', !onchange_checkbox.is(':checked')).iphoneStyle("refresh");
        return
      }, 2500);
    });
  </script>
  
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
			    <li id="current"><a href="./compte.php">Mon compte</a></li>
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
				
			<h2><a href="index.php">Compte</a></h2>
                        <?php
                    
                            /*
                             Paramètre get de la page : add=idSerie
                                                        del=idSerie
                            */
                            if(isset($_SESSION['logged']) && $_SESSION['logged']==1){
                                /* Traitement du formulaire */
                                if(isset($_POST["submited"])){
                                    /*Password*/
                                    
                                    if(isset($_POST["oldPwd"]) && !empty($_POST["oldPwd"])){
                                        if(isset($_POST["newPwd"]) && !empty($_POST["newPwd"])){
                                            if($myUser->isRightPwd($_SESSION['userID'], $_POST["oldPwd"])){
                                                mysql_query("update t_users set pwd='".md5($_POST["newPwd"])."' where `idt_users`='".$_SESSION['userID']."'");
                                            
                                                echo '<p align="center" style="color:green;"><strong>Votre mot de passe &agrave; &eacute;t&eacute; modifi&eacute; avec succ&egrave;s. </strong></p>';
                                                
                                            }else{
                                                echo '<p align="center" style="color:red;"><strong>L\'ancien mot de passe est inexact. </strong></p>';
                                            }
                                        }else{
                                            echo '<p align="center" style="color:red;"><strong>Veuillez entrer un nouveau mot de passe.  </strong></p>';
                                        }
                                    }elseif(isset($_POST["newPwd"]) && !empty($_POST["newPwd"])){
                                        echo '<p align="center" style="color:red;"><strong>Veuillez entrer votre ancien mot de passe.  </strong></p>';
                                    }
                                    
                                    
                                    
                                    
                                    
                                    /*souscriptions*/
                                    if(isset($_POST["day"])){
                                        mysql_query("update t_users set `subD`='1' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `subD`='0' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                    
                                    if(isset($_POST["week"])){
                                        mysql_query("update t_users set `subW`='1' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `subW`='0' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                    
                                    if(isset($_POST["month"])){
                                        mysql_query("update t_users set `subM`='1' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `subM`='0' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                    
                                    
                                    /*Planning preferences*/
                                    if(isset($_POST["sub"])){
                                        mysql_query("update t_users set `subtitles`='1' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `subtitles`='0' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                    
                                    if(isset($_POST["lan"])){
                                        mysql_query("update t_users set `subLang`='fr' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `subLang`='en' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                    
                                    if(isset($_POST["tpb"])){
                                        mysql_query("update t_users set `tpb`='1' where `idt_users`='".$_SESSION['userID']."'");
                                    }else{
                                        mysql_query("update t_users set `tpb`='0' where `idt_users`='".$_SESSION['userID']."'");
                                    }
                                }
                                
                                
                                /* Affichage du formulaire */ 
                                echo '<form action="compte.php" method="post">';
                                echo '<div style="float: right">
                                    <input type="submit" name="submited" value="Enregistrer"/>
                                    </div>';
                                
                                echo '<h3>Param&egrave;tres du planning : </h3>';
                                echo '<table>';
                                /*echo '<tr><td>Afficher la recherche The Pirate Bay : </td>';
                                
                                if($myUser->userTPB($_SESSION['userID'])){
                                    echo '<td class="on_off"><input type="checkbox" name="tpb" checked="checked"/></td>';
                                }else{
                                    echo '<td class="on_off"><input type="checkbox" name="tpb" /></td>';
                                }*/
                                
                                echo '</tr><tr><td class="on_off">Afficher la recherche de sous-titres : ';
                                if($myUser->userSubtitles($_SESSION['userID'])){
                                    echo '<td class="on_off"><input type="checkbox" name="sub" checked="checked"/></td>';
                                }else{
                                    echo '<td class="on_off"><input type="checkbox" name="sub" /></td>';
                                }
                                
                                echo '</tr><tr><td class="on_off">Langue de la recherche de sous-titres (fran&ccedil;ais ou anglais) : ';
                                if($myUser->userSubFr($_SESSION['userID'])){
                                    echo '<td class="fr_en"><input type="checkbox" name="lan" checked="checked"/></td>';
                                }else{
                                    echo '<td class="fr_en"><input type="checkbox" name="lan" /></td>';
                                }
                                
                                
                                echo '</tr>
                                    </table>';
                                    
                                    
                                
                                
                                echo '<h3>Vos alertes mails : </h3>';
                                echo '<table><tr><td>Abonnement au mail journalier : </td>';
                                
                                if($myUser->userSubD($_SESSION['userID'])){
                                    echo '<td class="on_off"><input type="checkbox" name="day" checked="checked"/></td>';
                                }else{
                                    echo '<td class="on_off"><input type="checkbox" name="day" /></td>';
                                }
                                
                                echo '</tr><tr><td class="on_off">Abonnement au mail hebdomadaire : </td>';
                                
                                
                                if($myUser->userSubW($_SESSION['userID'])){
                                    echo '<td class="on_off"><input type="checkbox" name="week" checked="checked"/></td>';
                                }else{
                                    echo '<td class="on_off"><input type="checkbox" name="week" /></td>';
                                }
                                
                                echo '</tr><tr><td class="on_off">Abonnement au mail mensuel : ';
                                if($myUser->userSubM($_SESSION['userID'])){
                                    echo '<td class="on_off"><input type="checkbox" name="month" checked="checked"/></td>';
                                }else{
                                    echo '<td class="on_off"><input type="checkbox" name="month" /></td>';
                                }
                                
                                echo '</tr>
                                    </table>';
                                

                                echo '<h3>Changer de mot de passe : </h3>';
                                echo '<table><tr><td>Saisissez votre ancien mot de passe : </td><td><input type="password" name="oldPwd"/></td></tr>';
                                echo '<tr><td>Saisissez votre nouveau mot de passe : </td><td><input type="password" name="newPwd"/></td></tr></table>';
                                
                                
                                echo '<div style="float: right; margin: -20px 0px 0px 0px;">
                                    <input type="submit" name="submited" value="Enregistrer"/>
                                    </div>';
                                echo '</form>';
                                    
                            }else{
			    echo 'Veuillez d\'abord vous connecter : ';
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
