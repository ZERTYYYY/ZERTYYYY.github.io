Sauvegarde tah zebi : 

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <title>Document</title>
    <h1 class="H">Filtrage dynamique des compétences et certifications</h1>
</head>
<body>
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




<?php
// Connexion à MySQL
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'jaja';

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
    $sql = "SELECT nom, type, contenu FROM personne WHERE 1";
    $params = [];

    if ($typeRecherche) {
        $sql .= " AND type = :type";
        $params[':type'] = $typeRecherche;
    }
    if ($nomRecherche) {
        $sql .= " AND nom LIKE :nom";
        $params[':nom'] = '%' . $nomRecherche . '%';
    }
    if ($contenuRecherche) {
        $sql .= " AND contenu LIKE :contenu";
        $params[':contenu'] = '%' . $contenuRecherche . '%';
    }
    if ($qRecherche) {
        $sql .= " AND (nom LIKE :q OR type LIKE :q OR contenu LIKE :q)";
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
