<?
 function writeMonthOptions( $d )
{
$d_array = getDate( $d );
$months = array( "Jan","Feb","Mar","Apr","May","Jun",
  "Jul","Aug","Sep","Oct","Nov","Dec" );
foreach ( $months as $key=>$value )
    {
    print "<OPTION VALUE=\"".($key+1)."\"";
    print ( ( $d_array[mon] == ($key+1) )?"SELECTED":"" );
print ">$value\n";
    }
}

 function writeDayOptions( $d )
{
$d_array = getDate( $d );
for ( $x = 1; $x<=31; $x++ )
    {
    print "<OPTION VALUE=\"$x\"";
    print ( ( $d_array[mday] == $x )?"SELECTED":"" );
    print ">$x\n";
    }
}

function writeYearOptions( $d )
{
$d_array = getDate( $d );
$now_array = getDate(time());
for ( $x = $now_array[year]; $x <= ($now_array[year]+5); $x++ )
    {
    print "<OPTION VALUE=\"$x\"";
    print ( ( $d_array[year] == $x )?"SELECTED":"" );
 print ">$x\n";
    }
}

 function writeHourOptions( $d )
{
$d_array = getDate( $d );
for ( $x = 0; $x< 24; $x++ )
    {
    print "<OPTION VALUE=\"$x\"";
    print ( ( $d_array[hours] == $x )?"SELECTED":"" );
    print ">".sprintf("%'02d",$x)."\n";
    }
}

 function writeMinuteOptions( $d )
{
$d_array = getDate( $d );
for ( $x = 0; $x<= 59; $x++ )
    {
    print "<OPTION VALUE=\"$x\"";
    print ( ( $d_array[minutes] == $x )?"SELECTED":"" );
    print ">".sprintf("%'02d",$x)."\n";
    }
}

function writeToday( $cdate )
{
$jour = array("Dimanche", "Lundi", "Mardi","Mercredi", "Jeudi", "Vendredi", "Samedi"); 
$mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"); 

$now = getdate( $cdate );
$numJourSemaine = $now[wday];
$num_mois = $now[mon];
$ann = $now[year];
return $jour[$numJourSemaine]." ".$now[mday]." ".$mois[$num_mois-1]." ".$ann; 
}

function writeDate( $cdate )
{
$jour = array("Dimanche", "Lundi", "Mardi","Mercredi", "Jeudi", "Vendredi", "Samedi"); 
$mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"); 

list($a,$m,$j) = explode("-",$cdate);
return $j."-".$m."-".$a; 
}

function writeDateLongue( $cdate )
{
$jour = array("Dimanche", "Lundi", "Mardi","Mercredi", "Jeudi", "Vendredi", "Samedi"); 
$mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"); 

list($a,$m,$j) = explode("-",$cdate);
$libjournee = $jour[$j];
$libmois = $mois[$m];
return $libjour." ".$j." ".$libmois." ".$a; 
}

?>
