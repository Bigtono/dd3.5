<!--  Bloc Classe DD3.5 --->
<?
  // mise en forme du contenu
  $type_classe='<select id="mp_cla_clt_id" name="mp_cla_clt_id">'.optionList("dd_classe_type", "clt","nom", $dn['cla_clt_id']).'</select>';
  $caracteristique_ls='<select id="mp_cla_car_id" name="mp_cla_car_id">'.optionList("dd_caracteristiques", "car","nom", $dn['cla_car_id']).'</select>';
  $type_magie='<select id="mp_cla_mag_id" name="mp_cla_mag_id">'.optionList("dd_typeMagie", "mag","nom", $dn['cla_mag_id']).'</select>';
  $niveau_max='<select id="mp_cla_niveauMax" name="mp_cla_niveauMax">'.optionListInt(1, 20, $dn['cla_niveauMax']).'</select>';
  $source='<select id="mp_cla_res_id" name="mp_cla_res_id">'.optionList("dd_ressources", "res","nom", $dn['cla_res_id'],"res_id IN ".$selection).'</select>';  
?>

  <div class="ligne mt5">
    <div class="label w100">Type</div><? echo $type_classe; ?>
    <div class="label w100 ml25">Niveaux max</div><? echo $niveau_max; ?>
  </div>

  <div class="label">Description</div><textarea id="mp_cla_description" name="mp_cla_description" class="input_notes"><? echo $dn['cla_description']; ?></textarea>
  <script>CKEDITOR.replace( 'mp_cla_description' );</script>

  <div class="label">Conditions (classe de prestige uniquement)</div><textarea id="mp_cla_conditions" name="mp_cla_conditions" class="input_notes"><? echo $dn['cla_conditions']; ?></textarea>
  <script>
    CKEDITOR.replace('mp_cla_conditions', {
      allowedContent: true, // désactive le filtre de contenu
      // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
      extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
      contentsCss: 'include/_styles_.css'
    });
  </script>

  <div class="label">Armes et armures</div><textarea id="mp_cla_armes" name="mp_cla_armes" class="input_notes"><? echo $dn['cla_armes']; ?></textarea>
  <script>CKEDITOR.replace( 'mp_cla_armes' );</script>

  <div class="label">Sorts</div><textarea id="mp_cla_sorts" name="mp_cla_sorts" class="input_notes"><? echo $dn['cla_sorts']; ?></textarea>
  <script>CKEDITOR.replace( 'mp_cla_sorts' );</script>          

  <div class="dflex mt15">
    <div class="gauche">
      <div class="ligne"><span class="label w200">D&eacute; de vie</span><input type="text" class="input_dv" id="mp_cla_dV" name="mp_cla_dV" value="<? echo stripslashes($dn['cla_dV']); ?>"></div>
      <div class="ligne"><span class="label w200">Points de comp&eacute;tences</span><input type="text" class="input_pointsCompetences" id="mp_cla_pointsCompetences" name="mp_cla_pointsCompetences" value="<? echo stripslashes($dn['cla_pointsCompetences']); ?>"></div>
      <div class="ligne"><span class="label w200">Argent de d&eacute;part</span><input type="text" class="input_argent" id="mp_cla_po_niveau1" name="mp_cla_po_niveau1" value="<? echo stripslashes($dn['cla_po_niveau1']); ?>"></div>
    </div>
    <div class="droite">
      <div class="ligne"><span class="label w200">Type de magie</span><? echo $type_magie; ?></div>
      <div class="ligne"><span class="label w200">Caract&eacute;ristique LS</span><? echo $caracteristique_ls ?></div>
      <div class="ligne"><span class="label w200">Alignement</span><input type="text" class="input_alignement" id="mp_cla_alignement" name="mp_cla_alignement" value="<? echo stripslashes($dn['cla_alignement']); ?>"></div>
    </div>
  </div>
