<?php

    //Pour reset quand il n'y a pas le lien de delog. 
    //$_SESSION['logged']=0;
    
    if(!empty($_POST['mail']) && !empty($_POST['pwd']) ){
        
        /* reminder : 
         1 if ok
	 0 if bad credentials
        */
        $logged=$myUser->login($_POST['mail'],$_POST['pwd']);
        
        if($logged==1){
            $_SESSION['logged']=1;
            
            //requète pour récup' l'id user.
            
            $_SESSION['userID']=$myUser->getUserID($_POST['mail']);
            
            
            echo 'Vous &ecirc;tes maintenant connect&eacute;. <br />';
            
        }else{
            $_SESSION['logged']=0;
        }
        
    }
    
    if(!isset($_SESSION['logged']) || $_SESSION['logged']==0){
        echo '  <div id="logbox">

                <h3>Connexion</h3>		
            
                <form method="post" action="./index.php">
                    <h4>E-mail : </h4>
                    <input type="text" name="mail"/>
                    
                    <h4>Mot de passe : </h4>
                    <input  type="password" name="pwd"/>
                    
                    <input type="submit" value="Se connecter" />
                </form>
                
                <div style="float: right;"><a href="./register.php">Cr&eacute;er un compte</a></div>
                <br />
                </div>';
        
    }else{
        echo '<a href="./logout.php">Se d&eacute;connecter. </a>';
    }

?>