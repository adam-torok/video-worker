<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    
    public function store(Request $request)
    {
        $this->validate($request,[
            'video' => 'required|file|mimetypes:video/*',
        ]);

        if($request->hasFile('video')){
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_';
            $uniqid = '';
                for ($i = 0; $i < 11; $i++)
            $uniqid .= $characters[mt_rand(0, 63)];

            $fileNameWithExtension = $request->file('video')->getClientOriginalName();
            $filename = pathinfo($fileNameWithExtension,PATHINFO_FILENAME);
            $extension = $request->file('video')->getClientOriginalExtension();
            $filenameToStore = $uniqid.".".$extension;
            $path = $request->file('video')->storeAs('/',$filenameToStore,'uploads');
            $video = new Video;
            $video->id = $uniqid;
            $video->path = $path;
            if($video->save()){
                return $uniqid;
            }
        }else{
            // RESPONSE
        }
    }

    public function show($id,$quality)
    {
        $video = Video::find($id);
        switch ($quality) {
            case '360':
                return "http://127.0.0.1:8000/videos/360/".$video->path;      
                break;
            case '720':
                return "http://127.0.0.1:8000/videos/360/".$video->path;      
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
}