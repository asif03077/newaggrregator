<?php
// app/Http/Controllers/ArticleController.php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    
    // Show a list of all articles
    public function index(Request $request)
    {
        $type = $request->input('type');
        $sources = collect(); 
        if($type && $type != 'all'){

            $source = Source::where('type',$request->type)->get();  
            
            $user = Auth::user();
            
            $preferences = $user->preference;
                // Check if user has preferred categories
                $preferredSources = json_decode($preferences->preferred_sources, true);
                if ($preferredSources) {
                    
                    // Return only the categories that are in the user's preferences
                     $sources = $source->whereIn('id', $preferredSources);
                }else{
                    $sources = $source;
                }
              
        }else{
            $sources = Source::get();
        }
        
        return response()->json($sources);
    }

   
}

?>