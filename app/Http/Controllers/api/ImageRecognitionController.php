<?php

namespace App\Http\Controllers\api;

use App\Models\Service;
use App\Models\ApiToken;
use App\Models\ServiceUsage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ImageRecognitionController extends Controller
{
    private const MINDREADER_URL = 'http://127.0.0.1:9003';

    public function recognize(Request $req, ) {
        $req->validate(['image' => 'required|file']);

        $start = now();

        $formData = fopen($req->file('image')->path(), 'r');

        $response = Http::attach('image', $formData)->post(self::MINDREADER_URL . '/recognize');

        if ($response->status() !== 200) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        ServiceUsage::create([
            'service_id' => Service::where('name', 'MindReader')->value('id'),
            'api_token_id' => ApiToken::where('token', $req->header('X-API-TOKEN'))->value('id'),
            'duration_in_ms' => now()->diffInMilliseconds($start),
            'usage_started_at' => $start
        ]);

        $responseJson = $response->json();

        return response()->json([
            'objects' => collect($responseJson['objects'])->map(function ($obj) {
                return [
                    'name' => $obj['label'],
                    'probability' => $obj['probability'],
                    'bounding_box' => [
                        'x' => $obj['bounding_box']['left'],
                        'y' => $obj['bounding_box']['right'],
                        'width' => $obj['bounding_box']['right'] - $obj['bounding_box']['left'],
                        'height' => $obj['bounding_box']['bottom'] - $obj['bounding_box']['top']
                    ]
                ];
            })
        ]);
    }
}
