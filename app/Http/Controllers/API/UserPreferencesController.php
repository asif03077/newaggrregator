<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferencesController extends Controller
{
    // Get the current user's preferences
    public function getPreferences()
    {
        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->first();

        if (!$preferences) {
            return response()->json(['message' => 'No preferences found'], 404);
        }

        return response()->json($preferences);
    }

    // Set or update the current user's preferences
    public function setPreferences(Request $request)
    {   
        $user = Auth::user();
        
        // dd($request->all());
        UserPreference::where('user_id' , $user->id)->delete();
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],  // match on user ID
            $request->only(['preferred_sources', 'preferred_categories', 'preferred_authors'])  // update these fields
        );

        return response()->json($preferences);
    }
}




