<?php include('./includes.php');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>

<title>Tvsp - Inscription</title>

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
		
                    <h2>Cr&eacute;ation d'un nouveau compte : </h2>
                    
                    <?php
                    function redirect($url, $time=3)
                    {     
                       //On vérifie si aucun en-tête n'a déjà été envoyé    
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
                    
                    // On met les variables utilis&eacute; dans le code PHP à FALSE (C'est-à-dire les d&eacute;sactiver pour le moment).
                    $error = FALSE;
                    $registerOK = FALSE;
                    
                        // On regarde si l'utilisateur est bien pass&eacute; par le module d'inscription
                        if(isset($_POST["register"])){
                           
                            // On regarde si tout les champs sont remplis, sinon, on affiche un message à l'utilisateur.
                            if($_POST["login"] == NULL OR $_POST["pass"] == NULL OR $_POST["pass2"] == NULL){
                               
                                // On met la variable $error à TRUE pour que par la suite le navigateur sache qu'il y'a une erreur à afficher.
                                $error = TRUE;
                               
                                // On &eacute;crit le message à afficher :
                                $errorMSG = "Tout les champs doivent &ecirc;tre remplis !";
                                   
                            }
                           
                           if(!ereg("^[A-Za-z0-9\.|-|_]*[@]{1}[A-Za-z0-9\.|-|_]*[.]{1}[a-z]{2,5}$", $_POST["login"])){
                                $error = TRUE;
                                
                                $errorMSG = "Veuillez rentrer une adresse email. <br/>";
                            }
                           
                            // Sinon, si les deux mots de passes correspondent :
                            elseif($_POST["pass"] == $_POST["pass2"]){
                               
                                // On regarde si le mot de passe et le nom de compte n'est pas le m&ecirc;me
                                if($_POST["login"] != $_POST["pass"]){
                                   
                                    // Si c'est bon on regarde dans la base de donn&eacute;e si le nom de compte est d&eacute;jà utilis&eacute; :
                                    $sql = "SELECT mail FROM t_users WHERE mail = '".$_POST["login"]."' ";
                                    $sql = mysql_query($sql);
                                // On compte combien de valeur à pour nom de compte celui tap&eacute; par l'utilisateur.
                                $sql = mysql_num_rows($sql);
                               
                                  // Si $sql est &eacute;gal à 0 (c'est-à-dire qu'il n'y a pas de nom de compte avec la valeur tap&eacute; par l'utilisateur
                                  if($sql == 0){
                                 
                                      // Si tout va bien on regarde si le mot de passe n'exède pas 60 caractères.
                                      if(strlen($_POST["pass"]) < 60){
                                     
                                        // Si tout va bien on regarde si le nom de compte n'exède pas 60 caractères.
                                        if(strlen($_POST["login"]) < 60){
                                        
                                            // Si le nom de compte et le mot de passe sont diff&eacute;rent :
                                            if($_POST["login"] != $_POST["pass"]){
                                       
                                              // Si tout ce passe correctement, on peut maintenant l'inscrire dans la base de donn&eacute;es :
                                              $sql = "INSERT INTO t_users (mail,pwd) VALUES ('".$_POST["login"]."','".md5($_POST["pass"])."')";
                                              $sql = mysql_query($sql);
                                             
                                              // Si la requ&ecirc;te s'est bien effectu&eacute; :
                                              if($sql){
                                             
                                                  // On met la variable $registerOK à TRUE pour que l'inscription soit finalis&eacute;
                                                  $registerOK = TRUE;
                                                  // On l'affiche un message pour le dire que l'inscription c'est bien d&eacute;roul&eacute; :
                                                  $registerMSG = "Inscription r&eacute;ussie ! Vous &ecirc;tes maintenant membre du site.";
                                                  
                                                  // On le met des variables de session pour stocker le nom de compte et le mot de passe :
                                                  $_SESSION['logged']=1;
                                                  $_SESSION['userID']=$myUser->getUserID($_POST['login']);
                                                  
                                                  
                                              }
                                             
                                              // Sinon on l'affiche un message d'erreur (g&eacute;n&eacute;ralement pour vous quand vous testez vos scripts PHP)
                                              else{
                                             
                                                  $error = TRUE;
                                                 
                                                  $errorMSG = "Erreur dans la requ&ecirc;te SQL<br/>".$sql."<br/>";
                                             
                                              }
                                              
                                              
                                           
                                            }
                                           
                                            // Sinon on fais savoir à l'utilisateur qu'il a mis un nom de compte trop long.
                                            else{
                                           
                                              $error = TRUE;
                                             
                                              $errorMSG = "Votre nom compte ne doit pas d&eacute;passer <strong>60 caract&eagrav;res</strong> !";
                                             
                                              $login = NULL;
                                             
                                              $pass = $_POST["pass"];
                                           
                                            }
                                       
                                        }
                                     
                                      }
                                     
                                      // Si le mot de passe d&eacute;passe 60 caractères on le fait savoir
                                      else{
                                     
                                        $error = TRUE;
                                        
                                        $errorMSG = "Votre mot de passe ne doit pas d&eacute;passer <strong>60 caractères</strong> !";
                                       
                                        $login = $_POST["login"];
                                       
                                        $pass = NULL;
                                     
                                      }
                                 
                                  }
                                 
                                  // Sinon on affiche un message d'erreur lui disant que ce nom de compte est d&eacute;jà utilis&eacute;.
                                  else{
                                 
                                      $error = TRUE;
                                     
                                      $errorMSG = "Le nom de compte <strong>".$_POST["login"]."</strong> est d&eacute;jà utilis&eacute; !";
                                     
                                      $login = NULL;
                                     
                                      $pass = $_POST["pass"];
                                 
                                  }
                                }
                               
                                // Sinon on fais savoir à l'utilisateur qu'il doit changer le mot de passe ou le nom de compte
                                else{
                                   
                                    $error = TRUE;
                                   
                                    $errorMSG = "Le nom de compte et le mot de passe doivent &ecirc;tres diff&eacute;rents !";
                                   
                                }
                               
                            }
                         
                          // Sinon si les deux mots de passes sont diff&eacute;rents :     
                          elseif($_POST["pass"] != $_POST["pass2"]){
                         
                            $error = TRUE;
                           
                            $errorMSG = "Les deux mots de passes sont diff&eacute;rents !";
                           
                            $login = $_POST["login"];
                           
                            $pass = NULL;
                         
                          }
                         
                          // Sinon si le nom de compte et le mot de passe ont la m&ecirc;me valeur :
                          elseif($_POST["login"] == $_POST["pass"]){
                         
                            $error = TRUE;
                           
                            $errorMSG = "Le nom de compte et le mot de passe doivent &ecirc;tre diff&eacute;rents !";
                         
                          }
                           
                        }
                    
                    ?>
                    
                    <?php // On affiche les erreurs :
                      if($error == TRUE){ echo '<p align="center" style="color:red;">'.$errorMSG.'</p>'; }
                    ?>
                    <?php // Si l'inscription s'est bien d&eacute;roul&eacute;e on affiche le succès :
                      if($registerOK == TRUE){
                        echo '<p align="center" style="color:green;"><strong>'.$registerMSG.'</strong></p>';
                        echo '<p align="center"><strong>Redirection dans 5 secondes ...</strong></p>';
                        redirect("./series.php","5");
                      }
                      
                    ?>
                    
                    <form action="register.php" method="post">
                   
                        <table>
                       
                        <tr>
                       
                        <td><label for="login"><strong>Votre adresse email :</strong></label></td>
                        <td><input type="text" name="login" id="login"/></td>
                       
                        </tr>
                       
                        <tr>
                       
                        <td><label for="pass"><strong>Mot de passe :</strong></label></td>
                        <td><input type="password" name="pass" id="pass"/></td>
                       
                        </tr>
                       
                        <tr>
                       
                        <td><label for="pass2"><strong>Confirmez le mot de passe :</strong></label></td>
                        <td><input type="password" name="pass2" id="pass2"/></td>
                       
                        </table>
                   
                    <input type="submit" name="register" value="S'inscrire"/>
                   
                    </form>
                    
                    
		
		</div>
	
	<!-- contents end here -->	
	</div></div>

	<?php include('./footer.php');?>

</body>
</html>
