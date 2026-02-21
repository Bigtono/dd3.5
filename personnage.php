<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
if (isset($_GET['personnage'])):
  $p=$_GET['personnage'];
  $_SESSION['perso']=$p;
  $requete = "SELECT * FROM dd_personnages WHERE pe_id='".$p."'";
  $resultat = queryPDO($requete);
  $dn=$resultat->fetch(PDO::FETCH_ASSOC);
  $nls=nls($p);
  else:
  $p="";
  $_SESSION['perso']="";
endif;
// initialisation des onglets
$classe_fiche="contenuMainV";
$classe_objets_magiques="contenuMain";
$classe_background="contenuMain";
$classe_grimoire="contenuMain";
$classe_notes="contenuMain";
$classe_mj="contenuMain";
if (!empty($_GET['onglet']) && $_GET['onglet']!="fiche"):
  $classe_fiche="contenuMain";
  $onglet="classe_".$_GET['onglet'];
  $$onglet="contenuMainV";
endif;
?>
<!doctype html>
<html>
<HEAD>
	<? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>
  
  
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>  
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">
        <? echo stripslashes($dn['pe_nom']); ?>
        <a href="personnage-modifier.php?personnage=<? echo $p; ?>&tri=<? echo $_GET['tri']; ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>
      </div>
      <div></div>
    </div>  
    
    <div id="personnage">
      <div class="menu_main contenu">
        <div id="menu_fiche" class="btMain" onClick="afficherContenu('fiche')">
          <span id="logo_fiche" class="logo_menu"><i class="fa-solid fa-person"></i></span>
          <span class="titre_menu">Fiche</span></span>
        </div>        
        <div id="menu_background" class="btMain" onClick="afficherContenu('background')">
          <span id="logo_background" class="logo_menu"><i class="fa-solid fa-feather"></i></span>
          <span class="titre_menu">Background</span>
        </div>
        <div id="menu_om" class="btMain" onClick="afficherContenu('objets-magiques')">
          <span id="logo_om" class="logo_menu"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
          <span class="titre_menu">Objets magiques</span>
        </div>
        <div id="menu_grimoire" class="btMain" onClick="afficherContenu('grimoire')">
          <span id="logo_grimoire" class="logo_menu"><i class="fa-solid fa-hat-wizard"></i></span>
          <span class="titre_menu">Magie</span>
        </div>
        <div id="menu_notes" class="btMain" onClick="afficherContenu('notes')">
          <span id="logo_notes" class="logo_menu"><i class="fa-solid fa-note-sticky"></i></span>
          <span class="titre_menu">Notes</span>
        </div>
        <? if ($_SESSION['mj']==1): ?>
        <div id="menu_mj" class="btMain" onClick="afficherContenu('mj')">
          <span id="logo_notes" class="logo_menu"><i class="fa-solid fa-note-sticky"></i></span>
          <span class="titre_menu">MJ</span>
        </div>
        <? endif; ?>
      </div> <!-- menu_main --->            
      <?
      /*
      switch ($_GET['msg']):
        case 1:
          echo '<div class="contenu"><div class="confirmation">Personnage mis &agrave; jour</div></div>';
          break;
        case 4:
          echo '<div class="contenu"><div class="confirmation">Grimoire mis &agrave; jour</div></div>';
          break;
        default:
      endswitch;
      */
      // préparation des données
      if ($dn['pe_arc_id']>0):
        $archetype=' ('.libelle("dd_races", "ra", "nom", $dn['pe_arc_id'], "ra_rat_id=2").')';
        else:
        $archetype='';
      endif;
      if (strlen($dn['pe_notes'])>0):
        $notes=stripslashes($dn['pe_notes']);
        else:
        $notes='Aucune note';
      endif;
      if (strlen($dn['pe_notes_mj'])>0):
        $notes_mj=stripslashes($dn['pe_notes_mj']);
        else:
        $notes_mj='Aucune note';
      endif;
      ?>
      <!--- Fiche ---------------------------------------------------------------------------------------------------------------------------->
      <div id="fiche" class="<? echo $classe_fiche; ?>">
        <div class="contenu">
          <div class="titreAction">
            <div class="titreA">Fiche</div>
          </div>        
          <div>
            <div class="mb10">
              <? echo libelle("dd_races", "ra", "nom", $dn['pe_ra_id'], "ra_rat_id=1").$archetype; ?>, Niveau <? echo niveauPersonnage($p) .' ('.classesPersonnage($p).')'; ?>, <? echo libelle("dd_alignements", "al", "abreviation", $dn['pe_al_id']); ?>, NLS : <? echo $nls; ?>
              <? 
              $organisation=libelle("dd_organisations", "org", "nom", $dn['pe_org_id']);
              if ($organisation!='') echo ', '.$organisation;
              if ($_SESSION['mj']==1): 
                echo '<br><span class="label">Joueur : </span>'.libelle_joueur($dn['pe_j_id']);
              endif;
              ?>
            </div>           
            <div>
              <div class="titre">Caract&eacute;ristiques</div>
              <div class="cellMainSort">
                <div>
                  <div class="cellEntete">For</div>
                  <div class="cellValue"><? echo $dn['pe_for']; ?></div>
                </div>
                <div>
                  <div class="cellEntete">Dex</div>
                  <div class="cellValue"><? echo $dn['pe_dex']; ?></div>
                </div>
                <div>
                  <div class="cellEntete">Con</div>
                  <div class="cellValue"><? echo $dn['pe_con']; ?></div>
                </div>
                <div>
                  <div class="cellEntete">Int</div>
                  <div class="cellValue"><? echo $dn['pe_int']; ?></div>
                </div>
                <div>
                  <div class="cellEntete">Sag</div>
                  <div class="cellValue"><? echo $dn['pe_sag']; ?></div>
                </div>
                <div>
                  <div class="cellEntete">Cha</div>
                  <div class="cellValue"><? echo $dn['pe_cha']; ?></div>
                </div>   
              </div>
            </div>  
          </div>
        </div>
      </div> <!-- #Fiche -->
    
      <!--- Background ----------------------------------------------------------------------------------------------------------------------->
      <div id="background" class="<? echo $classe_background; ?>">
        <div class="contenu">
          <div class="titreAction">
            <div class="titreA">Background</div>
          </div>        
          <div>
            <? echo stripslashes($dn['pe_background']); ?>
          </div>
        </div>
      </div> <!-- #background -->

      <!--- Objets magiques ------------------------------------------------------------------------------------------------------------------>
      <div id="objets-magiques" class="<? echo $classe_objets_magiques; ?>">
        <div class="contenu">
          <div class="titreAction">
            <div class="titreA">Objets magiques
            <? if ($isAdmin): ?>
              <a class="lien_cbt" onClick="gererEquipement('<? echo $p; ?>')"><i class="fa-solid fa-pen-to-square ml15"></i></a>    
            <? endif; ?>
            </div>
            <div></div>
          </div>
          <div id="listeOM">
            <?
            include('include/insert/'.$_SESSION['rulesetRep'].'/listeEqt.php');
            echo $listeEqt;
            ?>  
          </div>
        </div>
      </div> <!-- #objets-magiques -->

      <!--- Grimoires ------------------------------------------------------------------------------------------------------------------------>
      <?
      // recherche du grimoire par defaut
      $requete='SELECT gr_id FROM dd_grimoires WHERE gr_pe_id="'.$p.'" AND gr_defaut="1"';
      $result_grd=queryPDO($requete);
      $num_rows_grd=$result_grd->rowCount();
      if ($num_rows_grd>0):
        $dngrd=$result_grd->fetch(PDO::FETCH_ASSOC);
        $grimoire=$dngrd['gr_id'];
        else:
        $grimoire=0;
      endif;
      ?>
      <div id="grimoire" class="<? echo $classe_grimoire; ?>">
        <div class="contenu">
          <div class="titreAction">
            <div class="titreA">
              Magie
              <span class="ml15"><a class="lien_cbt" href="grimoire.php?personnage=<? echo $p; ?>&campagne=<? echo $_GET['campagne']; ?>"><i class="fa-solid fa-book"></i></a></span>
            </div>
            <div><a class="lien_cbt" href="grimoire_modifier.php?personnage=<? echo $p; ?>&campagne=<? echo $_GET['campagne']; ?>"><i class="fa-solid fa-pen-to-square"></i></a></div>
          </div>
          <div>
            <?
            // recherche de la classe de LS
            $requete='SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_caracteristiques ON cla_car_id=car_id WHERE cla_mag_id>0 AND pc_pe_id="'.$p.'" ORDER BY cla_mag_id';
            $result_ls=queryPDO($requete);
            $num_rows_ls=$result_ls->rowCount();
            if ($num_rows_ls>0):
              $dnls=$result_ls->fetch(PDO::FETCH_ASSOC);
              echo '<div>Nombre de sorts pas jour</div>';
              $requete='SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_classe_niveau ON cn_cla_id=cla_id WHERE cla_mag_id>0 AND pc_pe_id="'.$p.'" AND cla_id="'.$dnls['cla_id'].'" AND cn_niveau="'.$nls.'"';
              if ($_SESSION['mj']==1 && $_SESSION['debug']==1) echo '<div>'.$requete.'</div>';
              $result_nls=queryPDO($requete);
              $num_rows_nls=$result_nls->rowCount();
              if ($num_rows_nls>0):
                $dnnls=$result_nls->fetch(PDO::FETCH_ASSOC);
                $result='  <div class="tabMain mb10">';
                for ($i=0;$i<10;$i++):
                  $compCss='';
                  if ($i==1) $compCss=" cellLeft";
                  if (strlen($dnnls['cn_sort_n'.$i])>0):
                    $nbs=$dnnls['cn_sort_n'.$i];
                    else:
                    $nbs='-';
                  endif;
                  $result.='    <div class="cellMainSort">';
                  $result.='      <div>';
                  $result.='        <div class="cellEntete'.$compCss.'">'.$i.'</div>';
                  $result.='        <div class="cellValue'.$compCss.'">'.$nbs.'</div>';
                  $result.='      </div>';
                  $result.='    </div>';
                endfor;
                $result.='  </div>';
                echo '<div>'.$result.'</div>';
              endif;
            endif;            
            ?>
          </div>
          
          <div>
          <?
            $requete='SELECT gr_cla_id, count(grc_so_id) as nbsorts FROM dd_grimoires JOIN dd_grimoires_contenu ON gr_id=grc_gr_id WHERE gr_pe_id="'.$p.'" AND gr_defaut="1" GROUP BY gr_cla_id ORDER BY nbsorts DESC';
            if ($_SESSION['mj']==1 && $_SESSION['debug']==1) echo '<div>'.$requete.'</div>';
            $result_gr=queryPDO($requete);
            $num_rows_gr=$result_gr->rowCount();
            if ($num_rows_gr>0):
              $dngr=$result_gr->fetch(PDO::FETCH_ASSOC);
              if ($num_rows_gr>1) echo '<div class="titre">'.libelle("dd_classes","cla","nom",$dngr['gr_cla_id']).'</div>';
              echo '<div id="grimoire'.$dngr['gr_cla_id'].'">';
              $requete='SELECT gr_id, grc_so_id, so_nom, sc_niveau FROM dd_grimoires JOIN dd_grimoires_contenu ON gr_id=grc_gr_id LEFT JOIN dd_sorts ON grc_so_id=so_id LEFT JOIN dd_sortclasse ON so_id=sc_so_id WHERE gr_cla_id='.$dngr['gr_cla_id'].' AND sc_cla_id='.$dngr['gr_cla_id'].' AND gr_pe_id='.$p.' ORDER BY sc_niveau, so_nom';
              $result=queryPDO($requete);
              $num_rows=$result->rowCount();
              if ($num_rows > 0):
                // formatage du tableau de données
                $niveau='';
                $i=0;
                if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="action">'.$requete.'</div>';
                while($sort = $result->fetch(PDO::FETCH_ASSOC)):
                  if ($sort['sc_niveau']!=$niveau):
                    $i=0;
                    if ($niveau!='') echo '</div>';
                    echo '<div class="gras gros mb10"><i class="fa-solid fa-wand-magic-sparkles mr10"></i> Niveau '.$sort['sc_niveau'].'</div>';
                  endif;
                  if ($i==0) echo '<div class="lignePastille2">';
                  //echo '<span onClick="afficherSort('.$sort['gr_so_id'].')" class="lien">'.$sort['so_nom'].'</span>';
                  if ($_SESSION['onglet_sort']==1):
                    echo '  <div class="icone_onglet mr10">';
                    echo '    <a href="sort.php?sort='.$sort['grc_so_id'].'" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>';
                    echo '  </div>';
                  endif;         
                  echo '<div onClick="afficherSort('.$sort['grc_so_id'].')" class="pastille2 lien">'.$sort['so_nom'];
                  if ($isAdmin && $isDebug):
                    echo ' ('.$sort['grc_so_id'].')';
                  endif;
                  echo '</div>';
                  $i+=1;
                  $niveau=$sort['sc_niveau'];
                endwhile;
                echo '</div>';
              endif;
              echo '</div><!--  grimoire'.$dngr['gr_cla_id'].' -->';
              else:
              echo 'Aucun grimoire';
            endif;
          ?>      
          </div>
        </div> <!-- contenu -->
      </div> <!-- #grimoire -->

      <!--- Notes ---------------------------------------------------------------------------------------------------------------------------->
      <div id="notes" class="<? echo $classe_notes; ?>">
        <div class="contenu">
          <?
          $requete_filtre="SELECT tyno_id, tyno_nom, tyno_icone FROM dd_types_notes ORDER BY tyno_nom";
          $result_fn=queryPDO($requete_filtre);
          $num_rows_fn=$result_fn->rowCount();
          $filtre_notes='';
          if ($num_rows_fn > 0):
            $filtre_notes='<select id="filtre_notes" name="filtre_notes" onChange="affichageNotes()" class="search-select">>';
            $filtre_notes.='<option value="tout">tout</option>';
            while ($dnfn = $result_fn->fetch(PDO::FETCH_ASSOC)):
              $filtre_notes.='<option value="'.$dnfn['tyno_id'].'">'.htmlspecialchars($dnfn['tyno_nom']).'</option>';
            endwhile;
            $filtre_notes.='</select>';
          endif;
          ?>          
          <div class="titreAction">
            <div class="titreA">Notes</div>
          </div> <!-- titreAction -->
          <div>
            <?
            if ($filtre_notes!=''):
              echo $filtre_notes;
              echo '<button type="submit" class="search-button" id="search_res" name="search_res"/><i class="fa-solid fa-magnifying-glass"></i></button>';
            endif;
            ?>
          </div>          
          <div id="listeNotesPerso">
          <? include('include/insert/'.$_SESSION['rulesetRep'].'/listeNotesPerso.php'); ?>
          </div> <!-- listeNotesPerso -->
        </div> <!-- contenu -->
      </div> <!-- #notes -->

      <!--- Notes MJ ------------------------------------------------------------------------------------------------------------------------->
       <? if ($isAdmin): ?>
      <div id="mj" class="<? echo $classe_mj; ?>">
        <div class="contenu">
          <div class="titreAction">
            <div class="titreA">Notes MJ</div>
          </div>        
          <div>
            <? echo $notes_mj; ?>
          </div>
        </div>
      </div> <!-- #background -->
        <? endif; ?>
      
    </div> <!-- #personnage ---> 
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
	</div> <!-- wrapper --->
  <div id="modification"></div>
  <div id="detail-pp"></div>  
</div><!-- page --->
<script>
(function(){
  // Ajuste ce chemin à ton arborescence :
  const ENDPOINT = 'ajax/sorts_suggest.php';

  // Un timer par "wrap" pour le debounce
  const timers = new WeakMap();

  function getPartsFromWrap(wrap){
    const input  = wrap.querySelector('.js_sort_search');
    const panel  = wrap.querySelector('.ac-panel');      // <- classe, pas besoin d'ID unique
    const hidden = wrap.querySelector('.js_so_id');
    return {input, panel, hidden};
  }

  function render(wrap, items){
    const { panel } = getPartsFromWrap(wrap);
    if(!panel) return;
    panel.innerHTML = '';
    if(!items || !items.length){ panel.hidden = true; return; }
    items.forEach((it, idx) => {
      const div = document.createElement('div');
      div.className = 'ac-item' + (idx===0 ? ' is-active' : '');
      div.textContent = it.label;
      div.dataset.id = it.id;
      panel.appendChild(div);
    });
    panel.hidden = false;
  }

  function selectItem(wrap, item){
    const { input, hidden, panel } = getPartsFromWrap(wrap);
    if(input)  input.value  = item.label;
    if(hidden) hidden.value = item.id;
    if(panel)  panel.hidden = true;
  }

  async function search(wrap, q){
    try{
      const url = ENDPOINT + '?q=' + encodeURIComponent(q);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if(!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      render(wrap, Array.isArray(data) ? data : (data.results || []));
    }catch(err){
      console.error('Autocomplete fetch error:', err);
      render(wrap, []);
    }
  }

  // ---- DÉLÉGATIONS ----

  // Saisie + debounce
  document.addEventListener('input', (e) => {
    if(!e.target.matches('.js_sort_search')) return;
    const wrap = e.target.closest('.ac-wrap');
    if(!wrap) return;

    // reset l’ID si on modifie le texte
    const { hidden } = getPartsFromWrap(wrap);
    if(hidden) hidden.value = '';

    const q = e.target.value.trim();
    clearTimeout(timers.get(wrap));
    if(q.length < 2){ render(wrap, []); return; }
    const t = setTimeout(() => search(wrap, q), 180);
    timers.set(wrap, t);
  });

  // Navigation clavier
  document.addEventListener('keydown', (e) => {
    if(!e.target.matches('.js_sort_search')) return;
    const wrap = e.target.closest('.ac-wrap');
    if(!wrap) return;
    const { panel } = getPartsFromWrap(wrap);
    if(!panel || panel.hidden) return;

    const items = Array.from(panel.querySelectorAll('.ac-item'));
    if(!items.length) return;

    let idx = items.findIndex(i => i.classList.contains('is-active'));
    if(e.key === 'ArrowDown'){
      e.preventDefault();
      items[idx]?.classList.remove('is-active');
      idx = (idx + 1) % items.length;
      items[idx].classList.add('is-active');
      items[idx].scrollIntoView({block:'nearest'});
    }else if(e.key === 'ArrowUp'){
      e.preventDefault();
      items[idx]?.classList.remove('is-active');
      idx = (idx - 1 + items.length) % items.length;
      items[idx].classList.add('is-active');
      items[idx].scrollIntoView({block:'nearest'});
    }else if(e.key === 'Enter'){
      e.preventDefault();
      const it = items[idx];
      if(it) selectItem(wrap, { id: it.dataset.id, label: it.textContent });
    }else if(e.key === 'Escape'){
      panel.hidden = true;
    }
  });

  // Sélection à la souris (déléguée aussi)
  document.addEventListener('mousedown', (e) => {
    const item = e.target.closest('.ac-item');
    if(!item) return;
    const panel = item.closest('.ac-panel');
    if(!panel) return;
    const wrap = panel.closest('.ac-wrap');
    if(!wrap) return;
    selectItem(wrap, { id: item.dataset.id, label: item.textContent });
  });

  // Fermer si clic à l’extérieur
  document.addEventListener('click', (e) => {
    document.querySelectorAll('.ac-panel').forEach(p => {
      if(!p.contains(e.target) && !p.closest('.ac-wrap')?.contains(e.target)){
        p.hidden = true;
      }
    });
  });

})();
</script>

</html>