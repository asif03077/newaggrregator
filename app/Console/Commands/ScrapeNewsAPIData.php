<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;

class ScrapeNewsAPIData extends Command
{
    protected $signature = 'scrape:newsapi';
    protected $description = 'Scrape news data from API and insert into database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch sources
        $this->info('Fetching sources...');
        $sourcesResponse = Http::get('https://newsapi.org/v2/top-headlines/sources', [
            'apiKey' => config('services.newsapi.key'),
        ]);
        $sources_res = $sourcesResponse->json('sources', []);
        $sources = array_slice($sources_res, 0, 30);
        $this->info('Sources updated successfully.');
        $this->info('Fetching articles...');
        foreach ($sources as $source) {
            Source::updateOrCreate(
                [
                    'source_id' => $source['id']?? null,
                    'name' => $source['name'],
                    'description' => $source['description'] ?? null,
                    'url' => $source['url'] ?? null,
                    'category' => $source['category'] ?? null,
                    'language' => $source['language'] ?? null,
                    'country' => $source['country'] ?? null,
                    'type' => 'News',
                ]
            );

            $lastInsertId = Category::updateOrCreate(
                [
                   'name' => $source['category'],
                   'type' => 'News'
                ]
            );
            $yesterday = Carbon::yesterday();
            $articlesResponse = Http::get('https://newsapi.org/v2/everything', [
                'q' => $source['name'],
                'page' => 1,
                'from' => $yesterday->toDateString(),
                'apiKey' => config('services.newsapi.key'),
            ]);
            $articles = $articlesResponse->json('articles', []);
            
            foreach ($articles as $article) {
                $insertedAuthor = Author::updateOrCreate(
                    [
                       'name' => $article['author'],
                       'type' => 'News'
                    ]
                );
                Article::updateOrCreate(
                    [
                        'source_id' => Source::where('source_id', $article['source']['id'])->first()->id ?? null,
                        'author_id' => $insertedAuthor->id ?? null,
                        'category_id' => $lastInsertId->id ?? null,
                        'title' => $article['title'] ?? null,
                        'description' => $article['description'] ?? null,
                        'url' => $article['url'] ?? null,
                        'urlToImage' => $article['urlToImage'] ?? null,
                        'category' => Source::where('source_id', $article['source']['id'])->first()->category ?? null,
                        'type' => 'News',
                        'published_at' => Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s') ?? null,
                    ]
                );

                
            }
        }
        


        $this->info('Articles updated successfully.');
    }
}
