<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    // Show a list of all articles
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $sourceId = $request->input('source_id');
        $categoryId = $request->input('category_id');
        $authorId = $request->input('author_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type');
        
        // Get the current authenticated user and their preferences
        $user = Auth::user();
        
        $preferences = $user->preference;
        // dd($preferences);    
        // Create a base query for fetching articles
        $articles = Article::with(['source', 'category', 'author']);
        
        // Apply filters from the request (if provided)
        $articles->when($sourceId, function($query, $sourceId) {
            return $query->where('source_id', $sourceId);
        });
    
        $articles->when($categoryId, function($query, $categoryId) {
            return $query->where('category_id', $categoryId);
        });
    
        $articles->when($authorId, function($query, $authorId) {
            return $query->where('author_id', $authorId);
        });
    
        $articles->when($startDate, function($query, $startDate) {
            return $query->whereDate('published_at', '=', $startDate);
        });
    
        $articles->when($endDate, function($query, $endDate) {
            return $query->whereDate('published_at', '<=', $endDate);
        });
    
        $articles->when($type, function($query, $type) {
            return $query->where('type', $type);
        });
       
        // Apply user preferences
        if ($preferences) {
            if (!empty($preferences->preferred_sources)) {
                $preferredSources = json_decode($preferences->preferred_sources, true);
                if (!empty($preferredSources)) {
                    $articles->whereIn('source_id', $preferredSources);
                }
            }
            
            
            if (!empty($preferences->preferred_categories)) {
                $preferredCategories = json_decode($preferences->preferred_categories, true);
               
                if (!empty($preferredCategories)) {
                    $articles->whereIn('category_id', $preferredCategories);
                }
            }
    
            if (!empty($preferences->preferred_authors)) {
                $preferredAuthors = json_decode($preferences->preferred_authors, true);
                if (!empty($preferredAuthors)) {
                    $articles->whereIn('author_id', $preferredAuthors);
                }
            }
        }
    
        // Apply keyword search but make sure it respects the preferences
        if ($keyword) {
            $articles->where(function($query) use ($keyword, $preferences) {
                $query->where('title', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
    
                // Apply preferences within keyword search if they exist
                if ($preferences) {
                    if (!empty($preferences->preferred_sources)) {
                        $preferredSources = json_decode($preferences->preferred_sources, true);
                        $query->whereIn('source_id', $preferredSources);
                    }
    
                    if (!empty($preferences->preferred_categories)) {
                        $preferredCategories = json_decode($preferences->preferred_categories, true);
                        $query->whereIn('category_id', $preferredCategories);
                    }
    
                    if (!empty($preferences->preferred_authors)) {
                        $preferredAuthors = json_decode($preferences->preferred_authors, true);
                        $query->whereIn('author_id', $preferredAuthors);
                    }
                }
            });
        }
        $articles->orderBy('published_at', 'desc');
        // Get the filtered and/or preference-based articles
        $filteredArticles = $articles->get();
    
        // Return the articles as a JSON response
        return response()->json($filteredArticles);
    }
    
    public  function user(){
        $user = Auth::user();
        return response()->json($user);
    }

    public function personalizedFeed()
    {
        $user = Auth::user();
        $preferences = $user->preference;  // Assuming relationship in User model

        $articles = Article::query();

        if ($preferences) {
            if ($preferences->preferred_sources) {
                $articles->whereIn('source', $preferences->preferred_sources);
            }

            if ($preferences->preferred_categories) {
                $articles->whereIn('category', $preferences->preferred_categories);
            }

            if ($preferences->preferred_authors) {
                $articles->whereIn('author', $preferences->preferred_authors);
            }
        }

        return response()->json($articles->get());
    }

    public function categories(Request $request){
        
        $type = $request->input('type');
        if($type && $type != 'all'){
            $categories = Category::where('type',$request->type)->get(); 
                $user = Auth::user();
                $preferences = $user->preference;
                // Check if user has preferred categories
                $preferredCategories = json_decode($preferences->preferred_categories, true);
                
                if ($preferredCategories) {
                    // Return only the categories that are in the user's preferences
                    $categories = $categories->whereIn('id', $preferredCategories);
                }
        }else{
            $categories = Category::get();
        }
       

        

        
        return response()->json($categories);
    }
    public function authors(Request $request){
        $type = $request->input('type');
        if($type && $type != 'all'){
            $authors = Author::where('type',$request->type)->get();  
        }else{
            $authors = Author::get();
        }
        return response()->json($authors);
    }

   
}

?>