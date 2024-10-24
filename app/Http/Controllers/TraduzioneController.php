<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class TraduzioneController extends Controller
{
    protected $pythonPath;

    public function __construct()
    {
        $this->pythonPath = base_path('python_env/bin/python');
    }

    public function index(Request $request)
    {
        $imageSize = $request->session()->get('image_size', 300);
        return view('traduzione.index', compact('imageSize'));
    }

    public function traduci(Request $request)
    {
        $testo = $request->input('testo');
        $parole = explode(' ', $testo);
        $risultati = [];
        $imageSize = $request->session()->get('image_size', 300);

        foreach ($parole as $parola) {
            $risultato = $this->cercaParola($parola);
            if (empty($risultato)) {
                $lemma = $this->trovaLemma($parola);
                if ($lemma !== $parola) {
                    $risultato = $this->cercaParola($lemma);
                }
            }
            if (!empty($risultato)) {
                $immagineUrl = $this->getImmagineUrl($risultato[0]['_id'], $imageSize);
                $risultati[] = [
                    'parola' => $parola,
                    'immagine' => $immagineUrl,
                    'size' => $imageSize
                ];
            } else {
                $risultati[] = [
                    'parola' => $parola,
                    'immagine' => null,
                    'size' => $imageSize
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

    protected function getImmagineUrl($id, $size)
    {
        $localPath = "images/arasaac/{$id}_{$size}.png";
        $fullPath = public_path($localPath);

        if (!file_exists($fullPath)) {
            $url = "https://static.arasaac.org/pictograms/{$id}/{$id}_{$size}.png";
            $imageContent = file_get_contents($url);
            if ($imageContent !== false) {
                file_put_contents($fullPath, $imageContent);
            } else {
                return $url; // Fallback all'URL originale se il download fallisce
            }
        }

        return asset($localPath);
    }

    public function generaCasuale()
    {
        $frasi = config('frasi_casuali');
        $fraseCasuale = $frasi[array_rand($frasi)];

        return response()->json(['frase' => $fraseCasuale]);
    }

    public function clearResults()
    {
        $risultatoTraduzione = '';
        return response()->json(['success' => true]);
    }

    public function setImageSize(Request $request)
    {
        $size = $request->input('size');
        if (in_array($size, [300, 500])) {
            $request->session()->put('image_size', $size);
            
            // Se c'è un testo fornito, ritraduce immediatamente
            if ($request->has('testo')) {
                return $this->traduci($request);
            }
            
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
}
