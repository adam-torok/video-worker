<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


## VIDEO-WORKER

I've built this project for a job application, the base concept was building a REST API, which can handle multiple requests, process and work with data.

Video worker is a rest api that is capable of 

- Convert videos to specific formats,
- Resize videos to specific sizes,
- Upload videos to the server,
- Get videos statuses,
- Delete videos from the server

If the right endpoint is called with the right parameters

## The project

The project is built in Laravel, which is web application framework - For converting videos I used the FFMPEG library, which is a fast and efficient way to convert, resize images and videos.

## Installation

- Clone the project, 
- Set the correct datas in the .env file
- Migrate your database

## Support

For any questions that you have in mind, or suggestions please contact me at adam-torok@outlook.hu

## Endpoints

Showing video in quality
- Req. type : GET
- Endpoint : /video/{id}/{quality}
- PS: - where id is the id, quality can be 360 or 720 (it is not actual quality but height)
- The response is the link for the video

Show a default video
- Req. type : GET
- Endpoint : /video/{id}/
- PS: where id is the id with the default uploaded quality

Uploading videos 
- Req. type : POST
- Endpoint :  /video.upload 
- PS: Where the file name is video in the request body
- The response is the id of the video

Deleting videos
- Req. type : POST
- Endpoint : /video.delete/{id}
- PS: Where the id is the id of the video
- The response is nothing. 

Suggestion - Use postman to test the api out.




