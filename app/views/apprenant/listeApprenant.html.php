<div class="flex h-screen">
    <main class="flex-1">
        <div class="p-6 overflow-y-auto h-[80vh] -mt-5">
            <div class="p-6 bg-[#F9EFEF] rounded-xl">
                <!-- Page Titre -->
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h1 class="text-4xl font-bold text-orange-500">Apprenant</h1>
                        <p class="text-sm text-gray-500 mb-4">Gérer les apprenants de l'école</p>
                    </div>
                </div>

                <!-- Onglets -->
                <div class="border-b border-gray-200 mb-4">
                    <nav class=" grid grid-cols-2">
                        <a href="?controllers=apprenant&page=listeApprenant"
                            class="<?= (!isset($_GET['tab'])) ? 'border-orange-500 text-orange-500 text-center' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 text-center' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Liste des Retenus
                        </a>
                        <a href="?controllers=apprenant&page=listeApprenant&tab=attente"
                            class="<?= (isset($_GET['tab']) && $_GET['tab'] === 'attente') ? 'border-orange-500 text-orange-500 text-center' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 text-center' ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Liste d'Attente
                        </a>
                    </nav>
                </div>

                <!--Filtre-->
                <div class="flex justify-between items-center mb-4">
                    <form method="get" action="" class="w-[35%] text-xs">
                        <input type="hidden" name="controllers" value="apprenant">
                        <input type="hidden" name="page" value="listeApprenant">
                        <input type="hidden" name="statusFilter" value="<?= $_GET['statusFilter'] ?? 'all' ?>">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Rechercher par matricule..."
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400">
                            <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-orange-500">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>
                    </form>


                    <div class="flex gap-2 text-xs">
                        <form method="get" action="">
                            <input type="hidden" name="controllers" value="apprenant">
                            <input type="hidden" name="page" value="listeApprenant">
                            <select name="statusFilter" onchange="this.form.submit()" class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <option value="all" <?= ($_GET['statusFilter'] ?? 'all') === 'all' ? 'selected' : '' ?>>Filtre par statut</option>
                                <option value="actif" <?= ($_GET['statusFilter'] ?? 'all') === 'actif' ? 'selected' : '' ?>>Actif</option>
                                <option value="remplace" <?= ($_GET['statusFilter'] ?? 'all') === 'remplace' ? 'selected' : '' ?>>Remplace</option>
                            </select>
                        </form>
                    </div>

                    <div class="flex gap-2 text-xs">
                        <form method="get" action="">
                            <input type="hidden" name="controllers" value="apprenant">
                            <input type="hidden" name="page" value="listeApprenant">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <input type="hidden" name="statusFilter" value="<?= htmlspecialchars($_GET['statusFilter'] ?? 'all') ?>">
                            <select name="referentielFilter" class="p-2 border rounded" onchange="this.form.submit()">
                                <option value="all">Tous les référentiels</option>
                                <?php foreach ($referentiels as $referentiel): ?>
                                    <option value="<?= htmlspecialchars($referentiel['id_referentiel']) ?>"
                                        <?= ($_GET['referentielFilter'] ?? 'all') == $referentiel['id_referentiel'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($referentiel['libelle']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <div>
                        <a href="?controllers=apprenant&page=listeApprenant&tab=<?= $_GET['tab'] ?? 'retenus' ?>&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>&showModal=1" class="bg-orange-500 text-white text-xs  p-2 rounded-lg flex items-center gap-2 hover:bg-orange-600 transition">
                            Telecharger la liste<i class="ri-add-line"></i>
                        </a>
                    </div>

                    <div>
                        <a href="?controllers=apprenant&page=listeApprenant&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>&showModal=1" class="bg-orange-500 text-white text-xs p-2 rounded-lg flex items-center gap-2 hover:bg-orange-600 transition">
                            <i class="ri-add-line"></i> Ajouter Apprenant
                        </a>

                    </div>
                </div>

                <!-- Affichage en liste -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom complet</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telephone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referentiel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            // Filtrer les apprenants selon l'onglet sélectionné
                            $apprenantsFiltres = array_filter($apprenants, function ($apprenant) {
                                if (!isset($_GET['tab'])) {
                                    return true; // Afficher tous si aucun onglet sélectionné
                                }
                                return $_GET['tab'] === 'retenus'
                                    ? $apprenant['statut'] === 'actif'
                                    : $apprenant['statut'] === 'remplace';
                            });

                            foreach ($apprenantsFiltres as $apprenant): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (!empty($apprenant['image'])): ?>
                                            <?php $mime = getMimeTypeFromBinary($apprenant['image']); ?>
                                            <img class="rounded w-10 h-10 object-cover"
                                                src="data:<?= $mime ?>;base64,<?= base64_encode($apprenant['image']) ?>"
                                                alt="Image du référentiel">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-gray-200 flex items-center justify-center text-gray-500">
                                                Pas d’image
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($apprenant["matricule"] ?? 'Non défini') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($apprenant["nom_complet"] ?? 'Non assigné') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($apprenant["adresse"] ?? 'Non assigné') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($apprenant["telephone"] ?? 'Non assigné') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= htmlspecialchars($apprenant["referentiel"] ?? 'Non défini') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= strtolower($apprenant['statut']) === 'actif' ? 'bg-green-100 text-green-800' : (strtolower($apprenant['statut']) === 'remplace' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= htmlspecialchars(ucfirst($apprenant["statut"] ?? 'inconnu')) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="group relative inline-block">
                                            <button class="p-1 rounded-full hover:bg-gray-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div class="invisible group-hover:visible absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">
                                                    <a href="?controllers=apprenant&page=detailsApprenant&id=<?= $apprenant['id_apprenant'] ?>"
                                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="ri-eye-line mr-2 text-orange-500"></i>
                                                        Voir les détails
                                                    </a>
                                                    <a href="?controllers=apprenant&page=toggleStatus&id=<?= $apprenant['id_apprenant'] ?>&current_status=<?= strtolower($apprenant['statut']) ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <i class="ri-toggle-line mr-2 text-orange-500"></i>
                                                        Changer le statut
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <!-- Modal d'ajout de apprenant -->
                <div id="addApprenantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50   
<?= (isset($_GET['showModal']) || (isset($showModal) && $showModal)) ? 'block' : 'hidden' ?>">
                    <div class="bg-white rounded-lg p-6 w-[80%] h-[95vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-orange-500">Ajout Apprenant</h3>
                            <a href="?controllers=apprenant&page=listeApprenant&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>" class="text-gray-500 hover:text-gray-700">
                                <i class="ri-close-line"></i>
                            </a>
                        </div>

                        <form method="post" action="?controllers=apprenant&page=addApprenant" enctype="multipart/form-data">
                            <input type="hidden" name="redirect" value="?controllers=apprenant&page=listeApprenant&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>">

                            <h4 class="font-medium mb-3">Information de l'apprenant</h4>

                            <div class="grid grid-cols-3 gap-6 border border-gray-300 p-2 rounded">
                                <div>
                                    <div>
                                        <label for="prenom" class="block text-xs font-medium text-gray-700">Prénom</label>
                                        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['prenom'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['prenom'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label for="date_naissance" class="block text-xs font-medium text-gray-700">Date de naissance</label>
                                        <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($old['date_naissance'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['date_naissance'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['date_naissance'] ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="">
                                        <label for="adresse" class="block text-xs font-medium text-gray-700">Adresse</label>
                                        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($old['adresse'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['adresse'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['adresse'] ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="">
                                        <label for="telephone" class="block text-xs font-medium text-gray-700">Téléphone</label>
                                        <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['telephone'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['telephone'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <div>
                                        <label for="nom" class="block text-xs font-medium text-gray-700">Nom</label>
                                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['nom'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['nom'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['email'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <label for="lieu_naissance" class="block text-xs font-medium text-gray-700">lieu de naissance</label>
                                        <input type="lieu" id="lieu_naissance" name="lieu_naissance" value="<?= htmlspecialchars($old['lieu_naissance'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['lieu_naissance'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['lieu_naissance'] ?></p>
                                        <?php endif; ?>
                                    </div>


                                </div>

                                <!-- Champ Image -->
                                <div class="mb-4">
                                    <label for="referentielImage" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                                    <input type="file" name="image" id="referentielImage" accept="image/*" value="<?= htmlspecialchars($old['image'] ?? '') ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-400">
                                    <?php if (!empty($errors['image'])): ?>
                                        <p class="mt-1 text-sm text-red-600"><?= $errors['image'] ?></p>
                                    <?php endif; ?>


                                </div>

                            </div>


                            <h4 class="font-medium mb-3 mt-3">Information du tuteur</h4>


                            <div class="grid grid-cols-3 gap-4 mb-4">

                                <div>
                                    <div class="mb-4">
                                        <label for="tuteur_nom" class="block text-xs font-medium text-gray-700">Prénom</label>
                                        <input type="text" id="tuteur_nom" name="prenom_tuteur" value="<?= htmlspecialchars($old['prenom_tuteur'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['prenom_tuteur'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['prenom_tuteur'] ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-4">
                                        <label for="tuteur_nom" class="block text-xs font-medium text-gray-700">Nom</label>
                                        <input type="text" id="tuteur_nom" name="nom_tuteur" value="<?= htmlspecialchars($old['nom_tuteur'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['nom_tuteur'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['nom_tuteur'] ?></p>
                                        <?php endif; ?>
                                    </div>


                                </div>

                                <div>
                                    <div>
                                        <label for="tuteur_lien" class="block text-xs font-medium text-gray-700">Lien de parenté</label>
                                        <input type="text" id="lien" name="lien" value="<?= htmlspecialchars($old['lien'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['lien'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['lien'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label for="tuteur_telephone" class="block text-xs font-medium text-gray-700">Téléphone</label>
                                        <input type="tel" id="tuteur_telephone" name="telephone_tuteur" value="<?= htmlspecialchars($old['telephone_tuteur'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['telephone_tuteur'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['telephone_tuteur'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-4">
                                        <label for="adresse_tuteur" class="block text-xs font-medium text-gray-700">Adresse</label>
                                        <input type="text" id="adresse_tuteur" name="adresse_tuteur" value="<?= htmlspecialchars($old['adresse_tuteur'] ?? '') ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                        <?php if (!empty($errors['adresse_tuteur'])): ?>
                                            <p class="mt-1 text-sm text-red-600"><?= $errors['adresse_tuteur'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>


                            </div>

                            <div class="flex justify-end space-x-3 -mt-6 border-gray-300">
                                <a href="?controllers=apprenant&page=listeApprenant&statusFilter=<?= $_GET['statusFilter'] ?? 'all' ?>&search=<?= $_GET['search'] ?? '' ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    Annuler
                                </a>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
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