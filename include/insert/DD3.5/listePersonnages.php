    <!--- Liste des personnages DD3.5 --->

    <div<?= isset($listDomId) && $listDomId !== '' ? ' id="' . htmlspecialchars($listDomId) . '"' : '' ?> class="sortable-list" data-list-id="<?= htmlspecialchars($listId) ?>" data-global-sort="1">
      <div class="list-header">
        <?
        // classes pour flèches
        $personnageClass = 'col';
        if ($sort === 'personnage') {
          $personnageClass .= ' sort-' . ($orderDir === 'ASC' ? 'asc' : 'desc');
        }

        $joueurClass = 'col';
        if ($isAdmin && $sort === 'joueur') {
          $joueurClass .= ' sort-' . ($orderDir === 'ASC' ? 'asc' : 'desc');
        }
        ?>

        <!-- Icône suppression -->
        <div class="col action-col">
          <i class="fa-solid fa-trash"></i>
        </div>

        <!-- Icône modification -->
        <div class="col action-col">
          <i class="fa-solid fa-pen-to-square"></i>
        </div>

        <div class="<?= $personnageClass ?>" data-sort-field="campagne">
          Personnage
        </div>

        <?php if ($isAdmin || $listId === 'campagne'): ?>
          <div class="<?= $joueurClass ?> personnage-col-joueur" data-sort-field="joueur">
            Joueur
          </div>
        <?php endif; ?>

        <div class="col personnage-col-race">
          Race
        </div>

        <div class="col3 personnage-col-classes">
          classes
        </div>
      </div>

      <div class="list-body">
        <?php foreach ($rows as $dn): ?>

          <div class="list-row">

            <!-- Icône suppression -->
            <div class="col action-col">
              <a href="campagnes.php?action=supprimer&id=<?= $camp['camp_id'] ?>"
                class="action-delete"
                title="Supprimer">
                <i class="fa-solid fa-trash"></i>
              </a>
            </div>

            <!-- Icône modification -->
            <div class="col action-col">
              <?
              $complement = "";
              if ($c && $c > 0) $complement = "&campagne=" . $c;
              ?>
              <a href="personnage-modifier.php?personnage=<?= $dn['pe_id'] . $complement ?>"
                class="action-edit"
                title="Modifier">
                <i class="fa-solid fa-pen-to-square"></i>
              </a>
            </div>

            <?php
            $raceLabel = libelle("dd_races", "ra", "nom", $dn['pe_ra_id']);
            $classesLabel = classesPersonnage($dn['pe_id']);
            $metaParts = array();
            if ($isAdmin || $listId === 'campagne') {
              $metaParts[] = htmlspecialchars($dn['joueur_nom']);
            }
            $metaParts[] = htmlspecialchars(strip_tags($raceLabel));
            $metaParts[] = htmlspecialchars(strip_tags($classesLabel));
            $metaLine = implode(' &bull; ', $metaParts);
            ?>
            <div class="col personnage-col-nom">
              <? if ($c && $c > 0): ?>
                <a href="personnage.php?personnage=<? echo $dn['pe_id']; ?>&campagne=<? echo $c; ?>"><?= htmlspecialchars($dn['pe_nom']) ?></a>
              <? else: ?>
                <a href="personnage.php?personnage=<? echo $dn['pe_id']; ?>"><?= htmlspecialchars($dn['pe_nom']) ?></a>
              <? endif; ?>
              <div class="personnage-meta"><?= $metaLine ?></div>
            </div>

            <?php if ($isAdmin || $listId === 'campagne'): ?>
              <div class="col personnage-col-joueur">
                <?= htmlspecialchars($dn['joueur_nom']) ?>
              </div>
            <?php endif; ?>

            <div class="col personnage-col-race">
              <?= $raceLabel ?>
            </div>

            <div class="col3 personnage-col-classes">
              <?= $classesLabel ?>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
      </div>
