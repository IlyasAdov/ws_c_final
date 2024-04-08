<?php

namespace App\Http\Controllers\api;

use App\Models\Service;
use App\Models\ApiToken;
use App\Models\ServiceUsage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    private const CHATTERBLAST_URL = 'http://127.0.0.1:9001';

    private function parseResponse($textResponse) {
        $responseParts = explode('<EOF>', $textResponse);
        if (count($responseParts) > 1) {
            $words = explode(' ', $textResponse);
            $durationInMs = str_replace('ms', '', end($words));

            return [
                'isFinal' => true,
                'durationInMs' => $durationInMs,
                'text' => $responseParts[0]
            ];
        }

        return [
            'isFinal' => false,
            'durationInMs' => 0,
            'text' => $textResponse
        ];
    }

    private function getConversation($conversationId) {
        return request()->session()->get('conversations.' . $conversationId, [
            'lastPromptTimestamp' => now(),
            'promptCount' => 0,
            'promptsResponseCount' => 0,
            'savedUsageCount' => 0
        ]);
    }

    private function readResponse($token, $conversationId) {
        $response = Http::get(self::CHATTERBLAST_URL . '/conversation/' . $conversationId);
        $textResponse = $response->body();
        
        $parsedResponse = $this->parseResponse($textResponse);

        if ($parsedResponse['isFinal']) {
            $conversation = $this->getConversation($conversationId);
            $conversation['promptsResponseCount'] = $conversation['promptCount'];

            if ($conversation['promptCount'] > $conversation['savedUsageCount']) {
                ServiceUsage::create([
                    'duration_in_ms' => $parsedResponse['durationInMs'],
                    'service_id' => Service::where('name', 'ChatterBlast')->value('id'),
                    'api_token_id' => ApiToken::where('token', $token)->value('id'),
                    'usage_started_at' => $conversation['lastPromptTimestamp']
                ]);

                $conversation['savedUsageCount'] = $conversation['promptCount'];
            }

            request()->session()->put('conversations.'.$conversationId, $conversation);
        }

        return [
            'conversation_id' => $conversationId,
            'response' => $parsedResponse['text'],
            'is_final' => $parsedResponse['isFinal']
        ];
    }

    public function startConversation(Request $req) {
        $req->validate(['prompt' => 'required|string']);

        $conversationId = uniqid();

        $creationResponse = Http::post(self::CHATTERBLAST_URL . '/conversation', ['conversationId' => $conversationId]);

        if ($creationResponse->status() !== 201) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        $conversation = $this->getConversation($conversationId);
        $conversation['promptCount'] = 1;
        
        request()->session()->put('conversations.'.$conversationId, $conversation);

        $postPromptResponse = Http::withHeaders(['Content-Type' => 'text/plain'])->post(self::CHATTERBLAST_URL . '/conversation/' . $conversationId, $req->prompt);

        if ($postPromptResponse->status() !== 200) {
            return response()->json([
                'title' => 'Not Found',
                'status' => 404
            ], 404);
        }

        return response()->json($this->readResponse($req->header('X-API-TOKEN'), $conversationId));
    }

    public function continueConversation(Request $req, $conversationId) {
        $req->validate(['prompt' => 'required|string']);

        $conversation = $this->getConversation($conversationId);

        if ($conversation['promptsResponseCount'] < $conversation['promptCount']) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 400
            ], 400);
        }

        $conversation['lastPromptTimestamp'] = now();
        $conversation['promptCount']++;

        request()->session()->put('conversations.'.$conversationId, $conversation);

        $postPromptResponse = Http::withHeaders(['Content-Type' => 'text/plain'])->post(self::CHATTERBLAST_URL . '/conversation/' . $conversationId, $req->prompt);

        if ($postPromptResponse->status() !== 200) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        return response()->json($this->readResponse($req->header('X-API-TOKEN'), $conversationId));
    }

    public function getResponse(Request $req, $conversationId) {
        if(!$req->session()->has('conversations.'.$conversationId)) {
            return response()->json([
                'title' => 'Not Found',
                'status' => 404
            ], 404);
        }

        return response()->json($this->readResponse($req->header('X-API-TOKEN'), $conversationId));
    }
}
