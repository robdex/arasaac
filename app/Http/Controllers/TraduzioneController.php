<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TraduzioneController extends Controller
{
    public function index()
    {
        return view('traduzione.index');
    }

    public function traduci(Request $request)
    {
        $testo = $request->input('testo');
        $parole = explode(' ', $testo);
        $risultati = [];

        foreach ($parole as $parola) {
            $risposta = Http::get("https://api.arasaac.org/v1/pictograms/it/search/{$parola}");
            $dati = $risposta->json();

            if (!empty($dati)) {
                $id = $dati[0]['_id'];
                $risultati[] = [
                    'parola' => $parola,
                    'immagine' => "https://static.arasaac.org/pictograms/{$id}/{$id}_300.png"
                ];
            }
        }

        return response()->json($risultati);
    }

    public function generaCasuale()
    {
        $frasi = [
            "Il sole splende nel cielo azzurro",
            "I bambini giocano nel parco",
            "Il gatto dorme sul divano",
            "La mamma cucina una torta deliziosa",
            "Il treno parte dalla stazione"
        ];

        $fraseCasuale = $frasi[array_rand($frasi)];

        return response()->json(['frase' => $fraseCasuale]);
    }
}
