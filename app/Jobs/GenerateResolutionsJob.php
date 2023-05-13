<?php

namespace App\Jobs;

use App\Models\Resolution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Coordinate\Dimension;

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
        dd($this->storagePath);
        $resolutions = [144, 360, 720];

        foreach ($resolutions as $resolution) {
            $outputFilename = $resolution . 'p_' . $this->filename;

            try {
                $command = 'C:\ffmpeg\bin\ffmpeg -i ' . escapeshellarg($this->storagePath . '/' . $this->filename) .
                    ' -vf "scale=' . $resolution . ':trunc(ow/a/2)*2" ' .
                    escapeshellarg($this->storagePath . '/' . $outputFilename);

                shell_exec($command);

                $this->saveResolution($resolution, $outputFilename);
            } catch (\Exception $e) {
                $errorMessage = 'Error generating resolution: ' . $resolution . 'p. ' . $e->getMessage();
                throw new \Exception($errorMessage);
            }
        }
    }

    private function saveResolution($resolution, $outputFilename)
    {
        $resolutionModel = new Resolution();
        $resolutionModel->video_id = $this->videoId;
        $resolutionModel->resolution = $resolution;
        $resolutionModel->path = $outputFilename;
        $resolutionModel->save();
    }
}
