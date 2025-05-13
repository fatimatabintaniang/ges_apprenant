<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Gestion des apprenants</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="h-screen flex items-center justify-center p-4 font-sans overflow-hidden " style="background-image:url(assets/images/bg.png); background-size: cover;
background-position: center;
background-repeat: no-repeat;">


  <div class="p-20 w-full md:w-[42%] flex flex-col justify-center ">
    <div class="shadow-xl p-6 rounded-2xl border-r-4 border-[#E9A750] bg-white">
      <div class="text-center mb-6">
        <img src="assets/images/logo.png" alt="Logo" class="mx-auto w-16 mb-2">
        <h2 class="text-lg text-gray-600">Bienvenue sur</h2>
        <h1 class="text-xl font-bold text-[#870E12]">Ecole supérieur professionel 221</h1>
        <h2 class="text-2xl font-semibold text-gray-900 mt-2">Se connecter</h2>
      </div>
      <form class="space-y-4 " id="loginForm" method="post">
        <input type="hidden" name="controllers" value="login">
        <input type="hidden" name="page" value="login">
        <div>
          <div class="relative flex items-center">
            <input name="email" type="text" placeholder="Entrer votre adresse mail"
              class="w-full border border-gray-300 px-4 py-2 pr-10 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
              value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
              <i class="ri-mail-line text-gray-400 text-lg"></i>
            </div>
          </div>
          <?php if (!empty($errors['email'])): ?>
            <p name="emailError" class="mt-2 text-xs text-red-500 flex items-center ">
              <i class="fas fa-exclamation-circle mr-1"></i> <?= $errors['email'] ?>
            </p>
          <?php endif; ?>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input name="mot_de_passe" type="password" placeholder="Entrer votre mot de passe"
            class="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-red-500" />
          <?php if (!empty($errors['mot_de_passe'])): ?>
            <p name="passwordError" class="mt-2 text-xs text-red-500 flex items-center">
              <i class="fas fa-exclamation-circle mr-1"></i> <?= $errors['mot_de_passe'] ?>
            </p>
          <?php endif; ?>
        </div>
        <div class="text-right">
          <a href="?controllers=login&page=login&showModal=1" class="text-sm text-[#870E12] hover:underline">Mot de passe oublié ?</a>
        </div>
        <button type="submit"
          class="w-full bg-[#870E12] text-white py-2 rounded hover:bg-red-800 transition duration-200">
          Se connecter
        </button>
      </form>
    </div>
  </div>

 <!-- Modifiez cette partie dans login.html.php -->
<div id="forgotPasswordModal" class="fixed inset-0 bg-black bg-opacity-100 flex items-center justify-center p-4 z-50 <?= isset($_GET['showModal']) ? '' : 'hidden' ?>">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Réinitialiser le mot de passe</h3>
            <a href="?controllers=login&page=login" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </a>
        </div>

        <form id="forgotPasswordForm" method="post" class="space-y-4">
            <input type="hidden" name="controllers" value="login">
            <input type="hidden" name="page" value="resetPassword">
            
            <div>
                <label for="resetEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="far fa-envelope text-gray-400"></i>
                    </div>
                    <input id="resetEmail" name="email" type="email" placeholder="votre@email.com"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition duration-300 hover:border-primary/50"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                <?php if (!empty($resetErrors['email'])): ?>
                    <p class="mt-2 text-xs text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?= $resetErrors['email'] ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="newPassword" name="newPassword" type="password" placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition duration-300 hover:border-primary/50">
                </div>
                <?php if (!empty($resetErrors['newPassword'])): ?>
                    <p class="mt-2 text-xs text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?= $resetErrors['newPassword'] ?>
                    </p>
                <?php endif; ?>
            </div>

            <div>
                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="confirmPassword" name="confirmPassword" type="password" placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition duration-300 hover:border-primary/50">
                </div>
                <?php if (!empty($resetErrors['confirmPassword'])): ?>
                    <p class="mt-2 text-xs text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> <?= $resetErrors['confirmPassword'] ?>
                    </p>
                <?php endif; ?>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-6 rounded-xl shadow-md hover:shadow-lg text-lg font-semibold text-white bg-[#E9A750] hover:from-primary/90 hover:to-accent/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50 transition-all duration-300 hover:-translate-y-0.5">
                <i class="fas fa-sync-alt mr-2"></i> Réinitialiser
            </button>
        </form>
    </div>
</div>
</body>

</html>