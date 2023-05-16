<?php

namespace App\Http\Controllers;

use App\Models\Resolution;
use App\Models\Video;
use App\Jobs\GenerateResolutionsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = Video::all();
        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'video' => 'required|mimetypes:video/*',
        ]);

        $videoFile = $request->file('video');

        if ($request->hasFile('video') && $videoFile->isValid()) {
            $video = Video::create(['title' => $request->input('title')]);
            $filename = date('Ymd_His') . '_' . $videoFile->getClientOriginalName();
            $storagePath = storage_path('app/public/videos');
            $fullPath = $this->moveVideoFile($videoFile, $storagePath, $filename, $video->id);

            //wait until the file uploaded successfully
            while (!file_exists($fullPath)); {
                usleep(1000);
            }
            // Push the job to the queue after the file is moved
            Queue::push(new GenerateResolutionsJob($video->id, $storagePath, $filename));

            return redirect()->route('videos.create')->with('success', 'Video upload initiated successfully.');
        }

        $errorMessage = 'The video failed to upload: ' . $videoFile->getErrorMessage();
        return redirect()->back()->withErrors(['video' => $errorMessage]);
    }



    /**
     * Move the video file to storage and save a resolution.
     */
    private function moveVideoFile($videoFile, $storagePath, $filename, $videoId)
    {
        $path = $videoFile->move($storagePath, $filename);

        $resolution = new Resolution();
        $resolution->video_id = $videoId;
        $resolution->resolution = '1080';
        $resolution->path = $filename;
        $resolution->save();
        return $path->getRealPath();
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $video = Video::findOrFail($id);
        $resolutions = Resolution::where('video_id', $video->id)->get();

        return view('videos.show', compact('resolutions', 'video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $video = Video::findOrFail($id);

        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $video = Video::findOrFail($id);
        $video->title = $request->input('title');
        $video->save();

        return redirect()->route('videos.edit', $id)->with('success', 'Video updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $resolutions = Resolution::where('video_id', $video->id)->get();

        // Delete video resolutions
        foreach ($resolutions as $resolution) {
            // Delete the resolution file
            $storagePath = storage_path('app/public/videos');
            $filePath = $storagePath . '/' . $resolution->path;
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete the resolution from the database
            $resolution->delete();
        }

        // Delete the main video file
        $storagePath = storage_path('app/public/videos');
        $filePath = $storagePath . '/' . $video->filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the video from the database
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video and related files deleted successfully.');
    }

    public function clear()
    {
        $videos = Video::all();
        foreach ($videos as $video) {
            $resolutions = Resolution::where('video_id', $video->id)->get();
            foreach ($resolutions as $resolution) {
                $storagePath = storage_path('app/public/videos');
                $filePath = $storagePath . '/' . $resolution->path;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $videoDeleted = Video::find($video->id);
            $videoDeleted->delete();
        }
        return back();
    }
}
