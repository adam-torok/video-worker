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
    public $destination;
    public $height;
    public function __construct(Video $video, $height)
    {
        $this->video = $video;
        $this->height = $height;
        switch ($height) {
            case '360':
                $this->destination = 'videos'.$height;
                break;
            case '720':
                $this->destination = 'videos'.$height;
                break;
            default:
                $this->destination = 'videos360';
                break;
        }
    }

    public function handle()
    {
        $videoPath = $this->video->path;
        $videoId = $this->video->id;
        $videoHeight = $this->height;
        $videoDest = $this->destination;
       
        $this->ConvertVideoToFormat($videoPath,$videoDest,$videoHeight);
        $videoToUpdate = Video::find($videoId);
        $videoToUpdate->processed = 1;
        $videoToUpdate->save();
    }
    
    public function ConvertVideoToFormat($srcPath,$destPath,$videoHeight){
        $videoSrcWithoutExtension = $this->getCleanFileName($srcPath);
        FFMpeg::fromDisk('videos')
            //Mp4 format
            ->open($srcPath)
            ->export()
            ->toDisk($destPath)
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->resize($videoHeight, 480)
            ->save($videoSrcWithoutExtension.'.mp4')
            //WebM format
            ->export()
            ->toDisk($destPath)
            ->inFormat(new \FFMpeg\Format\Video\WebM)
            ->resize($videoHeight, 480)
            ->save($videoSrcWithoutExtension.'.webm');
    }

    private function getCleanFileName($filename){
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
    }
}
