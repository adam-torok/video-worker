<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $converted_name = $this->getCleanFileName($this->video->path);
        FFMpeg::fromDisk('videos')
            ->open($this->video->path)
            ->addFilter(function ($filters) {
                //360
                $filters->resize(new Dimension(960, 360));
            })
            ->export()
            ->toDisk('public')
            ->save($converted_name);
        // update the database so we know the convertion is done!
        $this->video->update([
            'converted__at' => Carbon::now(),
            'processed' => true,
        ]);
    }
    private function getCleanFileName($filename){
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename) . '.mp4';
    }
}
