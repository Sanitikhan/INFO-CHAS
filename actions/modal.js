document.querySelector('.form-fournisseur').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            showModal('modal-success');
            form.reset(); // Réinitialise les champs du formulaire
        } else {
            showModal('modal-error');
        }
    })
    .catch(error => {
        console.error('Erreur réseau :', error);
        showModal('modal-error');
    });
});

function showModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'block';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 3000);
}

function openModal(id) {
    document.getElementById(id).style.display = 'block';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
