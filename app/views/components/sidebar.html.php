 <!-- Sidebar -->
 <aside class="w-64 bg-gradient-to-b from-red-700 to-orange-500 text-white p-4 flex flex-col h-full">
   <div class="text-center mb-6">
     <div class="bg-white p-2 w-20 mx-auto rounded-2xl">
       <img src="assets/images/logo.png" alt="Logo" class="w-14 mx-auto mb-2">
     </div>
     <div class="mt-2">
       <span class="text-sm bg-[#F9CF98] text-[#87520E] px-2 py-1 rounded">Promotion <?= date("Y", strtotime("now")) ?></span>
     </div>
     <hr class="my-4">
   </div>
   <nav class="flex-1 space-y-6 text-sm">
     <a href="" class="flex items-center gap-2 hover:text-white"><i class="ri-dashboard-line"></i> Tableau de bord</a>
     <a href="<?=WEBROOB?>?controllers=promotion&page=listePromotion" class="flex items-center gap-2 text-white font-bold"><i class="ri-group-line"></i> Promotions</a>
     <a href="<?=WEBROOB?>?controllers=referentiel&page=listeReferentiel" class="flex items-center gap-2 hover:text-white"><i class="ri-booklet-line"></i> Referentiels</a>
     <a href="<?=WEBROOB?>?controllers=apprenant&page=listeApprenant" class="flex items-center gap-2 hover:text-white"><i class="ri-user-line"></i> Apprenant</a>
     <a href="#" class="flex items-center gap-2 hover:text-white"><i class="ri-calendar-check-line"></i> Gestion des présences</a>
     <a href="#" class="flex items-center gap-2 hover:text-white"><i class="ri-computer-line"></i> Kits et Laptop</a>
     <a href="#" class="flex items-center gap-2 hover:text-white"><i class="ri-bar-chart-line"></i> Rapport et Stats</a>
   </nav>
   <a href="<?= WEBROOB ?>?controllers=login&page=deconnexion" class="mt-auto bg-white text-red-700 py-2 rounded flex items-center justify-center gap-2">
     <button>
       <i class="ri-logout-box-r-line"></i> Déconnexion
     </button>
   </a>

 </aside>