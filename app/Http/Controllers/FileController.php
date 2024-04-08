<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($fileName) {
        $filePath = 'files/'.$fileName;

        if (Storage::disk('public')->exists($filePath)) {
            return response()->file(storage_path('app/public/'.$filePath));
        } else {
            return abort(404);
        }
    }
}
