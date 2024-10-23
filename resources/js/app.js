import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const formTraduzione = document.getElementById('formTraduzione');
    const testoInput = document.getElementById('testoInput');
    const risultatoTraduzione = document.getElementById('risultatoTraduzione');
    const generaCasuale = document.getElementById('generaCasuale');

    // Aggiungi stili per la textarea e il pulsante di cancellazione
    const style = document.createElement('style');
    style.textContent = `
        #testoInput {
            height: 150px; /* Aumentato del 50% da 100px */
            font-size: 18px; /* Aumenta la dimensione del carattere */
            padding-right: 30px; /* Spazio per il pulsante di cancellazione */
        }
        .input-wrapper {
            position: relative;
        }
        .clear-button {
            position: absolute;
            right: 10px;
            top: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);

    // Avvolgi la textarea in un div e aggiungi il pulsante di cancellazione
    const wrapper = document.createElement('div');
    wrapper.className = 'input-wrapper';
    testoInput.parentNode.insertBefore(wrapper, testoInput);
    wrapper.appendChild(testoInput);

    const clearButton = document.createElement('button');
    clearButton.innerHTML = '&times;';
    clearButton.className = 'clear-button';
    clearButton.type = 'button';
    wrapper.appendChild(clearButton);

    clearButton.addEventListener('click', function() {
        testoInput.value = '';
        testoInput.focus();
    });

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
