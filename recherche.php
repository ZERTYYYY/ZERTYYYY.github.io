
<?php
// Connexion à MySQL
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'jppppppp'; // Changer pour la bonne base de données

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement AJAX pour la recherche
if (isset($_GET['type']) || isset($_GET['nom']) || isset($_GET['contenu'])) {
    $typeRecherche = isset($_GET['type']) ? trim($_GET['type']) : '';
    $nomRecherche = isset($_GET['nom']) ? trim($_GET['nom']) : '';
    $contenuRecherche = isset($_GET['contenu']) ? trim($_GET['contenu']) : '';
    $qRecherche = !empty($_GET['q']) ? trim($_GET['q']) : '';

    // Construction de la requête SQL
    $sql = "SELECT personne.nom, sujet.type, contenu.contenu
            FROM contenu
            JOIN personne ON contenu.personne_id = personne.id
            JOIN sujet ON contenu.sujet_id = sujet.id
            WHERE 1";
    $params = [];

    if ($typeRecherche) {
        $sql .= " AND sujet.type = :type";
        $params[':type'] = $typeRecherche;
    }
    if ($nomRecherche) {
        $sql .= " AND personne.nom LIKE :nom";
        $params[':nom'] = '%' . $nomRecherche . '%';
    }
    if ($contenuRecherche) {
        $sql .= " AND contenu.contenu LIKE :contenu";
        $params[':contenu'] = '%' . $contenuRecherche . '%';
    }
    if ($qRecherche) {
        $sql .= " AND (personne.nom LIKE :q OR sujet.type LIKE :q OR contenu.contenu LIKE :q)";
        $params[':q'] = '%' . $qRecherche . '%';
    }

    // Exécution de la requête
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retour des résultats en JSON
    echo json_encode($resultats);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrage dynamique</title>
    <link rel="stylesheet" href="zrk.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<head>

<div class="progress_container">
    <div class="progress_bar"></div>
  </div>
  
  <section class="top-nav">
    <input id="menu-toggle" type="checkbox" />
    <label class="menu-button-container" for="menu-toggle">
      <div class="menu-button"></div>
    </label>
    <ul class="menu">
      <li><a href="index.html">Accueil</a></li>
      <li><a href="html/partenaires.html">Partenaire</a></li>
      <li><a href="html/contact.html">Contact</a></li>
      <li><a href="/html/Rrecherche.php">Recherche</a></li>
    </ul>
  <a href="https://guardia.school/campus/lyon.html?utm_term=&utm_campaign=PMX+GU+-+Etudiants&utm_source=adwords&utm_medium=ppc&hsa_acc=1749547295&hsa_cam=20907422767&hsa_grp=&hsa_ad=&hsa_src=x&hsa_tgt=&hsa_kw=&hsa_mt=&hsa_net=adwords&hsa_ver=3&gad_source=1&gclid=Cj0KCQiA0fu5BhDQARIsAMXUBOLF5lQxduMnrC_3qKBJVAWHTUJK-DNhqhYN9tiGD5igEzrigsmo3pAaAjjzEALw_wcB">
  <img src="img/guardiagif.gif" alt="Logo" class="logo" href="test.html">
</a>
</section>

</head>






<body>

 <!-- Vidéo en arrière-plan -->
 <video autoplay muted loop id="background-video">
    <source src="img/background_index.mp4" type="video/mp4">
    Votre navigateur ne supporte pas les vidéos HTML5.
  </video>



<div class="container">
<div class="Akhycode">
    <!-- Barre de recherche -->
    <label for="searchInput" class="form-label"></label>
    <input type="text" id="searchInput" placeholder="Akhydeluxe...">
    <div class="AKHA"></div>
</div>





<!-- Système de filtres -->

    <div class="row mb-3">
        <div class="col">
            <label for="typeFilter" class="form-label">Filtrer par type :</label>
            <select id="typeFilter" class="form-select">
                <option value="">Tous</option>
                <option value="skill">Skills</option>
                <option value="certification">Certifications</option>
            </select>
        </div>
        <div class="col">
            <label for="nomFilter" class="form-label">Filtrer par nom :</label>
            <input type="text" id="nomFilter" class="form-control" placeholder="Nom...">
        </div>
        <div class="col">
            <label for="contenuFilter" class="form-label">Filtrer par contenu :</label>
            <input type="text" id="contenuFilter" class="form-control" placeholder="Contenu...">
        </div>
    </div>
    
    <div id="loading" class="spinner-border text-primary" role="status" style="display:none;">
        <span class="visually-hidden">Chargement...</span>
    </div>

    <div id="results" class="mt-3"></div>
</div>
</div>

    <script>
        $(document).ready(function () {
            let searchQuery = '';
            let searchType = '';
            let searchNom = '';
            let searchContenu = '';

            $('#searchInput').on('input', function () {
                searchQuery = $(this).val();
                fetchResults();
            });

            $('#typeFilter').on('change', function () {
                searchType = $(this).val();
                fetchResults();
            });

            $('#nomFilter').on('input', function () {
                searchNom = $(this).val();
                fetchResults();
            });

            $('#contenuFilter').on('input', function () {
                searchContenu = $(this).val();
                fetchResults();
            });

            function fetchResults() {
                $('#loading').show();

                $.ajax({
                    url: 'recherche.php',
                    method: 'GET',
                    data: {
                        q: searchQuery,
                        type: searchType,
                        nom: searchNom,
                        contenu: searchContenu
                    },
                    dataType: 'json',
                    success: function (data) {
                        let resultsDiv = $('#results');
                        resultsDiv.empty();

                        if (data.length > 0) {
                            data.forEach(function (item) {
                                resultsDiv.append(
                                    `<div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">${item.nom}</h5>
                                            <p class="card-text">Type : ${item.type}</p>
                                            <p class="card-text">Contenu : ${item.contenu}</p>
                                        </div>
                                    </div>`
                                );
                            });
                        } else {
                            resultsDiv.append('<p>Aucun résultat trouvé.</p>');
                        }

                        $('#loading').hide();
                    }
                });
            }
        });
    </script>
</body>
</html>
