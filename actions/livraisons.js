// Voir les détails d'une livraison
function voirDetails(livraisonId) {
    fetch(`api/livraisons_api.php?action=details&id=${livraisonId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                afficherDetails(data.livraison, data.details);
            } else {
                alert('Erreur lors du chargement des détails');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur de connexion');
        });
}

function afficherDetails(livraison, details) {
    const modal = document.getElementById('modal-details');
    const content = document.getElementById('details-content');
    
    let html = `
        <h2>Livraison ${livraison.numero_livraison}</h2>
        <div class="details-info">
            <p><strong>Fournisseur:</strong> ${livraison.fournisseur_nom}</p>
            <p><strong>Date prévue:</strong> ${formatDate(livraison.date_prevue)}</p>
            <p><strong>Statut:</strong> <span class="statut-badge statut-${livraison.statut}">${livraison.statut}</span></p>
            <p><strong>Transporteur:</strong> ${livraison.transporteur || 'Non spécifié'}</p>
            ${livraison.notes ? `<p><strong>Notes:</strong> ${livraison.notes}</p>` : ''}
        </div>
        
        <h3>Détails des lots</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Lot</th>
                    <th>Produit</th>
                    <th>Qté attendue</th>
                    <th>Qté reçue</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    details.forEach(detail => {
        const statutLot = detail.quantite_recue >= detail.quantite_attendue ? 'complet' : 
                         detail.quantite_recue > 0 ? 'partiel' : 'attente';
        
        html += `
            <tr>
                <td>${detail.lot_numero}</td>
                <td>${detail.produit_nom}</td>
                <td>${detail.quantite_attendue}</td>
                <td>${detail.quantite_recue}</td>
                <td><span class="lot-statut lot-${statutLot}">${statutLot}</span></td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    content.innerHTML = html;
    modal.style.display = 'block';
}

// Supprimer une livraison
function supprimerLivraison(livraisonId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette livraison ?')) {
        fetch('api/livraisons_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: livraisonId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page ou supprimer la ligne
                location.reload();
            } else {
                alert('Erreur lors de la suppression: ' + data.message);
            }
        });
    }
}

// Fermer la modal
function fermerModal() {
    document.getElementById('modal-details').style.display = 'none';
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('modal-details');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Fonctions utilitaires
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

// Actualisation automatique toutes les 30 secondes
setInterval(() => {
    // Actualiser seulement les statuts sans recharger la page
    updateStatuts();
}, 30000);
/*
function updateStatuts() {
    fetch('api/livraisons_api.php?action=statuts')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.statuts.forEach(statut => {
                    const row = document.querySelector(`tr[data-id="${statut.id}"]`);
                    if (row) {
                        const badge = row.querySelector('.statut-badge');
                        badge.className = `statut-badge statut-${statut.statut}`;
                        badge.textContent = statut.statut;
                    }
                });
            }
        });
}*/