<?php

namespace App\Jobs;

use App\Models\Resolution;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use Illuminate\Support\Facades\Storage;

class GenerateResolutionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $videoId;
    protected $storagePath;
    protected $filename;

    /**
     * Create a new job instance.
     *
     * @param int $videoId
     * @param string $storagePath
     * @param string $filename
     * @return void
     */
    public function __construct(int $videoId, string $storagePath, string $filename)
    {
        $this->videoId = $videoId;
        $this->storagePath = $storagePath;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $resolutions = ['426x240', '640x360', '854x480', '1280x720'];
        $ffmpegPath = 'C:\ffmpeg\bin\ffmpeg'; // Path to the ffmpeg command-line tool

        // Convert original video to WebM format

        // Change resolution based on the converted WebM video
        foreach ($resolutions as $resolution) {
            $quality = explode('x', $resolution)[1];
            $outputFilename = $quality . '_' . $this->filename;

            $outputPath = $this->storagePath . '/' . $outputFilename;

            try {
                $command = $ffmpegPath . ' -i ' . $this->storagePath . '/' . $this->filename . ' -s ' . $resolution . ' ' . $outputPath;
                shell_exec($command);
                $this->saveResolution($quality, $outputFilename);
            } catch (\Exception $e) {
                $errorMessage = 'Error generating resolution: ' . $resolution . 'p. ' . $e->getMessage();
                throw new \Exception($errorMessage);
            }
        }
    }

    private function saveResolution($quality, $outputFilename)
    {
        $resolutionModel = new Resolution();
        $resolutionModel->video_id = $this->videoId;
        $resolutionModel->resolution = $quality;
        $resolutionModel->path = $outputFilename;
        $resolutionModel->save();
    }
}
