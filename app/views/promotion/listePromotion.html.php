<div class="flex h-screen ">
  <main class="flex-1 ">
    <div class="p-6 overflow-y-auto h-[80vh] -mt-5">
      <div class="p-6 bg-[#F9EFEF] rounded-xl">
        <!-- Page Titre -->
        <div class="flex justify-between items-center mb-2">
          <div>
            <h1 class="text-4xl font-bold text-orange-500">Promotion</h1>
            <p class="text-sm text-gray-500 mb-4">Gérer les promotions de l'école</p>
          </div>

          <a href="?controllers=promotion&page=listePromotion&view=<?= $_GET['view'] ?? 'grid' ?>&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>&showModal=1" class="bg-orange-500 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-orange-600 transition">
            <i class="ri-add-line"></i> Ajouter promotion
          </a>

        </div>

        <!-- Statistique-->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

          <div class="bg-red-700 text-white p-4 rounded-lg text-center shadow">
            <p class="text-2xl font-bold">
              <?= $total ?? 0 ?>
            </p>
            <p>Apprenant</p>
          </div>
          <div class="bg-red-700 text-white p-4 rounded-lg text-center shadow">
            <p class="text-2xl font-bold">
              <?= $total_referentiel ?? 0 ?>
            </p>
            <p>Référentiel</p>
          </div>
          <div class="bg-red-700 text-white p-4 rounded-lg text-center shadow">
            <p class="text-2xl font-bold">
              <?= $total_promotionActive ?? 0 ?>
            </p>
            <p>Promotion active</p>
          </div>
          <div class="bg-red-700 text-white p-4 rounded-lg text-center shadow">
            <p class="text-2xl font-bold">
              <?= $total_promotion ?? 0 ?>
            </p>
            <p>Total promotion</p>
          </div>
        </div>

        <!--Filtre-->
        <div class="flex justify-between items-center mb-4">
          <form method="get" action="" class="w-1/2">
            <input type="hidden" name="controllers" value="promotion">
            <input type="hidden" name="page" value="listePromotion">
            <input type="hidden" name="view" value="<?= $_GET['view'] ?? 'grid' ?>">
            <input type="hidden" name="statusFilter" value="<?= $_GET['statusFilter'] ?? 'all' ?>">
            <div class="relative">
              <input type="text" name="search" placeholder="Rechercher par nom..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400">
              <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-orange-500">
                <i class="ri-search-line"></i>
              </button>
            </div>
          </form>

          <div class="flex gap-2">
            <form method="get" action="">
              <input type="hidden" name="controllers" value="promotion">
              <input type="hidden" name="page" value="listePromotion">
              <input type="hidden" name="view" value="<?= $_GET['view'] ?? 'grid' ?>">
              <input type="hidden" name="search" value="<?= $_GET['search'] ?? '' ?>">
              <select name="statusFilter" onchange="this.form.submit()" class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400">
                <option value="all" <?= ($_GET['statusFilter'] ?? 'all') === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="active" <?= ($_GET['statusFilter'] ?? 'all') === 'active' ? 'selected' : '' ?>>Actif</option>
                <option value="inactive" <?= ($_GET['statusFilter'] ?? 'all') === 'inactive' ? 'selected' : '' ?>>Inactif</option>
              </select>
            </form>

            <a href="?controllers=promotion&page=listePromotion&view=grid&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>"
              class="<?= ($_GET['view'] ?? 'grid') === 'grid' ? 'bg-orange-500 text-white' : 'bg-gray-200' ?> p-2 rounded transition hover:bg-orange-600"
              aria-label="Vue grille">
              Grille
            </a>
            <a href="?controllers=promotion&page=listePromotion&view=list&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>"
              class="<?= ($_GET['view'] ?? 'grid') === 'list' ? 'bg-orange-500 text-white' : 'bg-gray-200' ?> p-2 rounded transition hover:bg-orange-600"
              aria-label="Vue liste">
              Liste
            </a>
          </div>
        </div>
      </div>

      <!-- Affichage en grille-->
      <div class="<?= ($_GET['view'] ?? 'grid') === 'grid' ? 'block' : 'hidden' ?> grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-6">
        <?php if (empty($promotions)): ?>
          <div class="col-span-full py-16 text-center animate-pulse">
            <div class="mx-auto w-28 h-28 rounded-full bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center mb-6 shadow-inner">
              <i class="fas fa-chalkboard-teacher text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-700">Aucun promotions programmé</h3>
            <p class="text-gray-400 mt-2">Les promotions apparaîtront ici</p>
          </div>
        <?php else: ?>
          <?php foreach ($promotions as $promotion): ?>
            <div class="relative bg-white rounded-xl overflow-hidden shadow-md border hover:shadow-xl transition-shadow duration-300 group">

              <!-- Bandeau couleur-->
              <div class="h-2 bg-gradient-to-r from-orange-400 to-red-500 w-full"></div>

              <div class="p-5">
                <!-- Nom de la promotion -->
                <div class="mb-2 flex justify-between">
                  <div>
                    <h3 class="text-xl font-bold text-gray-800">
                      <?= htmlspecialchars($promotion["promotion"] ?? 'Non défini') ?>
                    </h3>
                    <div class="flex text-xs text-gray-500 mb-3">
                      <span><?= htmlspecialchars($promotion["date_debut"] ?? 'Non assigné') ?></span>
                      <span>---</span>
                      <span><?= htmlspecialchars($promotion["date_fin"] ?? 'Non assigné') ?></span>
                    </div>
                  </div>
                  <div class="w-[10%] h-[10%] rounded-full p-6 bg-gray-200"></div>
                </div>


                <!-- Nombre d'apprenants -->
                <div class="mb-4">
                  <span class="inline-block bg-purple-100 text-black px-3 py-1 rounded-full text-sm font-medium">
                    <?= htmlspecialchars($promotion["nombre_apprenants"] ?? '0') ?> Apprenant(s)
                  </span>
                </div>



                <!-- Action -->
                <div class="flex justify-between">

                  <!-- Statut -->
                  <div class="">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
      <?= $promotion['statut'] === 'Actif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                      <?= htmlspecialchars($promotion["statut"] ?? 'Inconnu') ?>
                    </span>
                  </div>
                  <button class="text-sm text-orange-500 font-medium hover:underline">Voir détails</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>


      <!-- Affichage en liste -->
      <div class="<?= ($_GET['view'] ?? 'grid') === 'list' ? 'block' : 'hidden' ?> bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date_debut</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date_fin</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référentiels</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($promotions as $promotion): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  <?= htmlspecialchars($promotion["promotion"] ?? 'Non défini') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <?= htmlspecialchars($promotion["date_debut"] ?? 'Non assigné') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <?= htmlspecialchars($promotion["date_fin"] ?? 'Non assigné') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <?= htmlspecialchars($promotion["referentiel"] ?? 'Non défini') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
              <?= $promotion['statut'] === 'Actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                    <?= htmlspecialchars($promotion["statut"] ?? 'Inconnu') ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <a href="#" class="text-orange-600 hover:text-orange-900">Voir détails</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal d'ajout de promotion -->
      <div id="addPromotionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 
    <?= (isset($_GET['showModal']) || (isset($showModal) && $showModal)) ? 'block' : 'hidden' ?>">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-orange-500">Ajouter une promotion</h3>
            <a href="?controllers=promotion&page=listePromotion&view=<?= $_GET['view'] ?? 'grid' ?>&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>" class="text-gray-500 hover:text-gray-700">
              <i class="ri-close-line"></i>
            </a>
          </div>

          <form method="post" action="?controllers=promotion&page=addPromotion">
            <input type="hidden" name="redirect" value="?controllers=promotion&page=listePromotion&view=<?= $_GET['view'] ?? 'grid' ?>&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>">

            <div class="mb-4">
              <label for="nom" class="block text-sm font-medium text-gray-700">Nom de la promotion</label>
              <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
              <?php if (!empty($errors['nom'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= $errors['nom'] ?></p>
              <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                <input type="date" id="date_debut" name="date_debut" value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                <?php if (!empty($errors['date_debut'])): ?>
                  <p class="mt-1 text-sm text-red-600"><?= $errors['date_debut'] ?></p>
                <?php endif; ?>
              </div>
              <div>
                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                <?php if (!empty($errors['date_fin'])): ?>
                  <p class="mt-1 text-sm text-red-600"><?= $errors['date_fin'] ?></p>
                <?php endif; ?>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Statut</label>
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input type="radio" name="statut" value="Actif" checked class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                  <span class="ml-2">Actif</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input type="radio" name="statut" value="Inactif" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                  <span class="ml-2">Inactif</span>
                </label>
              </div>
            </div>

            <!-- Dans la section référentiels -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700">Référentiels</label>
              <div class="grid grid-cols-2 text-xs gap-2">
                <?php foreach (findAllReferentiels() as $referentiel): ?>
                  <div class="flex items-center">
                    <input type="checkbox" name="referentiels[]"  value="<?= $referentiel['id_referentiel'] ?>"
                      <?= (isset($old['referentiels']) && in_array($referentiel['id_referentiel'], $old['referentiels'])) ? 'checked' : '' ?>
                      class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                    <label class="ml-2"><?= htmlspecialchars($referentiel['libelle']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
              <?php if (!empty($errors['referentiels'])): ?>
                <p class="mt-1 text-sm text-red-600"><?= $errors['referentiels'] ?></p>
              <?php endif; ?>
            </div>

            <div class="flex justify-end space-x-3">
              <a href="?controllers=promotion&page=listePromotion&view=<?= $_GET['view'] ?? 'grid' ?>&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Annuler
              </a>
              <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Enregistrer
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
</div>
</main>

</div>