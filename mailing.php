<?php
    require_once 'lib/class.phpmailer.php';
    include('./includes.php');
    
    $result=mysql_query("SELECT `idt_users`, `mail`, `subD`, `subW`, `subM` FROM `t_users` WHERE 1");
    
    while($rowU = mysql_fetch_array($result)){
        $bMail=false;
        
        $bD=false;
        $bW=false;
        $bM=false;
        
        
        $bDu=$rowU['subD'];
        $bWu=$rowU['subW'];
        $bMu=$rowU['subM'];
        
        //D
        if($bDu=='1'){
            $bD=true;
            $bMail=true;
        }
        
        //W
        if(date('N', time())=='1' && $bWu=='1'){
            $bW=true;
            $bMail=true;
        }
        
        //M
        if(date('j', time())=='1' && $bWu=='1'){
            $bM=true;
            $bMail=true;
        }
        
        
        //If $bmail true, build and send a mail
        if($bMail){
            
            $head='<html><body>
		<div style="margin-left: auto;
                margin-right: auto;
            	width: 970px;	font: 11px/165% \'Lucida Grande\', Verdana, Helvetica, sans-serif;
                color: #666666; 	
                text-align: center;"><img src="./images/hMail.png" alt="TVSP, VOS s&eacute;ries, VOTRE planning ..." /> </div>
                
                <div style="margin-left: auto;
                margin-right: auto;
                width: 970px;
                color: #666666; 	
                text-align: left; font: 11px/165% \'Lucida Grande\',Verdana,Helvetica,sans-serif; ">';
                
            $content='<br />Bonjour '. $rowU['mail'].', <br />';

                
                if($bM){
                    $content.='<br />';
                    
                    //build month in $content
                    $pMonth=date('m', time());
		    $pYear=date('Y', time());
                    
                    
                    $content=$content.'<h2>Votre planning pour '.$myUser->getMonthName($pMonth).' '.$pYear.' : </h2>';
                    
                    $content=$content.'<div style="font: 11px/165% \'Lucida Grande\', Verdana, Helvetica, sans-serif; ">';
		    
		    $content=$content.'<table style="border-collapse: collapse;	margin: 10px; font-size:1em;">';
                    
		    
		    $prevMonth;
		    $prevYear;
		    
		    $nextMonth;
		    $nextYear;
                    
		    $content=$content.'<tr><th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" colspan="7">'.$myUser->getMonthName($pMonth).' '.$pYear.'</th></tr>
		    
			<tr style="background: #fff;">
			<th style="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;">Lundi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Mardi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Mercredi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Jeudi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Vendredi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Samedi</th>
			<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Dimanche</th>
			</tr>';
		    
		    
                    
		    //Nombre de jour dans le mois*/
		    $nbDaysInMonth=date('t', strtotime($pYear.'-'.$pMonth.'-01'));
		    $decalage= $myUser->calculDecalage($pMonth,$pYear);
		    //$content=$content.'decal='.$decalage;
		    
		    $t_episodes=$myUser->buildPlanning($rowU['idt_users'], $pMonth, $pYear);
		    
		    
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
                        $content=$content.'<tr style="background: #fff;">';
						
			for($j=1;$j<8;$j++){
							
							
							
			    //Write in td.
			    $nbTD++;
							
							
			    $content=$content.'<td style="border-color: #EFEFEF; padding: .7em 1em;	width: 150px; text-align: left;	border-width: 1px; border-style: solid; text-align: left; vertical-align: baseline; border-collapse: collapse; ">';
							
							
			    if($nbTD-$decalage>0 && $nbTD-$decalage<=$nbDaysInMonth){

				$content=$content.'<div style="text-align: right; font-weight: bold;">'.($nbTD-$decalage).'</div>';
								
				    if(isset($t_episodes[$nbTD][0])){
					foreach($t_episodes[$nbTD] as $valeur)
					{
					    if($valeur['SeasonNumber']!=0){
						$content=$content.'<strong><a style="color: #666666; ">'.$valeur['SerieName'].'</a></strong>';
						$content=$content.'<br />';
											
						$content=$content.'<a style="color: rgb(174, 133, 92);">';
						$sNo=$valeur['SeasonNumber'];
						if($valeur['SeasonNumber']<10){
						    $sNo='0'.$valeur['SeasonNumber'];
						}
											
						$epNo=$valeur['EpisodeNumber'];
						if($valeur['EpisodeNumber']<10){
						    $epNo='0'.$valeur['EpisodeNumber'];
						}
											
						$content=$content.'S'.$sNo.'E'.$epNo;
											
											
                                                $content=$content.'</a>';
											
						$content=$content.'<br />';
						$content=$content.'<br />';
					    }
					}
				    }
								
				}
							
			    $content=$content.'</td>';
			    }
    			$content=$content.'</tr>';
			}
		    $content=$content.'</table>';
                }
                
                
                
                if($bW){
                    $content.='<br />';
                    
                    
                    //build week in $content
                    $TodayTimeStamp = strtotime(date('Y', time()).'-'.date('m', time()).'-'.date('d', time()));
                    /*
                     Today étant supposé être un lundi, on peut contruire notre tableau en incrémentant le timestamp d'un jour x7 pour avoir nos jours de la semaine.
                     +1 jour = +86400 sec. 
                    */
                    
                    $content=$content.'<h2>Votre planning pour la semaine du '.date('d', time()).' '.$myUser->getMonthName(date('m', time())).' '.date('Y', time()).' : </h2>';
                    
                    $content=$content.'<table style="border-collapse: collapse; margin: 10px; font-size:1em;">
				
				<tr><th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Lundi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Mardi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Mercredi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Jeudi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Vendredi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Samedi</th>
				<th style ="text-align: center; border-width: 1px; border-style: solid; color: #7BA857; background: #EFFAE6; padding: .8em 1em; border-color: #DFF4D5 #D3EFC3 #A7DF8A #D3EFC3;" >Dimanche</th></tr>';
                    
                    
                    
                    $content=$content.'<tr>';
                    
                    for($i=0;$i<7;$i++){
                        $content=$content.'<td style="border-color: #EFEFEF; padding: .7em 1em;	width: 150px; text-align: left;	border-width: 1px; border-style: solid; vertical-align: baseline; border-collapse: collapse; ">
						<div style="text-align: right; font-weight: bold;">'.date('d', $TodayTimeStamp+86400*$i).'</div>';
                                        
                                        $resDay=$myUser->findShowsForADay($rowU['idt_users'], date('d', $TodayTimeStamp+86400*$i), date('m', $TodayTimeStamp+86400*$i), date('Y', $TodayTimeStamp+86400*$i));
                                        if($resDay!=null){
                                            foreach($resDay as $Eps){
                                                $content=$content.'<a style="color: rgb(102, 102, 102);"><strong>'.$Eps['SerieName'].'</strong></a>';
                                                $content=$content.'<br />';
                                                
                                                $content=$content.'<a style="color: rgb(174, 133, 92); ">';
                                                $content=$content.'S'.$Eps['SeasonNumber'];
                                                $content=$content.'E'.$Eps['EpisodeNumber'];
                                                $content=$content.'</a>';
                                                
                                                $content=$content.'<br /><br />';
                                            }
                                        }
                                        
                                        
                                        //Appel fonction pour les épisodes d\'un jour.
                                        //Affichage
                                        
			$content=$content.'</td>';
                    }
                    
                    $content=$content.'</tr>';
                    $content=$content.'</table>';
                    
                }
                
                if($bD){
                    $content.='<br />';
                    //build day in $content
                    
                    $content=$content.'<h2>Votre planning du '.date('d', time()).' '.$myUser->getMonthName(date('m', time())).' '.date('Y', time()).' : </h2>';
                    
                    $TodayTimeStamp = strtotime(date('Y', time()).'-'.date('m', time()).'-'.date('d', time()));
                    $resDay=$myUser->findShowsForADay($rowU['idt_users'], date('d', $TodayTimeStamp), date('m', $TodayTimeStamp), date('Y', $TodayTimeStamp));
                    if($resDay!=null){
                        foreach($resDay as $Eps){
                            $content=$content.'<a style="color: rgb(102, 102, 102);"><strong>'.$Eps['SerieName'].' : </strong></a>';
                            
                            $content=$content.'<a style="color: rgb(174, 133, 92); ">';
                            $content=$content.'S'.$Eps['SeasonNumber'];
                            $content=$content.'E'.$Eps['EpisodeNumber'];
                            $content=$content.'</a>';
                            
                            $content=$content.'<br />';
                        }
                    }else{
                        $content.='Vous n\'avez rien aujourd\'hui';
                    }
                }
                
                
                            
                $footer='</div><div style="margin-left: auto;
                margin-right: auto;
                width: 970px;	
                color: #666666; 
                text-align: center;"><img src="./images/fMail.png" /> </div>
                
                </body>
                </html>';
                
                $body=$head.$content.$footer;
                
                $mail = new PHPMailer(true); //defaults to using php "mail()"; the true param means it will throw exceptions on errors, which we need to catch
                $body = eregi_replace("[\]",'',$body);
                
                /*$mail->Username = 'tvshowplanning@gmail.com';
                $mail->Password = '8UsTAHuw';*/
		$mail->Username = 'postmaster@tvshowplanning.com';
                $mail->Password = 'BitE5Yh3';
		
                $mail->From = 'postmaster@tvshowplanning.com';
                $mail->FromName = 'TV Show Planning';
                $mail->Subject = 'Votre planning du '.date('d',time()).'/'.date('m',time()).'/'.date('y',time());
                $mail->AddAddress($rowU['mail'], $rowU['mail']); //user adress
                
                //$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
                
                $mail->IsHTML(true);
                $mail->MsgHTML($body);
                  
		    
                /*$mail->IsSMTP(); // enable SMTP
                $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true;  // authentication enabled
                $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 465; */
                
		$mail->IsSMTP(); // enable SMTP
                $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true;  // authentication enabled
                $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
                $mail->Host = 'ns0.ovh.net';
                $mail->Port = 587; 
                
                try {
		  /*$mail->AddReplyTo('tvshowplanning@gmail.com', 'Add ReplyTo');
                  $mail->AddAddress($rowU['mail'], $rowU['mail']);
                  $mail->SetFrom('tvshowplanning@gmail.com', 'Set From');
                  $mail->AddReplyTo('tvshowplanning@gmail.com', 'First Last');
                  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
                  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically*/
                  
                  $mail->MsgHTML($body);
                
                  //$mail->Send();
                  echo $body;
                  
                  //echo "Message Sent OK<p></p>\n";
                } catch (phpmailerException $e) {
                  echo $e->errorMessage(); //Pretty error messages from PHPMailer
                } catch (Exception $e) {
                  echo $e->getMessage(); //Boring error messages from anything else!
                }
                
                unset($mail);
            
        }
        
    }
    
?>