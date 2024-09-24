<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source; 
use Carbon\Carbon;

class ScrapeNewYorkTimesData extends Command
{
    protected $signature = 'scrape:newyorkapi';

    protected $description = 'Fetch top stories from the New York Times and save them to the database';
    
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {   
        $this->info('Fetching newyorkTimes...');
        $apiKey = config('services.nytimesapi.key');
        $url = "https://api.nytimes.com/svc/topstories/v2/home.json?api-key={$apiKey}";
        $response = Http::get($url);

        if ($response->successful()) {
           
            $stories = $response->json()['results'];
            // Fetch or create the NYT source entry
            $source = Source::firstOrCreate([
                'name' => 'New York Times', 
                'source_id' => 'new-york-times', 
                'url' => 'https://www.nytimes.com',
                'type' => 'New York Times'
            ]);


            foreach ($stories as $story) {
                
                $catInsertId = Category::updateOrCreate(
                    [
                    'name' => $story['section'],
                    'type' => 'New York Times'
                    ]
                );
                $authorInsertId = Author::updateOrCreate(
                    [
                    'name' => $story['byline'],
                    'type' => 'New York Times'
                    ]
                );
                // Save each story to the `article` table
                Article::updateOrCreate(
                    [
                        'source_id' => $source->id ?? null,
                        'author_id' => $authorInsertId->id ?? null,
                        'category_id' => $catInsertId->id ?? null,
                        'title' => $story['title'],
                        'description' => $story['abstract'],
                        'url' => $story['url'],
                        // 'author' => $story['byline'],
                        'type' => 'New York Times',
                        'urlToImage' => null,
                        'published_at' => Carbon::parse($story['published_date'])->format('Y-m-d H:i:s') ?? null,
                    ]
                );
            }

            $this->info('Top stories updated successfully.');
        } else {
            $this->error('Failed to fetch top stories.');
        }
    }
}
