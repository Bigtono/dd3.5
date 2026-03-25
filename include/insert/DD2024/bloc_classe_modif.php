<!--  Bloc Classe DD2024 --->
<?
  // mise en forme du contenu
  $type_classe='<select id="mp_cla_clt_id" name="mp_cla_clt_id">'.optionList("dd_classe_type", "clt","nom", $dn['cla_clt_id']).'</select>';
?>

<div class="label_long">Caract&eacute;ristique(s) principale(s)</div><textarea id="mp_cla_caracteristiques" name="mp_cla_caracteristiques" class="input_notes"><? echo $dn['cla_caracteristiques']; ?></textarea>
<div class="ligne"><div class="label">D&eacute; de vie</div><input type="text" class="input_dv" id="mp_cla_dV" name="mp_cla_dV" value="<? echo stripslashes($dn['cla_dV']); ?>"></div>
<div class="label_long">Maitrise des jets de sauvegarde</div><textarea id="mp_cla_sauvegardes" name="mp_cla_sauvegardes" class="input_notes"><? echo $dn['cla_sauvegardes']; ?></textarea>
<div class="label_long">Maitrise de comp&eacute;tence</div><textarea id="mp_cla_competences" name="mp_cla_competences" class="input_notes"><? echo $dn['cla_competences']; ?></textarea>
<div class="label_long">Maitrise d'arme</div><textarea id="mp_cla_armes" name="mp_cla_armes" class="input_notes"><? echo $dn['cla_armes']; ?></textarea>
<div class="label_long">Formation aux armures</div><textarea id="mp_cla_armures" name="mp_cla_armures" class="input_notes"><? echo $dn['cla_armures']; ?></textarea>
<div class="label_long">Maitrise d'outils</div><textarea id="mp_cla_outils" name="mp_cla_outils" class="input_notes"><? echo $dn['cla_outils']; ?></textarea>
<div class="label_long">&Eacute;quipement de d&eacute;part</div><textarea id="mp_cla_equipement" name="mp_cla_equipement" class="input_notes"><? echo $dn['cla_equipement']; ?></textarea>

<div class="gras mt10">Donn&eacute;es techniques</div>
<div class="ligne"><div class="label200">Intitul&eacute; Pouvoir 1</div><input type="text" class="input_pouvoir" id="mp_cla_pouvoir1" name="mp_cla_pouvoir1" value="<? echo stripslashes($dn['cla_pouvoir1']); ?>"></div>
<div class="ligne"><div class="label200">Intitul&eacute; Pouvoir 2</div><input type="text" class="input_pouvoir" id="mp_cla_pouvoir2" name="mp_cla_pouvoir2" value="<? echo stripslashes($dn['cla_pouvoir2']); ?>"></div>
<div class="ligne"><div class="label200">Intitul&eacute; Pouvoir 3</div><input type="text" class="input_pouvoir" id="mp_cla_pouvoir3" name="mp_cla_pouvoir3" value="<? echo stripslashes($dn['cla_pouvoir3']); ?>"></div>
<div class="ligne"><div class="label200">Intitul&eacute; Pouvoir 4</div><input type="text" class="input_pouvoir" id="mp_cla_pouvoir4" name="mp_cla_pouvoir4" value="<? echo stripslashes($dn['cla_pouvoir4']); ?>"></div>

<? if (isset($c) && (int)$c > 0): ?>
  <div class="nodata mt5">Enregistrer d'abord ces intitul&eacute;s pour les voir appara&icirc;tre en section 2.</div>
<? endif; ?>

<div class="label_long">Description</div><textarea id="mp_cla_description" name="mp_cla_description" class="input_notes"><? echo $dn['cla_description']; ?></textarea>
<script>CKEDITOR.replace('mp_cla_description');</script>
