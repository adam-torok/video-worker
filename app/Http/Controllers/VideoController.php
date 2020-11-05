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
            'video' => 'required|file|mimetypes:video/*',
        ]);

        if($request->hasFile('video')){
            $uniqId = $this->generateuniqId();
            $fileNameWithExtension = $request->file('video')->getClientOriginalName();
            $filename = pathinfo($fileNameWithExtension,PATHINFO_FILENAME);
            $extension = $request->file('video')->getClientOriginalExtension();
            $filenameToStore = $uniqId.".".$extension;
            $path = $request->file('video')->storeAs('/',$filenameToStore,'videos');
            $video = new Video;
            $video->id = $uniqId;
            $video->path = $path;
            if($video->save()){
                ConvertVideo::dispatch($video);
                return $uniqId;
            }
        }else{
            abort(404);
        }
    }

    public function show($id,$quality)
    {
        $video = Video::find($id);
        switch ($quality) {
            case '360':
            if($video->processed = 1){
                return "http://127.0.0.1:8000/videos/360/".$video->path;      
            }else{
                return "http://127.0.0.1:8000/videos/default/".$video->path;      
            }
            break;
            case '720':
            if($video->processed = 1){
                return "http://127.0.0.1:8000/videos/720/".$video->path;      
            }else{
                return "http://127.0.0.1:8000/videos/default/".$video->path;
            }     
                break;
            default:
                return "http://127.0.0.1:8000/videos/default/".$video->path;      
            break;
        }
    }

    public function destroy($id)
    {
        $video = Video::find($id);
        if($video->delete() && Storage::delete($video->path)){
            //RESPONSE
        }else{
           //RESPONSE
        }
    }

    public static function generateuniqId(){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
        $uniqId = '';
        for ($i = 0; $i < 11; $i++)
        $uniqId .= $characters[mt_rand(0, 63)];
        return $uniqId;
    }
}