@extends('layouts.app')

@section('content')
<div class="form-container">
    <form id="formTraduzione">
        <div class="form-group">
            <label for="testoInput">Scrivi qui la tua frase magica:</label>
            <textarea id="testoInput" rows="3" required></textarea>
        </div>
        <div class="button-group">
            <button type="button" id="generaCasuale" class="btn-secondary">Frase a Sorpresa</button>
            <button type="submit" class="btn-primary">Trasforma in Immagini!</button>
        </div>
    </form>
</div>
<div id="risultatoTraduzione"></div>
@endsection
