<?php
session_start();
require_once('../../includes/config.php');

// Vérifier si l'utilisateur est connecté
/*if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header('Location: ../login.php');
    exit();
}*/

// 1. Récupérer les livraisons avec infos fournisseur
$stmt = $pdo->query("
    SELECT l.id, l.numero_livraison, l.date_livraison, l.fournisseur_id, f.nom AS fournisseur_nom, 
           l.etat, l.commentaire, l.date_reception
    FROM livraisons l
    LEFT JOIN fournisseurs f ON l.fournisseur_id = f.id
");
$livraisons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Récupérer les articles liés aux livraisons (avec nom article)
$stmt = $pdo->query("
    SELECT la.livraison_id, la.article_id, a.nom_article AS article_nom, la.quantite
    FROM livraison_articles la
    JOIN articles a ON la.article_id = a.id
");
$livraison_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Regrouper les articles par livraison_id pour accès facile
$articles_par_livraison = [];
foreach ($livraison_articles as $la) {
    $articles_par_livraison[$la['livraison_id']][] = $la;
}

// 4. Ajouter les articles à chaque livraison
foreach ($livraisons as &$livraison) {
    $livraison['articles'] = $articles_par_livraison[$livraison['id']] ?? [];
}
unset($livraison); // éviter référence persistante

// --- Partie commandes avec détails ---

// 5. Récupérer les commandes avec infos client
$stmt = $pdo->query("
    SELECT c.id, c.date_commande, c.date_prevue_envoi, c.etat_preparation
    FROM commandes c
    WHERE c.date_prevue_envoi IS NOT NULL
");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Récupérer les lots liés aux commandes (avec nom article)
$stmt = $pdo->query("
    SELECT commande_id, lot_id, quantite_lot_commande, articles, date_prevue_envoi, etat_lot
    FROM commande_lot
    WHERE date_prevue_envoi IS NOT NULL
");
$commande_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Parcourir les lots pour décoder le JSON des articles et regrouper par commande
$lots_par_commande = [];
foreach ($commande_articles as $lot) {
    $articles = $articles_par_lot[$lot['lot_id']] ?? [];


    // Enrichir chaque article avec son nom depuis la table `articles`
    foreach ($articles as &$article) {
        $stmt = $pdo->prepare("SELECT nom_article FROM articles WHERE id = ?");
        $stmt->execute([$article['id_article']]);
        $article['nom_article'] = $stmt->fetchColumn() ?? 'Inconnu';
    }

    $lot['articles'] = $articles;
    $lots_par_commande[$lot['commande_id']][] = $lot;
}

// Puis, récupérer les commandes
$stmt = $pdo->query("
    SELECT id, date_prevue_envoi, etat_preparation
    FROM commandes
    WHERE date_prevue_envoi IS NOT NULL
");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter les lots (avec leurs articles) à chaque commande
foreach ($commandes as &$commande) {
    $commande['lots'] = $lots_par_commande[$commande['id']] ?? [];
}
unset($commande);

// 7. Récupérer les événements
$stmt = $pdo->query("
    SELECT id, title, date
    FROM evenements
");
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les articles associés aux lots (lot_id → article_id)
$stmt = $pdo->query("
    SELECT al.lot_id, al.article_id, al.quantite, a.nom_article
    FROM article_lot al
    JOIN articles a ON al.article_id = a.id
");
$articles_par_lot = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $articles_par_lot[$row['lot_id']][] = [
        'id_article' => $row['article_id'],
        'nom_article' => $row['nom_article'],
        'quantite' => $row['quantite']
    ];
}


if (!isset($evenements) || !is_array($evenements)) {
    $evenements = [];
}

$events = [];

foreach ($livraisons as $livraison) {
    $events[] = [
        'id' => "livraison-" . $livraison['id'],
        'title' => "Livraison #" . htmlspecialchars($livraison['numero_livraison']),
        'start' => $livraison['date_reception'],
    ];
}

foreach ($evenements as $evt) {
    $events[] = [
        'title' => htmlspecialchars($evt['title']),
        'start' => $evt['date'],
    ];
}

foreach ($commandes as $commande) {
    $events[] = [
        'id' => "commande-" . $commande['id'],
        'title' => "Commande #" . htmlspecialchars($commande['id']),
        'start' => $commande['date_prevue_envoi'],
    ];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier</title>
    <link rel="stylesheet" href="../../public/style.css">
    <link rel="stylesheet" href="../../public/calendrier.css">
    <link rel="stylesheet" href="../../public/calendar.css">
    <link rel="icon" href="../../img/logo_w.png" type="image/png">
    <!-- Linking Google Fonts for Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
</head>
<body>
<!-- Mobile Sidebar Menu Button -->
    <button class="sidebar-menu-button">
        <span class="material-symbols-rounded">menu</span>
    </button>

    <aside class="sidebar">
        <!-- Sidebar Header -->
        <hearder class="sidebar-header">
            <a href="" class="header-logo">
                <img src="../../img/logo_w.png" alt="FASHION CHIC">
                <!-- Faire en sorte que l'image soit différente quand la sidebar est collapsed -->
            </a>
            <button class="sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
        </hearder>

        <nav class="sidebar-nav">
            <!-- Primary Top Nav -->
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <span class="material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Tableau de bord</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Tableau de bord</a>
                        </li>
                    </ul>
                </li>
                <!-- Dropdown -->
                    <li class="nav-item dropdown-container">
                        <a href="#" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">inventory_2</span>
                            <span class="nav-label">Stock</span>
                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                        </a>
                        <!-- Dropdown menu -->
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Stock</a>
                            </li>
                            <li class="nav-item">
                                <a href="lots.php" class="nav-link dropdown-link">Lots</a>
                            </li>
                            <li class="nav-item">
                                <a href="articles.php" class="nav-link dropdown-link">Articles</a>
                            </li>
                            <li class="nav-item">
                                <a href="rangement.php" class="nav-link dropdown-link">Rangement</a>
                            </li>
                        </ul>
                    </li>
                <?php if (
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ||
                    (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire de stock')
                ): ?>
                    <li class="nav-item">
                        <a href="commandes.php" class="nav-link">
                            <span class="material-symbols-rounded">shopping_cart</span>
                            <span class="nav-label">Commandes</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item">
                                <a class="nav-link dropdown-title">Commandes</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!-- Dropdown -->
                <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">mail</span>
                        <span class="nav-label">Messagerie</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>
                    <!-- Dropdown menu -->
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Messagerie</a>
                        </li>
                        <li class="nav-item">
                            <a href="messages.php" class="nav-link dropdown-link">Mes messages</a>
                        </li>
                        <li class="nav-item">
                            <a href="alertes.php" class="nav-link dropdown-link">Alertes</a>
                        </li>
                        <li class="nav-item">
                            <a href="fournisseurs.php" class="nav-link dropdown-link">Fournisseurs</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="calendrier.php" class="nav-link active">
                        <span class="material-symbols-rounded">calendar_today</span>
                        <span class="nav-label">Calendrier</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Calendrier</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Secondary Bottom Nav -->
            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="parameters.php" class="nav-link">
                        <span class="material-symbols-rounded">settings</span>
                        <span class="nav-label">Paramètres</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Paramètres</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <span class="material-symbols-rounded">power_settings_new</span>
                        <span class="nav-label">Déconnexion</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a class="nav-link dropdown-title">Déconnexion</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </aside>

    <section class="main-content">
        <header class="header">
            <h1>CALENDRIER</h1>
        </header>
        <section class="btn-section">
            <button id="add-event-btn" class="btn" style="margin-bottom: 20px;">Ajouter un événement</button>
        </section>
            
            <div id="calendar"></div>

        <div id="add-event-modal" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:350px;">
                <span class="close" onclick="closeAddEventModal()" style="float:right;cursor:pointer;">&times;</span>
                <h2>Nouvel événement</h2>
                <form id="add-event-form">
                    <div style="margin-bottom:1em;">
                        <label for="event-title">Titre</label><br>
                        <input type="text" id="event-title" required>
                    </div>
                    <div style="margin-bottom:1em;">
                        <label for="event-date">Date</label><br>
                        <input type="date" id="event-date" required>
                    </div>
                    <button type="submit" class="btn">Ajouter</button>
                </form>
            </div>
        </div>

        <div id="event-details-modal" class="modal" style="display:none;">
            <div class="modal-content" style="max-width:350px;">
                <span class="close" onclick="closeEventDetailsModal()" style="float:right;cursor:pointer;">&times;</span>
                <h2>Détails de l'événement</h2>
                <div id="event-details-content">
                <!-- Contenu dynamique ici -->
                </div>
            </div>
            </div>

    </section>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="../../actions/script.js"></script>

<script>
    // Données PHP transformées en objets JS
    const livraisonsDetails = <?php
        $data = [];
        foreach ($livraisons as $livraison) {
            $livraison_id = $livraison['id'];
            $data[$livraison_id] = $livraison;
            $data[$livraison_id]['articles'] = $articles_par_livraison[$livraison_id] ?? [];
        }
        echo json_encode($data);
    ?>;

    const commandesDetails = {
        <?php foreach ($commandes as $commande): ?>
        "<?= $commande['id'] ?>": {
            id: "<?= $commande['id'] ?>",
            date_prevue_envoi: "<?= $commande['date_prevue_envoi'] ?>",
            etat_preparation: "<?= addslashes($commande['etat_preparation']) ?>",
            lots: [
                <?php 
                if (!empty($commande['lots'])) {
                    foreach ($commande['lots'] as $lot) {
                        $articles = $lot['articles'] ?? [];
                        ?>
                        {
                            lot_id: "<?= $lot['lot_id'] ?>",
                            quantite_lot_commande: "<?= $lot['quantite_lot_commande'] ?>",
                            date_prevue_envoi: "<?= $lot['date_prevue_envoi'] ?>",
                            etat_lot: "<?= addslashes($lot['etat_lot']) ?>",
                            articles: [
                                <?php foreach ($articles as $article): ?>
                                {
                                    article_id: "<?= $article['article_id'] ?? '' ?>",
                                    nom_article: "<?= addslashes($article['nom_article'] ?? '') ?>",
                                    quantite: "<?= $article['quantite'] ?? '' ?>"
                                },
                                <?php endforeach; ?>
                            ]
                        },
                        <?php
                    }
                }
                ?>
            ]
        },
        <?php endforeach; ?>
    };

    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?= json_encode($events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                const eventId = info.event.id;

                if (eventId.startsWith('livraison-')) {
                    const livraisonId = eventId.replace('livraison-', '');
                    const livraison = livraisonsDetails[livraisonId];
                    if (!livraison) return alert('Détails de la livraison introuvables.');

                    let html = `<h3>Livraison #${livraison.numero_livraison}</h3>`;
                    html += `<p><strong>Date de livraison :</strong> ${livraison.date_livraison}</p>`;
                    html += `<p><strong>Date de réception :</strong> ${livraison.date_reception}</p>`;
                    html += `<p><strong>Fournisseur :</strong> ${livraison.fournisseur_nom || 'N/A'}</p>`;
                    html += `<p><strong>État :</strong> ${livraison.etat}</p>`;
                    html += `<p><strong>Commentaire :</strong> ${livraison.commentaire || 'Aucun'}</p>`;

                    if (Array.isArray(livraison.articles) && livraison.articles.length > 0) {
                        html += '<h4>Articles reçus :</h4><ul>';
                        livraison.articles.forEach(article => {
                            html += `<li>${article.article_nom} - Quantité: ${article.quantite}</li>`;
                        });
                        html += '</ul>';
                    } else {
                        html += '<p>Aucun article enregistré pour cette livraison.</p>';
                    }

                    document.getElementById('event-details-content').innerHTML = html;
                    document.getElementById('event-details-modal').style.display = 'flex';

                } else if (eventId.startsWith('commande-')) {
                    const commandeId = eventId.replace('commande-', '');
                    const commande = commandesDetails[commandeId];
                    if (!commande) return alert('Détails de la commande introuvables.');

                    let html = `<h3>Commande #${commande.id}</h3>`;
                    html += `<p><strong>Date prévue d'envoi :</strong> ${commande.date_prevue_envoi}</p>`;
                    html += `<p><strong>État de préparation :</strong> ${commande.etat_preparation}</p>`;

                    if (Array.isArray(commande.lots) && commande.lots.length > 0) {
                        html += '<h4>Lots :</h4>';
                        commande.lots.forEach(lot => {
                            html += `<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">`;
                            html += `<p><strong>Lot n°</strong> ${lot.lot_id}</p>`;
                            html += `<p><strong>Quantité commandée :</strong> ${lot.quantite_lot_commande}</p>`;
                            html += `<p><strong>Date prévue d'envoi du lot :</strong> ${lot.date_prevue_envoi}</p>`;
                            html += `<p><strong>État du lot :</strong> ${lot.etat_lot}</p>`;
                            html += `</div>`;
                        });
                    } else {
                        html += '<p>Aucun lot enregistré pour cette commande.</p>';
                    }

                    document.getElementById('event-details-content').innerHTML = html;
                    document.getElementById('event-details-modal').style.display = 'flex';

                } else {
                    alert('Détails non disponibles pour cet événement.');
                }
            }
        });
        calendar.render();

        // Modal add event
        document.getElementById('add-event-btn').onclick = function() {
            document.getElementById('add-event-modal').style.display = 'flex';
        };

        document.getElementById('add-event-form').onsubmit = function(e) {
            e.preventDefault();
            const title = document.getElementById('event-title').value.trim();
            const date = document.getElementById('event-date').value;
            if (title && date) {
                fetch('../../actions/ajouter_event.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'title=' + encodeURIComponent(title) + '&date=' + encodeURIComponent(date)
                })
                .then(response => response.text())
                .then(result => {
                    if (result === 'ok') {
                        calendar.addEvent({ title, start: date });
                        closeAddEventModal();
                        document.getElementById('add-event-form').reset();
                    } else {
                        alert('Erreur lors de l\'ajout de l\'événement.');
                    }
                });
            }
        };

        // Fermer les modals avec un clic sur les boutons (prévoir les boutons avec ces IDs dans ton HTML)
        document.getElementById('close-add-event-modal').onclick = closeAddEventModal;
        document.getElementById('close-event-details-modal').onclick = closeEventDetailsModal;
    });

    function closeAddEventModal() {
        document.getElementById('add-event-modal').style.display = 'none';
    }

    function closeEventDetailsModal() {
        document.getElementById('event-details-modal').style.display = 'none';
    }
</script>

</body>
</html>