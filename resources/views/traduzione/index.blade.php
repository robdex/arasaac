@extends('layouts.app')

@section('content')
<div class="form-container">
    <div class="header-row">
        <h2>SCRIVI QUI SOTTO LA TUA FRASE MAGICA</h2>
        <div class="switcher">
            <label class="switch">
                <input type="checkbox" id="imageSizeToggle" {{ $imageSize == 500 ? 'checked' : '' }}>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <form id="formTraduzione">
        <div class="form-group">
            <textarea id="testoInput" rows="3" required></textarea>
        </div>
        <div class="button-group">
            <button type="button" id="generaCasuale" class="btn-secondary">FRASE A SORPRESA</button>
            <button type="submit" class="btn-primary">TRASFORMA IN IMMAGINI</button>
        </div>
    </form>
</div>
<div id="risultatoTraduzione"></div>
@endsection
