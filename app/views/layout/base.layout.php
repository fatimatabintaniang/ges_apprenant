
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des apprenants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7C3AED',
                        secondary: '#F59E0B',
                        accent: '#EC4899',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 overflow-hidden">
    <div class="flex h-screen">

        <?php
        $role = $_SESSION["utilisateur"]["role"] ?? null;

        switch ($role) {
            case "Admin":
                require_once "../app/views/components/sidebar.html.php";
                break;
            case "Vigile":
                require_once "../views/components/sidebarVi.html.php";
                break;
            case "Apprenant":
                require_once "../views/components/sidebarApp.html.php";
                break;
            default:
                echo "<div class='p-4 text-red-500'>Rôle inconnu ou non connecté.</div>";
                break;
        }
        ?>
        <main class="flex-1">
            <header class="flex justify-between items-center bg-white shadow-xl p-4 rounded-xl border border-gray-100 z-50">
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" placeholder="Rechercher..." 
                class="border border-gray-200 pl-10 pr-4 py-2 rounded-xl w-full focus:ring-2 focus:ring-primary focus:border-primary transition duration-300">
        </div>

        <div class="flex items-center space-x-4 group cursor-pointer">
            <div class="relative">
                <img src="https://img.freepik.com/photos-gratuite/avatar-androgyne-personne-queer-non-binaire_23-2151100279.jpg" 
                    alt="User" 
                    class="w-10 h-10 rounded-full border-2 border-white shadow-md group-hover:border-primary transition duration-300">
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
            </div>
            <div class="text-right">
                <p class="text-gray-800 font-semibold leading-tight group-hover:text-primary transition duration-300"><?= $_SESSION["utilisateur"]["prenom"] ?? 'Utilisateur' ?>
                </p>
                <p class="text-sm text-gray-500 group-hover:text-primary/80 transition duration-300"><?= $_SESSION["utilisateur"]["nom"] ?? 'Utilisateur' ?>
                </p>
            </div>
        </div>
            </header>            
            <div class="mt-6 p-6">
                <?= $content ?? '<p>Aucun contenu à afficher.</p>'; ?>
            </div>
        </main>
    </div>
</body>

</html>
