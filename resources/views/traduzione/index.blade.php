@extends('layouts.app')

@section('content')
<h1>Traduttore Magico: Italiano - CAA</h1>
<div class="form-container">
    <form id="formTraduzione">
        <div class="form-group">
            <label for="testoInput">Scrivi qui la tua frase magica:</label>
            <textarea id="testoInput" rows="3" required></textarea>
        </div>
        <div class="button-group">
            <button type="submit">Trasforma in Immagini!</button>
            <button type="button" id="generaCasuale">Frase a Sorpresa</button>
        </div>
    </form>
</div>
<div id="risultatoTraduzione"></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formTraduzione = document.getElementById('formTraduzione');
        const testoInput = document.getElementById('testoInput');
        const risultatoTraduzione = document.getElementById('risultatoTraduzione');
        const generaCasuale = document.getElementById('generaCasuale');

        formTraduzione.addEventListener('submit', async function(e) {
            e.preventDefault();
            await traduci(testoInput.value);
        });

        generaCasuale.addEventListener('click', async function() {
            const response = await fetch('/genera-casuale');
            const data = await response.json();
            testoInput.value = data.frase;
            await traduci(data.frase);
        });

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
</script>
@endpush
@endsection
