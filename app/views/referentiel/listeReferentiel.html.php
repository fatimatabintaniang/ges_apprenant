<div class="flex h-screen">
    <div class="p-6 overflow-y-auto h-[80vh] -mt-10">
        <div class="p-6 bg-[#F9EFEF] rounded-xl">
            <!-- Page Titre -->
            <div class="items-center mb-2">
                <h1 class="text-sm font-bold text-gray-100">Retour au referentiels actif</h1>
                <h1 class="text-4xl font-bold text-orange-500">Tous les referentiels</h1>
            </div>
            <p class="text-sm text-gray-500 mb-4">Liste complete des referentiels de formation</p>

            <div class="flex justify-between items-center mb-4">
                <form method="get" action="" class="w-1/2">
                    <input type="hidden" name="controllers" value="referentiel">
                    <input type="hidden" name="page" value="listeReferentiel">

                    <div class="relative">
                        <input type="text" name="search" placeholder="Rechercher par nom..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-orange-500">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                </form>
                <a href="?controllers=referentiel&page=listeReferentiel&search=<?= $_GET['search'] ?? '' ?>&showModal=1"
                    class="bg-orange-500 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-orange-600 transition">
                    <i class="ri-add-line"></i> Ajouter referentiel
                </a>
            </div>



            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="RefContainer">
                <?php foreach ($referentiels as $referentiel): ?>
                    <div class="bg-white rounded-lg shadow">
                        <img class="rounded" src="<?= htmlspecialchars($referentiel['image']) ?>" alt="">
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-red-500"><?= htmlspecialchars($referentiel['libelle']) ?></h3>
                            <h3 class="text-sm">Capacite: <?= htmlspecialchars($referentiel['capacite']) ?> places</h3>
                            <div class="flex justify-between mt-4">
                                <a href="?controllers=referentiel&page=listeReferentiel&action=edit&referentiel_id=<?= $referentiel['id_referentiel'] ?>&search=<?= $_GET['search'] ?? '' ?>"
                                    class="block p-2 hover:bg-orange-400 rounded text-xs bg-orange-500 text-white">Modifier</a>
                                <a href="?controllers=referentiel&page=listeReferentiel&ask_confirm=1&referentiel_id=<?= $referentiel['id_referentiel'] ?>&search=<?= $_GET['search'] ?? '' ?>"
                                    class="block p-2 hover:bg-red-400 rounded text-white text-xs bg-red-500">Archiver</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout/modification de referentiel -->
    <div id="addPromotionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 
        <?= (isset($_GET['showModal']) || (isset($_GET['action'])) && $_GET['action'] == 'edit') ? 'block' : 'hidden' ?>">
        <div class="bg-white rounded-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-xl font-semibold text-gray-800">
                    <?= isset($_GET['action']) && $_GET['action'] == 'edit' ? 'Modifier' : 'Ajouter' ?> un référentiel
                </h3>
                <a href="?controllers=referentiel&page=listeReferentiel&search=<?= $_GET['search'] ?? '' ?>"
                    class="text-gray-500 hover:text-gray-700">
                    <i class="ri-close-line"></i>
                </a>
            </div>

            <form method="post" action="?controllers=referentiel&page=<?= isset($_GET['action']) && $_GET['action'] == 'edit' ? 'updateReferentiel' : 'addReferentiel' ?>" class="p-6">
                <?php if (isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
                    <input type="hidden" name="referentiel_id" value="<?= $_GET['referentiel_id'] ?>">
                <?php endif; ?>

                <!-- Champ Libellé -->
                <div class="mb-4">
                    <label for="referentielName" class="block text-sm font-medium text-gray-700 mb-1">Nom du référentiel</label>
                    <input type="text" name="libelle" id="referentielName"
                        value="<?= htmlspecialchars(
                                    isset($_GET['action']) && $_GET['action'] == 'edit' ?
                                        ($referentielToEdit['libelle'] ?? '') : ($old['libelle'] ?? '')
                                ) ?>"

                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <?php if (!empty($errors['libelle'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['libelle'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Champ Image -->
                <div class="mb-4">
                    <label for="referentielImage" class="block text-sm font-medium text-gray-700 mb-1">URL de l'image</label>
                    <input type="url" name="image" id="referentielImage"
                        value="<?= htmlspecialchars(
                                    isset($_GET['action']) && $_GET['action'] == 'edit' ?
                                        ($referentielToEdit['image'] ?? '') : ($old['image'] ?? '')
                                ) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="https://example.com/image.jpg">
                    <?php if (!empty($errors['image'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['image'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Champ Description -->
                <div class="mb-4">
                    <label for="referentielDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="referentielDescription"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400"><?= htmlspecialchars(
                                                                                                                                                isset($_GET['action']) && $_GET['action'] == 'edit' ?
                                                                                                                                                    ($referentielToEdit['description'] ?? '') : ($old['description'] ?? '')
                                                                                                                                            ) ?></textarea>
                    <?php if (!empty($errors['description'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= $errors['description'] ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Champ Capacité -->
                    <div>
                        <label for="referentielCapacite" class="block text-sm font-medium text-gray-700 mb-1">Capacité</label>
                        <input type="number" name="capacite" id="referentielCapacite"
                            value="<?= htmlspecialchars(
                                        isset($_GET['action']) && $_GET['action'] == 'edit' ?
                                            ($referentielToEdit['capacite'] ?? '') : ($old['capacite'] ?? '')
                                    ) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <?php if (!empty($errors['capacite'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['capacite'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Session -->
                    <div>
                        <label for="referentielSession" class="block text-sm font-medium text-gray-700 mb-1">Session</label>
                        <input type="text" name="session" id="referentielSession"
                            value="<?= htmlspecialchars(
                                        isset($_GET['action']) && $_GET['action'] == 'edit' ?
                                            ($referentielToEdit['session'] ?? '') : ($old['session'] ?? '')
                                    ) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <?php if (!empty($errors['session'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['session'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="?controllers=referentiel&page=listeReferentiel&search=<?= $_GET['search'] ?? '' ?>"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 flex items-center gap-2">
                        <i class="ri-save-line"></i> <?= isset($_GET['action']) && $_GET['action'] == 'edit' ? 'Modifier' : 'Ajouter' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmation pour archiver un référentiel -->
    <div id="archive-confirm-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 <?= isset($showConfirmModal) && $showConfirmModal ? 'flex' : 'hidden' ?>">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Confirmer l'archivage</h2>
            <p class="mb-6">Êtes-vous sûr de vouloir archiver ce référentiel ?</p>

            <div class="flex justify-end space-x-3">
                <a href="?controllers=referentiel&page=listeReferentiel&search=<?= $search ?? '' ?>"
                    class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                    Annuler
                </a>
              <a href="?controllers=referentiel&page=archiveReferentiel&referentiel_id=<?= $confirmReferentielId ?>&search=<?= $search ?>"
    class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
    Confirmer
</a>
            </div>
        </div>
    </div>
</div>