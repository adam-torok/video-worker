<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg;
use App\Models\Video;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\Video\VideoFilters;


class ConvertVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $video;
   
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    public function handle()
    {
       try {
            FFMpeg::fromDisk('videos')
            ->open($this->video->path)
            ->export()
            ->toDisk('videos360')
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(360, 480));
            })
            ->save($this->video->path);

             FFMpeg::fromDisk('videos')
            ->open($this->video->path)
            ->export()
            ->toDisk('videos720')
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->addFilter(function (VideoFilters $filters) {
                $filters->resize(new \FFMpeg\Coordinate\Dimension(720, 480));
            })
            ->save($this->video->path);
            
            $videoToUpdate = Video::find($this->video->id);
            $videoToUpdate->processed = 1;

            $videoToUpdate->save();
            
        } catch (EncodingException $exception) {
            $errorLog = $exception->getErrorOutput();
        }
    }
}
