<?php
    include("../class/user.php");
    include("../class/cAPI.php");
    
    $myAPI=new cApi();
    
    $myUser=new cUser();
    
    require_once( './iCalcreator.class.php' );
    
    
    if(!empty($_GET['id'])){
        
        $config = array( 'unique_id' => 'kigkonsult.se' );
        // set Your unique id
        $v = new vcalendar( $config );
        // create a new calendar instance
      
        $v->setProperty( 'method', 'PUBLISH' );
          // required of some calendar software
        $v->setProperty( "x-wr-calname", "Calendar Sample" );
          // required of some calendar software
        $v->setProperty( "X-WR-CALDESC", "TVSP" );
          // required of some calendar software
        $v->setProperty( "X-WR-TIMEZONE", "Europe/Stockholm" );
          // required of some calendar software
        
        /*
         On prend la date courante.
         on construit pour :
            mois, mois-1, mois-2
            mois+1 à mois+6
         donc -2 -1 0 +1 +2 +3 +4 +5 +6 = 9 mois. Donc dans un for <10, avec un switch derrière. 
        */
        
        $cMonth=date('m', time());
	$cYear=date('Y', time());
        $t_episodes;
        
        for($i=0;$i<10;$i++){
            
            $month;
            $year;
            
            switch ($i) {
                case 0:
                    $month=$cMonth;
                    $year=$cYear;
                    $t_episodes=$myUser->buildPlanning($_GET['id'], $cMonth, $cYear);
                    break;
                case 1:
                    //m-1
                case 2:
                    //m-2
                    $month=$cMonth-$i;
                    $year=$cYear;
                    
                    if($month<=0){
                        $month=12+$month;
                    
                        $year--;
                    }
                    
                    if($month<10){
                        $month='0'.$month;
                    }
                    
                    $t_episodes=$myUser->buildPlanning($_GET['id'],$month , $year);                  
                    break;
                
                
                case 3:
                    //m+1
                case 4:
                    //m+2
                case 5:
                    //m+3
                case 6:
                    //m+4
                case 7:
                    //m+5
                case 8:
                    //m+6
                    $month=$cMonth+$i-2;
                    $year=$cYear;
                    
                    if($month>12){
                        $month=-12+$month;
                        $year++;
                    }
                    if($month<10){
                        $month='0'.$month;
                    }
                    
                    $t_episodes=$myUser->buildPlanning($_GET['id'], $month, $year);
                    
                    break;
            }
            
            
            if(!is_null($t_episodes)){
                foreach($t_episodes as $index=>$valeur){
                    $decal = $myUser->calculDecalage($month,$year);
                    $day=$index-$decal; //nécessaire, car la méthode de calcul des planning génère un tableau avec des index basés décalage + day
                        
                    if($day<10){
                        $day='0'.$day;
                    }
                        
                    foreach($valeur as $ep){
                        
                        $vevent = & $v->newComponent( 'vevent' );
                        /*DATE*/
                        $vevent->setProperty( 'dtstart', $year.$month.$day, array('VALUE' => 'DATE'));
                        
                        /*TITLE*/
                        $sNo=$ep['SeasonNumber'];
                        if($ep['SeasonNumber']<10){
                            $sNo='0'.$ep['SeasonNumber'];
                        }
                        $epNo=$ep['EpisodeNumber'];
                        if($ep['EpisodeNumber']<10){
                            $epNo='0'.$ep['EpisodeNumber'];
                        }
                        $vevent->setProperty( 'summary', $ep['SerieName'].' S'.$sNo.'E'.$epNo);
                        $vevent->setProperty( 'description', $ep['EpisodeName'].'\n'.$ep['Overview'] );
                    } 
                }
            }

            
            
        }
        
        /*
        $vevent = & $v->newComponent( 'vevent' );
          // create an event calendar component
        $start = array( 'year'=>2007, 'month'=>4, 'day'=>1, 'hour'=>19, 'min'=>0, 'sec'=>0 );
        $vevent->setProperty( 'dtstart', $start );
        $end = array( 'year'=>2007, 'month'=>4, 'day'=>1, 'hour'=>22, 'min'=>30, 'sec'=>0 );
        $vevent->setProperty( 'dtend', $end );
        $vevent->setProperty( 'LOCATION', 'Central Placa' );
          // property name - case independent
        $vevent->setProperty( 'summary', 'PHP summit' );//titre
        $vevent->setProperty( 'description', 'This is a description' );
        $vevent->setProperty( 'comment', 'This is a comment' );//useless
        $vevent->setProperty( 'attendee', 'attendee1@icaldomain.net' );//usls
        
        
        $vevent = & $v->newComponent( 'vevent' );
          // create next event calendar component
        $vevent->setProperty( 'dtstart', '20070401', array('VALUE' => 'DATE'));//
          // alt. date format, now for an all-day event
        $vevent->setProperty( "organizer" , 'boss@icaldomain.com' );
        $vevent->setProperty( 'summary', 'ALL-DAY event' );//
        $vevent->setProperty( 'description', 'This is a description for an all-day event' );//
        $vevent->setProperty( 'resources', 'COMPUTER PROJECTOR' );
        $vevent->setProperty( 'rrule', array( 'FREQ' => 'WEEKLY', 'count' => 4));
          // weekly, four occasions
        $vevent->parse( 'LOCATION:1CP Conference Room 4350' );
        */
          // supporting parse of strict rfc2445 formatted text
    
          // all calendar components are described in rfc2445
          // a complete iCalcreator function list (ex. setProperty) in iCalcreator manual
    
        $v->returnCalendar();
          // redirect calendar file to browser 
    }
    
    
?>