<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;

use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo "All videos";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->validate($request,[
            'video' => 'required',
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
            $path = $request->file('video')->storeAs('public/videos',$filenameToStore);

            $video = new Video;
            $video->id = $uniqid;
            $video->path = $path;
            if($video->save()){
                return $uniqid;
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$quality)
    {
        echo "id - ".$id."<br> quality - " . $quality;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $video = Video::find($id);
        if($video->delete()){
            echo "deleted";
        }
    }
}
