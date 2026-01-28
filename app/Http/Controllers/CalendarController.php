<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CalendarController extends Controller
{
    public function index()
    {
        return view('admin.calendar.index');
    }

    public function locationCalendar($location_id)
    {
        // $location_id should be 1-4
        if (!in_array($location_id, [1, 2, 3, 4, 5])) {
            abort(404);
        }

        $files = [];
        $path = public_path('calendars');
        if (File::exists($path)) {
            $allFiles = File::files($path);
            foreach ($allFiles as $file) {
                $filename = $file->getFilename();
                // Check if file starts with location_id_
                if (str_starts_with($filename, "{$location_id}_") && str_ends_with($filename, '.jpg')) {
                    // Parse details: {location_id}_{lang}_{index}_{year}.jpg
                    $parts = explode('_', str_replace('.jpg', '', $filename));
                    if (count($parts) === 4) {
                        $files[] = [
                            'filename' => $filename,
                            'lang' => $parts[1],
                            'index' => $parts[2],
                            'year' => $parts[3],
                            'url' => asset('calendar/' . $filename)
                        ];
                    }
                }
            }
        }

        // Sort files by year (desc), then index (asc), then lang
        usort($files, function ($a, $b) {
            if ($a['year'] != $b['year']) return $b['year'] <=> $a['year'];
            if ($a['index'] != $b['index']) return $a['index'] <=> $b['index'];
            return $a['lang'] <=> $b['lang'];
        });

        return view('admin.calendar.location', compact('location_id', 'files'));
    }

    public function uploadImage(Request $request, $location_id)
    {

        $request->validate([
            'year' => 'required|integer',
            'index' => 'required|integer|min:1|max:9',
            'lang' => 'required|in:th,en',
            'image' => 'required|image|mimes:jpeg,jpg|max:4096', // assuming jpg only as per request convention
        ]);

        if (!in_array($location_id, [1, 2, 3, 4])) {
            abort(404);
        }



        $year = $request->input('year');
        $index = $request->input('index');
        $lang = $request->input('lang');
        $file = $request->file('image');

        // Name format: {location_id}_{lang}_{index}_{year}.jpg
        $fileName = "{$location_id}_{$lang}_{$index}_{$year}.jpg";
        $destinationPath = public_path('calendars');

        // Ensure directory exists
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);

        return back()->with('success', 'Image uploaded successfully: ' . $fileName);
    }

    public function getCalendar($location_id, $lang, $index, $year)
    {
        $fileName = "{$location_id}_{$lang}_{$index}_{$year}.jpg";
        $path = public_path('calendars/' . $fileName);

        if (!File::exists($path)) {
            // Return 404 or a placeholder if preferred, user requested return image
            abort(404);
        }

        return response()->file($path);
    }
    public function deleteImage(Request $request, $location_id)
    {
        $filename = $request->input('filename');
        if (!$filename) {
            return back()->with('error', 'Filename is required.');
        }

        // Security check: ensure filename matches location_id and expected pattern
        if (!str_starts_with($filename, "{$location_id}_")) {
            return back()->with('error', 'Invalid file for this location.');
        }

        $path = public_path('calendars/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'Image deleted successfully.');
        }

        return back()->with('error', 'File not found.');
    }
}
