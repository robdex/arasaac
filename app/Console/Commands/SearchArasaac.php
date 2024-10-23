<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SearchArasaac extends Command
{
    protected $signature = 'arasaac:search 
                            {parola : La parola da cercare}
                            {--lang=it : La lingua per la ricerca (default: it)}
                            {--limit=5 : Il numero massimo di risultati da mostrare (default: 3)}
                            {--best : Usa la ricerca ottimizzata}';
    
    protected $description = 'Cerca un pittogramma su Arasaac';

    public function handle()
    {
        $parola = $this->argument('parola');
        $lang = $this->option('lang') ?? 'it';
        $limit = $this->option('limit') ?? 3;
        $useBestSearch = $this->option('best');

        $endpoint = $useBestSearch ? 'bestsearch' : 'search';
        $url = "https://api.arasaac.org/v1/pictograms/{$lang}/{$endpoint}/{$parola}";

        $response = Http::get($url);

        if ($response->successful()) {
            $risultati = $response->json();
            
            if (empty($risultati)) {
                $this->info("Nessun risultato trovato per '{$parola}'.");
                return;
            }

            $this->info($useBestSearch ? "Risultato migliore per '{$parola}':" : "Risultati per '{$parola}' (mostrando i primi {$limit}):");
            $this->info("Usando " . ($useBestSearch ? "ricerca ottimizzata" : "ricerca standard"));
            foreach (array_slice($risultati, 0, $limit) as $index => $risultato) {
                $this->line("---");
                $this->line("Risultato " . ($index + 1) . ":");
                $this->line("ID: " . $risultato['_id']);
                $this->line("Parola chiave: " . $risultato['keywords'][0]['keyword']);
                $this->line("URL immagine: https://static.arasaac.org/pictograms/" . $risultato['_id'] . "/" . $risultato['_id'] . "_300.png");
            }
        } else {
            $this->error("Errore nella richiesta: " . $response->status());
        }
    }
}
