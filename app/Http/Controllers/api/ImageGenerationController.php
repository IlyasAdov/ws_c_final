<?php

namespace App\Http\Controllers\api;

use App\Models\Service;
use App\Models\ApiToken;
use App\Models\ServiceUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageGenerationController extends Controller
{
    private const DREAMWEAVER_URL = 'http://127.0.0.1:9002';

    public function generate(Request $req) {
        $req->validate(['text_prompt' => 'required|string']);

        $response = Http::post(self::DREAMWEAVER_URL . '/generate', ['text_prompt' => $req->text_prompt]);

        if ($response->status() !== 201) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        $responseJson = $response->json();

        request()->session()->put('jobs.'.$responseJson['job_id'], [
            'start' => now(),
            'savedUsage' => false
        ]);

        return response()->json(['job_id' => $responseJson['job_id']]);
    }

    public function getStatus(Request $req, $jobId) {
        if (!$jobId) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        }

        $response = Http::get(self::DREAMWEAVER_URL . '/status/' . $jobId);

        if ($response->status() !== 200) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        $responseJson = $response->json();

        $filePath = $this->downloadFile($responseJson['image_url']);
        $imageUrl = url('files/'.$filePath);

        return response()->json([
            'status' => $responseJson['status'],
            'progress' => $responseJson['progress'],
            'image_url' => $imageUrl
        ]);
    }

    public function getResult(Request $req, $jobId) {
        if (!$jobId) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        }

        $jobs = $req->session()->get('jobs');

        if (!isset($jobs[$jobId])) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        }

        $response = Http::get(self::DREAMWEAVER_URL . '/result/' . $jobId);

        if ($response->status() === 400) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        } else if ($response->status() !== 200) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        $responseJson = $response->json();

        if (!$jobs[$jobId]['savedUsage']) {
            $finishedAt = Carbon::parse($responseJson['finished_at']);
            $start = Carbon::parse($jobs[$jobId]['start']);

            ServiceUsage::create([
                'service_id' => Service::where('name', 'DreamWeaver')->value('id'),
                'api_token_id' => ApiToken::where('token', $req->header('X-API-TOKEN'))->value('id'),
                'duration_in_ms' => $finishedAt->diffInMilliseconds($start),
                'usage_started_at' => $jobs[$jobId]['start']
            ]);

            $jobs[$jobId]['savedUsage'] = true;
            request()->session()->put('jobs', $jobs);
        }

        $filePath = $this->downloadFile($responseJson['image_url']);
        $imageUrl = url('files/'.$filePath);

        return response()->json([
            'resource_id' => $responseJson['resource_id'],
            'image_url' => $imageUrl
        ]);
    }

    public function upscale(Request $req) {
        return $this->triggerResourceAction($req, 'upscale');
    }

    public function zoomIn(Request $req) {
        return $this->triggerResourceAction($req, 'zoom/in');
    }

    public function zoomOut(Request $req) {
        return $this->triggerResourceAction($req, 'zoom/out');
    }

    protected function triggerResourceAction(Request $req, $action) {
        $req->validate(['resource_id' => 'required|string']);

        $response = Http::post(self::DREAMWEAVER_URL . '/' . $action, ['resource_id' => $req->resource_id]);

        if ($response->status() === 400) {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        } else if ($response->status() !== 201) {
            return response()->json([
                'title' => 'Service Unavailable',
                'status' => 503
            ], 503);
        }

        $responseJson = $response->json();

        request()->session()->put('jobs.'.$responseJson['job_id'], [
            'start' => now(),
            'savedUsage' => false
        ]);

        return response()->json($responseJson);
    }

    protected function downloadFile($fileUrl) {
        $response = Http::get($fileUrl);

        if ($response->successful()) {
            $fileContent = $response->body();

            $fileName = uniqid('file_') . '.' . pathinfo($fileUrl, PATHINFO_EXTENSION);

            Storage::disk('public')->put('files/'.$fileName, $fileContent);

            return $fileName;
        } else {
            return response()->json([
                'title' => 'Bad Request',
                'status' => 404
            ], 404);
        }
    }
}
