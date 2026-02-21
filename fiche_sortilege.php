<?php
require("fpdf/fpdf.php");
include("include/parametres.inc.php");
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

class PDF extends FPDF
{

//En-tête
function Header()
{
	global $nomperso;
	$this->SetXY(5,5);
	//Police Arial gras 15
	$this->SetFont('Arial','B',8);
	//Titre
	$this->Cell(290,5,'Bibliothèque de Tono - Impression des sorts de '.$nomperso,0,0,'L');

}

//Pied de page
function Footer()
{
	//Positionnement
	$this->SetY(-10);
	//Police Arial italique 8
	$this->SetFont('Arial','I',7);
	//Numéro de page
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}	
	
//Tableau 
function contenu($requete)
{
	//Restauration des couleurs et de la police
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	
	// paramétrage
	$xc2 = 45; // X de la 2ème colonne de l'entête du sort
	$mg = 10; // marge gauche
	$mh = 15; // marge haute
	$dmg = 0; // décalement de la marge gauche (incrémenté dans la boucle sort par sort)
	$dmh = 0; // décalement de la marge haute (incrémenté dans la boucle sort par sort)	
	
	//Données
	$result = getRowSpec($requete);
	$num_rows = mysql_num_rows( $result );
	$nbl=0; // nb de sorts dans la ligne
	if ($num_rows > 0):
		while ( $sort = mysql_fetch_array ( $result ) ):
			$nbl++; 
			$lg=66;
			if ($nbl<5):
				$dmg=($lg+1)*($nbl-1);	
				$dmh=0;
				else:
				$dmg=($lg+1)*($nbl-5);	
				$dmh=94;
			endif;
			
			// impression du fond parchemin
			$this->Image('graphisme/font4.jpg',$mg+$dmg, $mh+$dmh,$lg);
			
			//ecriture des informations
			// positionnement initial 
			$this->SetXY($mg+$dmg,$mh+$dmh);
			
			// nom du sort
			$this->SetTextColor(0,0,0);			
			// traitement de césure			
			$lg=strlen($sort[nom_sort]);
			if ($lg<15):
				// le nom ne fait pas plus de 24c
				$this->SetFont('Arial','B',9);
				$this->Cell(62,10,$sort[nom_sort],0,2,'C',0);
				else:
				// le nom fait plus de 24c
				$this->SetFont('Arial','B',8);						
				$pos = strpos($sort[nom_sort], " ");
				if ($pos):
					// présence d'une césure
					$this->Cell(62,1,'',0,2,'C',0);
					if ($pos<($lg/3)):
						// la césure est situé à moins d'un tiers du début du nom
						// recherche d'una autre césure plus proche du milieu du nom
						$pos2 = strpos(substr($sort[nom_sort],$pos), " ");
						if ($pos2):
							//comparaison de l'emplacement des deux césures
							if ((($lg/2)-$pos)<(($lg/2)-$pos2)):
								// la césure n°1 est plus proche du milieu
								$this->Cell(62,4,substr($sort[nom_sort],0,$pos),0,2,'C',0);
								$this->Cell(62,4,substr($sort[nom_sort],$pos+1),0,2,'C',0);
								else:
								// la césure n°2 est plus proche du milieu								
								$this->Cell(62,4,substr($sort[nom_sort],0,$pos2),0,2,'C',0);
								$this->Cell(62,4,substr($sort[nom_sort],$pos2+1),0,2,'C',0);
							endif;
							else:
								$this->Cell(62,4,substr($sort[nom_sort],0,$pos),0,2,'C',0);
								$this->Cell(62,4,substr($sort[nom_sort],$pos+1),0,2,'C',0);							
						endif;
						else:
						$this->Cell(62,4,substr($sort[nom_sort],0,$pos),0,2,'C',0);
						$this->Cell(62,4,substr($sort[nom_sort],$pos+1),0,2,'C',0);	
					endif;
					else:
					// ne contient pas d'espace
					$this->SetFont('Arial','B',9);
					$this->Cell(62,13,$sort[nom_sort],0,2,'C',0);
				endif;				
			endif;
			
			// niveau	 du sort pour la classe de personnage du Lanceur de sort
			// positionnement initial 
			$this->SetXY($mg+$dmg+60,$mh+$dmh+3);
			$this->SetFont('Arial','B',12);
			$this->Cell(4,5,$sort['ls'],0,2,'C',0);
			
			// college et branche
			$this->SetXY($mg+$dmg+4,$mh+$dmh+10); // repositionnement
			$this->SetTextColor(0,0,255);			
			$this->SetFont('Arial','B',7);
			$college_branche = $sort[college_sort];
			if ($sort[branche_sort] != "") $college_branche .= " ($sort[branche_sort])";
			$this->Cell(58,5,$college_branche,0,2,'C',0);
			
			//entete du sort
			$this->SetTextColor(0,0,0);			
			//ligne 1
			$this->SetXY($mg+$dmg+5,$mh+$dmh+15); // repositionnement			
			// catégorie de lanceur de sorts
			$lanceur="Lanceur: ";
			$cpt=0;
			if (is_numeric($sort[pretre])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "P".$sort[pretre];
				$cpt++;
			endif;
			if (is_numeric($sort[mage])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "M".$sort[mage];
				$cpt++;
			endif;
			if (is_numeric($sort[paladin])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "Pa".$sort[palaidn];
				$cpt++;
			endif;
			if (is_numeric($sort[rodeur])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "R".$sort[rodeur];
				$cpt++;
			endif;
			if (is_numeric($sort[barde])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "B".$sort[barde];
				$cpt++;
			endif;
			if (is_numeric($sort[druide])):
				if ($cpt!=0) $lanceur .= ",";
				$lanceur .= "D".$sort[druide];
				$cpt++;
			endif;
			$this->Cell($xc2-2,5,$lanceur,0,0,'L',0); 
			// composante
			$this->SetX($mg+$dmg+$xc2); // repositionnement			
			$composante="";
			$cpt=0;
			if ($sort[vocal_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "V"; 
				$cpt++;
			endif;
			if ($sort[gestuel_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "G"; 
				$cpt++;				
			endif;
			if ($sort[materiel_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "M"; 
				$cpt++;				
			endif;
			if ($sort[focalisateur]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "F"; 
				$cpt++;				
			endif;
			if ($sort[focalisateur_divin]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "FD"; 
				$cpt++;				
			endif;
			$this->Cell(62-$xc2,5,$composante,0,2,'L',0);
			
			//ligne 2
			$this->SetXY($mg+$dmg+5,$mh+$dmh+15+3); // repositionnement			
			// portée
			$this->Cell($xc2-2,5,"Portée: ".$sort[portee_sort],0,0,'L',0);
			// durée de l'incantation
			$this->SetX($mg+$dmg+$xc2); // repositionnement			
			$this->Cell(62-$xc2,5,$sort[duree_incantation],0,2,'L',0);
			
			//ligne 3
			$this->SetXY($mg+$dmg+5,$mh+$dmh+15+6); // repositionnement			
		  // Cible
			$this->Cell($xc2-2,5,"Cible: ".$sort[cible_sort],0,0,'L',0);
			// RM
			$this->SetX($mg+$dmg+$xc2); // repositionnement
			$rm="non";
			if ($sort[resistance_sort]==1) $rm="oui";			
			$this->Cell(62-$xc2,5,"RM : ".$rm,0,2,'L',0);
			
			//ligne 4
			$this->SetXY($mg+$dmg+5,$mh+$dmh+15+9); // repositionnement			
		  // Durée
			$this->Cell($xc2-2,5,"Durée: ".$sort[duree_sort],0,0,'L',0);
			// JS
			$this->SetX($mg+$dmg+$xc2); // repositionnement
			
			$this->Cell(62-$xc2,5,"JS: ".$sort[jet_sauv_sort],0,2,'L',0);
			// fin de l'entête

			// Effet du sort
			$this->SetFont('Arial','',7);			
			$this->SetXY($mg+$dmg+5,$mh+$dmh+29); // repositionnement			
			$this->MultiCell(58,3,"Effet: ".$sort[description_courte],0,2,'L',0);

			// description du sort
			$this->SetFont('Arial','',7);			
			$this->SetXY($mg+$dmg+5,$mh+$dmh+35); // repositionnement			
			$this->MultiCell(58,3,$sort[text_sort],0,2,'L',0);
			
			// Source et page du sort
			$this->SetFont('Arial','',7);			
			$this->SetXY($mg+$dmg+5,$mh+$dmh+83); // repositionnement initial, remplacé par repositionnement à la place des composantes matérielles
			$this->Cell(58,3,trim($sort[titre_livre])." p".trim($sort[page_sort]),0,2,'C',0);
			
			/* Composantes matérielles du sort
			$this->SetXY($mg+$dmg+5,$mh+$dmh+83); // repositionnement
			$this->SetFont('Arial','B',7);			
			$this->Cell(58,2,"Composante(s) matérielle(s)",0,2,'C',0);
			$this->SetFont('Arial','',7);
			// traitement de césure			
			$lg=strlen($sort[composante_sort]);
			$ok=0;			
			$pos=0;			
			if ($lg<24):  // le nom ne fait pas plus de 24c
				$ok=0;
				else: // le nom fait plus de 24c
				for ($i=0;$i<$lg;$i++):
					$car=substr($sort[composante_sort],$i,1);
					if ($car==" "):
						$ok=1;
						if (($i>$pos) && ($i<=($lg/2))):
							$pos=$i;
						endif;
					endif;
				endfor;
			endif;
			if ($ok==1):
				$this->Cell(58,3,substr($sort[composante_sort],0,$pos),0,2,'C',0);
				$this->Cell(58,2,substr($sort[composante_sort],$pos+1),0,2,'C',0);
				else:
				$this->Cell(58,1,'',0,2,'C',0);
				$this->Cell(58,2,$sort[composante_sort],0,2,'C',0);
			endif;
			*/
			
		endwhile;
	endif;
}

function sommaire($a,$b,$c)
{
	//Restauration des couleurs et de la police
	if ($a==1) $this->SetXY(15,15);
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	$this->Cell(62,3,"page ".$a." : sorts ".$b." à ".($b+$c),0,2,'L',0);
	$this->Ln();	
}

function nosort($count)
{
	$this->SetXY(10,10);
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	$this->Cell(100,20,"Aucun sort ($count)",0,2,'L',0);
	$this->Ln();	

}

}

// ************************************************************************************************************
// création de la page
// ************************************************************************************************************
$pdf=new PDF('L','mm','A4');
$pdf->Open();
$pdf->SetAutoPageBreak(TRUE,10);
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',10);

$erreur="";

if (isset($_GET['v'])) $v=$_GET['v'];
if (isset($_GET['c'])) $c=$_GET['c'];
if (isset($_GET['n'])) $n=$_GET['n'];
if (isset($_GET['idperso'])) :
	$idperso=$_GET['idperso']; // id du personage
	$nomperso=writeLibelle( "personnages", "id_perso", "nom_perso",$idperso);
endif;
if (isset($_GET['classe'])): // classe de LS
	$classe=$_GET['classe']; 
	//if ($classe=="mage") $classe="mago";
endif;

// détermination du contenu
// détermination du nombre d'item à imprimer
switch($_GET['v']):
	case "F": // toute la bibliothèque
		$SQL = "SELECT * FROM listesort ORDER BY nom_sort";
		break;
	case "C": // une sélection précise de sorts
		$SQL = "SELECT * FROM caddie_impression WHERE id_perso='".$idperso."' ORDER BY nom_sort";
		break;
	case "L": // une classe de personnage
		$SQL = "SELECT * FROM listesort WHERE $c=\"$n\" ORDER BY nom_sort";
		break;	
	default:
		$erreur="argument v erroné : $v";
endswitch;
if ($erreur==""): 
	$result = getRowSpec($SQL);
	$num_rows = mysql_num_rows( $result ); // nb d'item à imprimer
	if ($num_rows > 0): // cas où il y a au moins un sort à imprimer
		$nbs=0;
		$nbp=0;	
		while ($nbs<=$num_rows-1):
			$pdf->AddPage();
			$nbp++;
			// début de la création de la requete qui va remonter les données à imprimer
			switch($v):
				case "F":
					$requete = "SELECT listesort.*,".$classe." as ls, titre_livre FROM listesort, ressources WHERE source_sort=id_livre ORDER BY ls, nom_sort";
					break;
				case "C":
					$requete = "SELECT listesort.*,".$classe." as ls, titre_livre FROM listesort, ressources, caddie_impression";
					$requete .= " WHERE listesort.id_sort=caddie_impression.id_sort AND source_sort=id_livre AND id_perso=\"$idperso\" ORDER BY ls, nom_sort";
					break;
				case "L":
					$requete = "SELECT listesort.*,".$c." as ls, titre_livre FROM listesort, ressources WHERE source_sort=id_livre AND $c=\"$n\" ORDER BY nom_sort";
					break;	
				default:
			endswitch;
			if (($nbs+7)<=$num_rows):		
				$requete .= " LIMIT $nbs, 8"; // création paramètres requete qui remontera les 8 prochains sorts à imprimer
				$pdf->contenu($requete);
				else:
				$requete .= " LIMIT $nbs, ".($num_rows-$nbs); // création paramètres requete qui remontera les derniers sorts à imprimer
				$pdf->contenu($requete);
			endif;
			$nbs+=8;
		endwhile;
		else: // cas où il n'y a rien du tout à imprimer
		$pdf->AddPage();
		$pdf->nosort($num_rows);
	endif;
	// impression
	$pdf->Output();
endif;
echo $erreur;
?>
