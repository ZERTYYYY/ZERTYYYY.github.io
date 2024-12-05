<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrage dynamique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .result-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        #results {
            margin-top: 20px;
        }
        #loading {
            display: none;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Filtrage dynamique des compétences et certifications</h1>

        <!-- Formulaire de filtrage -->
        <div class="mb-3">
            <label for="searchInput" class="form-label">Rechercher :</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Recherchez par nom, contenu...">
        </div>

        <!-- Filtres supplémentaires -->
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

        <div id="loading" class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>

        <div id="results" class="mt-3">
            <!-- Les résultats de la recherche apparaîtront ici -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Variables pour les critères de filtrage
            let searchQuery = ''; // Variable pour la recherche par texte
            let searchType = '';  // Variable pour filtrer par type (skill ou certification)
            let searchNom = '';   // Variable pour filtrer par nom
            let searchContenu = ''; // Variable pour filtrer par contenu

            // Recherche par texte
            $('#searchInput').on('input', function () {
                searchQuery = $(this).val();
                fetchResults();
            });

            // Filtrer par type
            $('#typeFilter').on('change', function () {
                searchType = $(this).val();
                fetchResults();
            });

            // Filtrer par nom
            $('#nomFilter').on('input', function () {
                searchNom = $(this).val();
                fetchResults();
            });

            // Filtrer par contenu
            $('#contenuFilter').on('input', function () {
                searchContenu = $(this).val();
                fetchResults();
            });

            // Fonction pour récupérer les résultats filtrés
            function fetchResults() {
                $('#loading').show(); // Afficher le spinner de chargement

                $.ajax({
                    url: 'recherche.php',  // URL vers le fichier PHP pour la recherche
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
                        resultsDiv.empty(); // Effacer les anciens résultats

                        if (data.length > 0) {
                            data.forEach(function (item) {
                                resultsDiv.append(
                                    `<div class="result-item">
                                        <strong>${item.nom} (${item.type})</strong>: ${item.contenu}
                                    </div>`
                                );
                            });
                        } else {
                            resultsDiv.html('<p>Aucun résultat trouvé.</p>');
                        }
                    },
                    complete: function() {
                        $('#loading').hide(); // Masquer le spinner une fois les résultats reçus
                    }
                });
            }
        });
    </script>
</body>
</html>
