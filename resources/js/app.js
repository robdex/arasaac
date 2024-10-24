import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const formTraduzione = document.getElementById('formTraduzione');
    const testoInput = document.getElementById('testoInput');
    const risultatoTraduzione = document.getElementById('risultatoTraduzione');
    const generaCasuale = document.getElementById('generaCasuale');

    // Aggiungi Font Awesome
    const fontAwesome = document.createElement('link');
    fontAwesome.rel = 'stylesheet';
    fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    document.head.appendChild(fontAwesome);

    // Aggiungi il pulsante di cancellazione
    const clearButton = document.createElement('button');
    clearButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
    clearButton.className = 'clear-button';
    clearButton.type = 'button';
    clearButton.setAttribute('aria-label', 'Cancella testo');
    
    const formContainer = document.createElement('div');
    formContainer.className = 'form-container';
    testoInput.parentNode.insertBefore(formContainer, testoInput);
    formContainer.appendChild(testoInput);
    formContainer.appendChild(clearButton);

    clearButton.addEventListener('click', function() {
        testoInput.value = '';
        risultatoTraduzione.innerHTML = '';
        testoInput.focus();
        
        // Chiamata AJAX per cancellare i risultati sul server
        fetch('/clear-results', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });
    });

    // Aggiungi icone agli altri pulsanti
    const submitButton = formTraduzione.querySelector('button[type="submit"]');
    submitButton.innerHTML += '<i class="fas fa-magic icon"></i>';

    if (generaCasuale) {
        generaCasuale.innerHTML += '<i class="fas fa-dice icon"></i>';
    }

    if (formTraduzione) {
        formTraduzione.addEventListener('submit', async function(e) {
            e.preventDefault();
            await traduci(testoInput.value);
        });
    }

    if (generaCasuale) {
        generaCasuale.addEventListener('click', async function() {
            const response = await fetch('/genera-casuale');
            const data = await response.json();
            testoInput.value = data.frase;
            await traduci(data.frase);
        });
    }

    if (testoInput) {
        testoInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                formTraduzione.dispatchEvent(new Event('submit'));
            }
        });
    }

    async function traduci(testo) {
        risultatoTraduzione.innerHTML = '<p>Sto preparando le immagini magiche...</p>';
        const response = await fetch('/traduci', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ testo: testo })
        });
        const risultati = await response.json();
        
        risultatoTraduzione.innerHTML = '';
        risultati.forEach(risultato => {
            const divParola = document.createElement('div');
            divParola.className = 'parola-container';

            const span = document.createElement('span');
            span.textContent = risultato.parola;
            span.className = 'parola-testo';
            divParola.appendChild(span);

            if (risultato.immagine) {
                const img = document.createElement('img');
                img.src = risultato.immagine;
                img.alt = risultato.parola;
                img.title = risultato.parola;
                img.width = 100;
                divParola.appendChild(img);
            }

            risultatoTraduzione.appendChild(divParola);
        });
    }
});
