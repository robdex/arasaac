import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const formTraduzione = document.getElementById('formTraduzione');
    const testoInput = document.getElementById('testoInput');
    const risultatoTraduzione = document.getElementById('risultatoTraduzione');
    const generaCasuale = document.getElementById('generaCasuale');

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

            const img = document.createElement('img');
            img.src = risultato.immagine;
            img.alt = risultato.parola;
            img.title = risultato.parola;
            img.width = 100;
            divParola.appendChild(img);

            risultatoTraduzione.appendChild(divParola);
        });
    }
});
