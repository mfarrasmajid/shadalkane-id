<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageEditorController extends Controller
{
    public function index()
    {
        return view('tools.image-editor');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $file = $request->file('image');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads', $filename, 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
            'filename' => 'required|string|max:255',
        ]);

        $imageData = $request->input('image_data');

        // Validate base64 image data
        if (!preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/', $imageData, $matches)) {
            return response()->json(['error' => 'Format gambar tidak valid.'], 422);
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return response()->json(['error' => 'Data gambar tidak valid.'], 422);
        }

        $filename = Str::uuid() . '.' . $extension;
        Storage::disk('public')->put('edited/' . $filename, $imageData);

        return response()->json([
            'url' => asset('storage/edited/' . $filename),
            'filename' => $filename,
        ]);
    }
}
