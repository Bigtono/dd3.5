<?
	// recherche des références de livres sélectionnées
	$requete='SELECT res_id, res_nom, res_editeur, res_selection FROM dd_ressources WHERE res_ruleset_var_id="'.$_SESSION['ruleset'].'" AND res_selection="1" ORDER BY res_nom';
  $result=queryPDO($requete);
  $num_rows=$result->rowCount();
	//$result = getRowSpec($sql);
	$num_rows=$result->rowCount();
	$i=1;
	if ($num_rows > 0):
		//while($sort = mysql_fetch_array ($result)):
    while($sort = $result->fetch(PDO::FETCH_ASSOC)):
			if ($i==1):
				  $selection ='(';
				$selectionAffichage='<strong>R&eacute;f&eacute;rences choisies : </strong>';
			endif;
			$selection .= $sort['res_id'];
			$selectionAffichage.=$sort['res_nom'];
			if ($i<$num_rows):
				$selection.=', ';
				$selectionAffichage.=', ';
				else:
				$selection.=')';
				$selectionAffichage.='.';
			endif;
			$i++;
		endwhile;
	endif;
?>