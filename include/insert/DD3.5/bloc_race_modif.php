<!--- Bloc race Modif DD3.5 --->

<div class="ligne mt5">
    <div class="label w200">Type</div><? echo $type_race; ?>
  </div>  
  <div class="ligne mt5">
    <div class="label w200">Origine</div><input type="text" class="input_nom" id="mp_ra_origine" name="mp_ra_origine" value="<? echo $dn['ra_origine']; ?>">
  </div> 
  <div class="label">Description</div><textarea id="mp_ra_description" name="mp_ra_description" class="input_notes"><? echo $dn['ra_description']; ?></textarea>
  <script>CKEDITOR.replace( 'mp_ra_description' );</script>

  <div class="ligne mt5">
    <div class="label w200">Modif. niveau</div><input type="text" class="input_mod" id="mp_ra_mod_niveau" name="mp_ra_mod_niveau" value="<? echo $dn['ra_mod_niveau']; ?>">
  </div>
  <div class="dflex mt15">
    <div class="gauche">
      <div class="ligne"><span class="label w200">Mod FOR</span><input type="text" class="input_mod" id="mp_ra_modifFor" name="mp_ra_modifFor" value="<? echo stripslashes($dn['ra_modifFor']); ?>"></div>
      <div class="ligne"><span class="label w200">Mod CON</span><input type="text" class="input_mod" id="mp_ra_modifCon" name="mp_ra_modifCon" value="<? echo stripslashes($dn['ra_modifCon']); ?>"></div>
      <div class="ligne"><span class="label w200">Mod DEX</span><input type="text" class="input_mod" id="mp_ra_modifDex" name="mp_ra_modifDex" value="<? echo stripslashes($dn['ra_modifDex']); ?>"></div>              
    </div>
    <div class="droite">
      <div class="ligne"><span class="label w200">Mod INT</span><input type="text" class="input_mod" id="mp_ra_modifInt" name="mp_ra_modifInt" value="<? echo stripslashes($dn['ra_modifInt']); ?>"></div>
      <div class="ligne"><span class="label w200">Mod SAG</span><input type="text" class="input_mod" id="mp_ra_modifSag" name="mp_ra_modifSag" value="<? echo stripslashes($dn['ra_modifSag']); ?>"></div>
      <div class="ligne"><span class="label w200">Mod CHA</span><input type="text" class="input_mod" id="mp_ra_modifCha" name="mp_ra_modifCha" value="<? echo stripslashes($dn['ra_modifCha']); ?>"></div>
    </div>
  </div>