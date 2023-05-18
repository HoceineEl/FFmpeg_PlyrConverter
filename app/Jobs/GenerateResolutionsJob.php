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
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;
use ProtoneMedia\LaravelFFMpeg\Exporters\Concatenate;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;

class GenerateResolutionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $filename;
    protected $storagePath;

    /**
     * Create a new job instance.
     *
     * @param int $videoId
     * @param string $storagePath
     * @param string $filename
     * @return void
     */
    public function __construct(string $storagePath, string $filename, string $title)
    {
        $this->filename = $filename;
        $this->storagePath = $storagePath;
        $this->title = $title;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newFilename = str_replace('.mp4', '', $this->filename);
        $resolutions = ['1500'];

        // Iterate over the resolutions and add them as formats
        foreach ($resolutions as $resolution) {
            $format = (new X264('aac'))->setKiloBitrate($resolution);
            FFMpeg::fromDisk('ffmpeg')
                ->open($this->filename)
                ->exportForHLS()
                ->setSegmentLength(2)
                ->addFormat($format)
                ->onProgress(function ($progress) {
                    echo "Progress: {$progress}%\n";
                })
                ->toDisk('public')
                ->save('videos/' . $newFilename  . '.m3u8');


            $this->saveResolution($newFilename . '_0_' . $resolution . '.m3u8');
        }
        // Delete the original video file and the old resolution from the database
        unlink($this->storagePath . '/' . $this->filename);
    }

    private function saveResolution($outputFilename)
    {
        $resolutionModel = new Video();
        $resolutionModel->title = $this->title;
        $resolutionModel->path = $outputFilename;
        $resolutionModel->save();
    }
}
