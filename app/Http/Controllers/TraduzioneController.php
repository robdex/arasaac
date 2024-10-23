<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class TraduzioneController extends Controller
{
    protected $pythonPath;

    public function __construct()
    {
        $this->pythonPath = base_path('python_env/bin/python');
    }

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
            $risultato = $this->cercaParola($parola);
            if (empty($risultato)) {
                $lemma = $this->trovaLemma($parola);
                if ($lemma !== $parola) {
                    $risultato = $this->cercaParola($lemma);
                }
            }
            if (!empty($risultato)) {
                $risultati[] = [
                    'parola' => $parola,
                    'immagine' => "https://static.arasaac.org/pictograms/" . $risultato[0]['_id'] . "/" . $risultato[0]['_id'] . "_300.png"
                ];
            } else {
                $risultati[] = [
                    'parola' => $parola,
                    'immagine' => null
                ];
            }
        }

        return response()->json($risultati);
    }

    protected function cercaParola($parola)
    {
        $response = Http::get("https://api.arasaac.org/v1/pictograms/it/search/{$parola}");
        return $response->successful() ? $response->json() : [];
    }

    protected function trovaLemma($parola)
    {
        $process = new Process([$this->pythonPath, '-c', "
import spacy
nlp = spacy.load('it_core_news_sm')
doc = nlp('$parola')
print(doc[0].lemma_)
"]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return trim($process->getOutput());
    }

    public function generaCasuale()
    {
        $frasi = config('frasi_casuali');
        $fraseCasuale = $frasi[array_rand($frasi)];

        return response()->json(['frase' => $fraseCasuale]);
    }
}
