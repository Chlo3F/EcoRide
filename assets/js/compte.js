document.addEventListener('DOMContentLoaded', () => {
    initAjaxForms();
    initToggleConducteurPassager();
    initFormValidation();
});

function initAjaxForms() {
    const forms = document.querySelectorAll('form[data-url]');
    forms.forEach(attachAjaxSubmit);
}

function attachAjaxSubmit(form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const url = form.dataset.url;
        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) throw new Error('Erreur serveur');
            return res.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newForm = doc.querySelector('form');

            if (newForm && form.parentElement) {
                form.parentElement.replaceChild(newForm, form);
                attachAjaxSubmit(newForm);
                initFormValidation(); // réinit validation sur nouveau form
            }

            const message = doc.querySelector('.form-result');
            if (message) {
                const resultContainer = document.getElementById(form.id + '-result');
                if (resultContainer) {
                    resultContainer.innerHTML = message.innerHTML;
                    resultContainer.classList.add('fade-in');
                    setTimeout(() => resultContainer.classList.remove('fade-in'), 2000);
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erreur lors de la soumission du formulaire.');
        });
    });
}

function initToggleConducteurPassager() {
    const conducteurRadio = document.getElementById('conducteur');
    const passagerRadio = document.getElementById('passager');

    const conducteurSection = document.getElementById('formulaires-conducteur');
    const passagerSection = document.getElementById('passager-section');

    if (!conducteurRadio || !passagerRadio || !conducteurSection || !passagerSection) {
        return;
    }

    function toggleSections(type) {
        if (type === 'conducteur') {
            conducteurSection.style.display = 'block';
            passagerSection.style.display = 'none';
        } else if (type === 'passager') {
            conducteurSection.style.display = 'none';
            passagerSection.style.display = 'block';
        }
    }

    conducteurRadio.addEventListener('change', () => toggleSections('conducteur'));
    passagerRadio.addEventListener('change', () => toggleSections('passager'));

    // Initial display state
    if (conducteurRadio.checked) toggleSections('conducteur');
    else if (passagerRadio.checked) toggleSections('passager');
}

function initFormValidation() {
    // Validation pour trajetForm
    const trajetForm = document.querySelector('form#trajetForm');
    if (trajetForm) {
        const inputs = {
            vehicule: trajetForm.querySelector('[name$="[vehicule]"]'),
            villeDepart: trajetForm.querySelector('[name$="[villeDepart]"]'),
            villeArrivee: trajetForm.querySelector('[name$="[villeArrivee]"]'),
            dateHeureDepart: trajetForm.querySelector('[name$="[dateHeureDepart]"]'),
            placesDisponibles: trajetForm.querySelector('[name$="[placesDisponibles]"]'),
            credits: trajetForm.querySelector('[name$="[credits]"]')
        };

        Object.entries(inputs).forEach(([key, input]) => {
            if (!input) return;

            input.addEventListener('change', () => {
                validateInput(key, input);
            });
        });
    }

    // Validation pour vehiculeForm
    const vehiculeForm = document.querySelector('form#vehiculeForm');
    if (vehiculeForm) {
        const inputs = {
            marque: vehiculeForm.querySelector('[name$="[marque]"]'),
            modele: vehiculeForm.querySelector('[name$="[modele]"]'),
            couleur: vehiculeForm.querySelector('[name$="[couleur]"]'),
            immatriculation: vehiculeForm.querySelector('[name$="[immatriculation]"]'),
            datePremiereImmat: vehiculeForm.querySelector('[name$="[datePremiereImmat]"]'),
            nbrPlaces: vehiculeForm.querySelector('[name$="[nbrPlaces]"]'),
        };

        Object.entries(inputs).forEach(([key, input]) => {
            if (!input) return;

            input.addEventListener('change', () => {
                validateInput(key, input);
            });
        });
    }
}

function validateInput(name, input) {
    clearValidation(input);

    let valid = true;
    let message = '';

    const val = input.value.trim();

    switch(name) {
        case 'vehicule':
        case 'villeDepart':
        case 'villeArrivee':
        case 'marque':
        case 'modele':
        case 'couleur':
            if (val === '') {
                valid = false;
                message = 'Ce champ est obligatoire.';
            }
            break;

        case 'dateHeureDepart':
        case 'datePremiereImmat':
            if (val === '') {
                valid = false;
                message = 'Date requise.';
            } else if (isNaN(Date.parse(val))) {
                valid = false;
                message = 'Date invalide.';
            }
            break;

        case 'placesDisponibles':
        case 'credits':
        case 'nbrPlaces':
            if (val === '') {
                valid = false;
                message = 'Ce champ est obligatoire.';
            } else if (!/^\d+$/.test(val) || parseInt(val) <= 0) {
                valid = false;
                message = 'Doit être un nombre entier positif.';
            }
            break;

        case 'immatriculation':
            // Exemple simple de validation immatriculation française
            if (val === '') {
                valid = false;
                message = 'L\'immatriculation est obligatoire.';
            } else if (!/^[A-Z0-9\- ]{1,10}$/i.test(val)) {
                valid = false;
                message = 'Immatriculation invalide.';
            }
            break;

        default:
            break;
    }

    if (!valid) {
        input.classList.add('is-invalid');
        showErrorMessage(input, message);
    } else {
        input.classList.add('is-valid');
    }
}

function clearValidation(input) {
    input.classList.remove('is-invalid');
    input.classList.remove('is-valid');
    const next = input.nextElementSibling;
    if (next && next.classList.contains('invalid-feedback')) {
        next.remove();
    }
}

function showErrorMessage(input, message) {
    const error = document.createElement('div');
    error.className = 'invalid-feedback';
    error.innerText = message;
    input.insertAdjacentElement('afterend', error);
}
