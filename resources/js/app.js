import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const formTraduzione = document.getElementById('formTraduzione');
    const testoInput = document.getElementById('testoInput');
    const risultatoTraduzione = document.getElementById('risultatoTraduzione');
    const generaCasuale = document.getElementById('generaCasuale');
    const imageSizeToggle = document.getElementById('imageSizeToggle');

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
    
    const formContainer = document.querySelector('.form-group');
    formContainer.style.position = 'relative';
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

    if (imageSizeToggle) {
        imageSizeToggle.addEventListener('change', function() {
            const size = this.checked ? 500 : 300;
            const testo = testoInput.value;
            
            fetch('/set-image-size', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ size: size, testo: testo })
            }).then(response => response.json())
              .then(data => {
                if (data.length > 0) {
                    // Se abbiamo ricevuto risultati, aggiorniamo le immagini
                    updateResults(data);
                }
            });
        });
    }

    async function traduci(testo) {
        risultatoTraduzione.innerHTML = '<p class="loading-message">STO PREPARANDO LE IMMAGINI MAGICHE...</p>';
        const response = await fetch('/traduci', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ testo: testo })
        });
        const risultati = await response.json();
        updateResults(risultati);
    }

    function updateResults(risultati) {
        const risultatoTraduzione = document.getElementById('risultatoTraduzione');
        risultatoTraduzione.innerHTML = '';
        risultati.forEach(risultato => {
            const divParola = document.createElement('div');
            divParola.className = 'parola-container';

            if (risultato.immagine) {
                const img = document.createElement('img');
                img.src = risultato.immagine;
                img.alt = risultato.parola;
                img.title = risultato.parola;
                img.className = 'thumbnail';
                // Imposta le dimensioni corrette in base alla size
                img.style.width = risultato.size == 300 ? '150px' : '250px';
                img.style.height = risultato.size == 300 ? '150px' : '250px';
                img.style.objectFit = 'cover';
                img.addEventListener('click', () => apriModale(risultato.immagine, risultato.parola, risultato.size));
                divParola.appendChild(img);
            }

            const span = document.createElement('span');
            span.textContent = risultato.parola;
            span.className = 'parola-testo';
            divParola.appendChild(span);

            risultatoTraduzione.appendChild(divParola);
        });
    }

    function apriModale(src, alt, size) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <img src="${src}" alt="${alt}" style="width:${size}px;height:${size}px;object-fit:contain;">
                <p>${alt.toUpperCase()}</p>
            </div>
        `;
        document.body.appendChild(modal);

        // Forza un reflow prima di aggiungere la classe 'show'
        modal.offsetWidth;
        modal.classList.add('show');

        modal.addEventListener('click', () => {
            modal.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 300); // Aspetta che la transizione finisca prima di rimuovere l'elemento
        });
    }
});
