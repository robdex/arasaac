<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class SearchArasaac extends Command
{
    protected $signature = 'arasaac:search 
                            {parola : La parola da cercare}
                            {--lang=it : La lingua per la ricerca (default: it)}
                            {--limit=5 : Il numero massimo di risultati da mostrare (default: 5)}
                            {--best : Usa la ricerca ottimizzata}';
    
    protected $description = 'Cerca un pittogramma su Arasaac';

    protected $pythonPath;

    public function __construct()
    {
        parent::__construct();
        $this->pythonPath = base_path('python_env/bin/python');
    }

    public function handle()
    {
        $parola = $this->argument('parola');
        $lang = $this->option('lang') ?? 'it';
        $limit = $this->option('limit') ?? 5;
        $useBestSearch = $this->option('best');

        $risultati = $this->cercaParola($parola, $lang, $useBestSearch);

        if (empty($risultati)) {
            $this->info("Nessun risultato trovato per '{$parola}'. Provo con il lemma...");
            $lemma = $this->trovaLemma($parola);
            if ($lemma !== $parola) {
                $risultati = $this->cercaParola($lemma, $lang, $useBestSearch);
                if (!empty($risultati)) {
                    $this->info("Risultati trovati per il lemma '{$lemma}':");
                } else {
                    $this->error("Nessun risultato trovato anche per il lemma '{$lemma}'.");
                    return;
                }
            } else {
                $this->error("Non è stato possibile trovare un lemma diverso per '{$parola}'.");
                return;
            }
        } else {
            $this->info("Risultati per '{$parola}':");
        }

        $this->info("Usando " . ($useBestSearch ? "ricerca ottimizzata" : "ricerca standard"));
        $this->mostraRisultati($risultati, $limit);
    }

    protected function cercaParola($parola, $lang, $useBestSearch)
    {
        $endpoint = $useBestSearch ? 'bestsearch' : 'search';
        $url = "https://api.arasaac.org/v1/pictograms/{$lang}/{$endpoint}/{$parola}";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return [];
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

    protected function mostraRisultati($risultati, $limit)
    {
        foreach (array_slice($risultati, 0, $limit) as $index => $risultato) {
            $this->line("---");
            $this->line("Risultato " . ($index + 1) . ":");
            $this->line("ID: " . $risultato['_id']);
            $this->line("Parola chiave: " . $risultato['keywords'][0]['keyword']);
            $this->line("URL immagine: https://static.arasaac.org/pictograms/" . $risultato['_id'] . "/" . $risultato['_id'] . "_300.png");
        }
    }
}
