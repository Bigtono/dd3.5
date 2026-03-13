<div class="contenu">
  <div class="titreAction">
    <div class="titreA">
      Objets magiques
      <? if ($canEditPersonnage): ?>
        <a class="lien_cbt" onClick="gererEquipement('<? echo $p; ?>')"><i class="fa-solid fa-pen-to-square ml15"></i></a>
      <? endif; ?>
    </div>
    <div></div>
  </div>
  <div id="listeOM">
    <?
    include('include/insert/' . $_SESSION['rulesetRep'] . '/listeEqt.php');
    echo $listeEqt;
    ?>
  </div>
</div>
