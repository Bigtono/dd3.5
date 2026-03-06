    <div class="sortable-list" data-list-id="<?= htmlspecialchars($listId) ?>" data-global-sort="1">
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
          <div class="<?= $joueurClass ?>" data-sort-field="joueur">
            Joueur
          </div>
        <?php endif; ?>

        <div class="col">
          Race
        </div>

        <div class="col3">
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

            <div class="col">
              <? if ($c && $c > 0): ?>
                <a href="personnage.php?personnage=<? echo $dn['pe_id']; ?>&campagne=<? echo $c; ?>"><?= htmlspecialchars($dn['pe_nom']) ?></a>
              <? else: ?>
                <a href="personnage.php?personnage=<? echo $dn['pe_id']; ?>"><?= htmlspecialchars($dn['pe_nom']) ?></a>
              <? endif; ?>
            </div>

            <?php if ($isAdmin || $listId === 'campagne'): ?>
              <div class="col">
                <?= htmlspecialchars($dn['joueur_nom']) ?>
              </div>
            <?php endif; ?>

            <div class="col">
              <?= libelle("dd_races", "ra", "nom", $dn['pe_ra_id']) ?>
            </div>

            <div class="col3">
              <?= classesPersonnage($dn['pe_id']) ?>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    </div>