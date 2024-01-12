<?php

// app/Http/Controllers/ShortUrlController.php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ShortUrlController extends Controller
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = 'AIzaSyAo3LWdvqexXe3Nxxuj5SFqA4ClPBLIyew';
    }

    public function createShortUrl(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
        ]);

        $originalUrl = $request->input('original_url');
        $isSafe = $this->checkSafeBrowsing($originalUrl);
        if (!$isSafe) {
            return response()->json(['error' => 'Unsafe URL'], 500);
        }
        $shortCode = $this->generateShortCode();
        $existingUrl = ShortUrl::where('original_url', $originalUrl)->first();
        if ($existingUrl) {
            return response()->json(['is_new' =>0,'short_url' => url("/{$existingUrl->short_code}")]);
        }

        // Check against Google Safe Browsing API
       
       

        $shortUrl = ShortUrl::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        return response()->json(['is_new' =>1,'short_url' => url("/{$shortUrl->short_code}")]);
    }

    public function redirectShortUrl($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if ($shortUrl) {
            return redirect($shortUrl->original_url);
        }

        abort(404);
    }

    private function generateShortCode()
    {
        // Generate a 6-character alphanumeric code
        return substr(md5(uniqid()), 0, 6);
    }

    private function checkSafeBrowsing($url)
    {
        $client = new Client();
        try {
            $response = $client->post('https://safebrowsing.googleapis.com/v4/threatMatches:find', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'client' => [
                        'clientId' => '176858900795-8r2tkqoclal6bma178291hsdip2m34go.apps.googleusercontent.com',
                        'clientVersion' => '1.5.2',
                    ],
                    'threatInfo' => [
                        'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                        'platformTypes' => ['ANY_PLATFORM'],
                        'threatEntryTypes' => ['URL'],
                        'threatEntries' => [
                            ['url' => $url],
                        ],
                    ],
                ],
            ]);
            return true;          
        } catch (\Exception $e) {
            //\Log::error('Error making Safe Browsing API request: ' . $e->getMessage());
            return false;
            // Log or handle the exception
        }
      
    }
}
