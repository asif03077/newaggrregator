<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;

class ScrapeGuardianData extends Command
{
    protected $signature = 'scrape:guardianapi';
    protected $description = 'Fetch data from The Guardian API and save to articles table';

    public function handle()
    {
        $this->info('Fetching articles...');
        $apiKey = config('services.guardianapi.key'); // Your Guardian API key
        $url = 'https://content.guardianapis.com/search?show-fields=all&page=10&page-size=50&api-key=' . $apiKey;

        // Fetch data from The Guardian API
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $articles = $data['response']['results'];

            // Create or find the source
            $source = Source::firstOrCreate([
                'name' => 'The Guardian',
                'type' => 'The Guardian'
            ]);


            // Iterate through each article and save it to the database
            foreach ($articles as $articleData) {
                $catInsertId = Category::updateOrCreate(
                    [
                    'name' => $articleData['sectionName'],
                    'type' => 'The Guardian',
                    ]
                );
                $authorInsertId = Author::updateOrCreate(
                    [
                    'name' => $articleData['fields']['byline'] ?? 'Unknown',
                    'type' => 'The Guardian',
                    ]
                );
                Article::updateOrCreate(
                    [
                        'source_id' => $source->id ?? null,
                        'author_id' => $authorInsertId->id ?? null,
                        'category_id' => $catInsertId->id ?? null,
                        'title' => $articleData['webTitle'],
                        'description' => $articleData['fields']['trailText'] ?? null,
                        'url' => $articleData['webUrl'],
                        // 'author' => $articleData['fields']['byline'] ?? 'Unknown',
                        'urlToImage' => null,
                        'type' => 'The Guardian',
                        'category' => $articleData['sectionName'],
                        'published_at' =>Carbon::parse($articleData['webPublicationDate'])->format('Y-m-d H:i:s') ?? null,
                        
                    ]
                    
                );
            }

            $this->info('Articles have been successfully fetched and saved.');
        } else {
            $this->error('Failed to fetch data from The Guardian API.');
        }
    }
}
