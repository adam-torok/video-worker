<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Jobs\ConvertVideo;

use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    
    public function store(Request $request)
    {
        $this->validate($request,[
            'video' => 'required|file|mimetypes:video/mp4,video/webm',
        ]);

        if($request->hasFile('video')){
            $uniqId = $this->generateUniqId();
            $fileNameWithExtension = $request->file('video')->getClientOriginalName();
            $filename = pathinfo($fileNameWithExtension,PATHINFO_FILENAME);
            $extension = $request->file('video')->getClientOriginalExtension();
            $filenameToStore = $uniqId.".".$extension;

            $path = $request->file('video')->storeAs('/',$filenameToStore,'videos');
            $video = new Video;
            $video->id = $uniqId;
            $video->path = $path;
            $video->save();
            ConvertVideo::dispatch($video,360);
            ConvertVideo::dispatch($video,720);
            return response()->json([
                'id' => $uniqId,
            ], 200);
        }else{
            abort(500);
        }
    }

    public function show($quality,$id)
    {
        $video = Video::find($id);
        $videoPath = $video->path;
        $isProcessed = $video->processed;
        $urlFor360Video =  env('TEST_DOMAIN')."/videos/360/";
        $urlFor720Video = env('TEST_DOMAIN')."/videos/720/";
        $urlForDefaultVideo = env('TEST_DOMAIN')."/videos/default/";

        switch ($quality) {
            case '360':
            if($isProcessed = 1){
            return response()->json([
                'link' => $urlFor360Video.$videoPath
            ], 200);
            }else{
            return response()->json([
                'link' => $urlForDefaultVideo.$videoPath
            ], 404);  
            }
            break;
            case '720':
            if($isProcessed = 1){
            return response()->json([
                'link' => $urlFor720Video.$videoPath
            ], 200);
            }else{
            return response()->json([
                'link' => $urlForDefaultVideo.$videoPath
            ], 404);
            }     
            break;
            default:
                return response()->json([
                'link' => $urlForDefaultVideo.$videoPath
            ], 200);
            break;
        }
    }

    public function destroy($id)
    {
        $video = Video::find($id);
        $videoId = $id;
        $this->deleteVideosFromDirectories($videoId);
        $video->delete();
    }

    public static function deleteVideosFromDirectories($videoToDelete)
    {
        $disks = ['videos360','videos720','videos'];
        foreach ($disks as $disk) {
            echo $disk;
            echo $videoToDelete;
            Storage::disk($disk)->delete($videoToDelete.'.mp4');
            Storage::disk($disk)->delete($videoToDelete.'.webm');
        }
    }

    public static function generateUniqId(){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
        $uniqId = '';
        for ($i = 0; $i < 11; $i++)
        $uniqId .= $characters[mt_rand(0, 63)];
        return $uniqId;
    }
}