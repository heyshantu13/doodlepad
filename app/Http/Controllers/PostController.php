<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use App\Post;
use App\Http\Requests\CreatePostValidate;
use Aws\S3\S3Client as AWS;
use Aws\S3\Exception\S3Exception;




class PostController extends Controller
{
    //
    private $client;
    private $awsobj;

    // public function __construct(){

    //      $this->awsobj = $this->client=  new AWS([
    //         'version' => 'latest',
    //         'region'  => env('DO_SPACES_REGION'),
    //         'endpoint' => env('DO_SPACES_ENDPOINT'),
    //         'credentials' => [
    //                 'key'    => env('DO_SPACES_KEY'),
    //                 'secret' => env('DO_SPACES_SECRET'),
    //             ],
    // ]);

    // }

    public function uploadimage(){
//         $imageName = rand(1111,9999).time().'.'.request()->profile_picture_url->getClientOriginalExtension();
//           request()->file('profile_picture_url');
//           $dofiles = Aws::putObject([
//      'Bucket' => 'doodlepadin',
//      'Key'    => 'file.ext',
//      'Body'   => request()->file('profile_picture_url'),
//      'ACL'    => 'private'
// ]);

//           return $dofiles;

//         $bucket = 'doodlepadin';
// $keyname = 'sample';
// // $filepath should be absolute path to a file on disk                      
// $filepath = '/';
// $s3 = S3Client::factory(array(
//     'key'    => 'UUT2IQAMH5WUSJDJDYSJ',
//     'secret' => 'hz87awvT5XZiZqfS2qHNZ1+qty0HO5BAd/IpijI0gtI',
//     'version' => 'latest',
//     'region'  => env('DO_SPACES_REGION'),
//     'endpoint' => env('DO_SPACES_ENDPOINT'),

//     ));

// try {
//     // Upload data.
//     $result = $s3->putObject(array(
//         'Bucket' => $bucket,
//         'Key'    => $keyname,
//         'SourceFile'   => $filePath,
//         'ACL'    => 'public-read',
//         'ContentType' => 'image/jpeg'
//     ));

//     // Print the URL to the object.
//     echo $result['ObjectURL'] . "\n";
// } catch (S3Exception $e) {
//     echo $e->getMessage() . "\n";
// }

  $imageName = rand(1111,9999).time().'.'.request()->profile_picture_url->getClientOriginalExtension();
//           request()->file('profile_picture_url');

       // Configure a client using Spaces
$client = new AWS([
        'version' => 'latest',
            'region'  => env('DO_SPACES_REGION'),
            'endpoint' => env('DO_SPACES_ENDPOINT'),
            'credentials' => [
                    'key'    => env('DO_SPACES_KEY'),
                    'secret' => env('DO_SPACES_SECRET'),
            ],
]);



// Listing all Spaces in the region
$spaces = $client->listBuckets();
foreach ($spaces['Buckets'] as $space){
    echo $space['Name']."\n";
}


// Upload a file to the Space
$insert = $client->putObject(array(
        'Bucket' => 'doodlepadin',
        'Key'    => 'images1222221',
        'SourceFile'   =>  request()->file('profile_picture_url'),
        'ContentType' => 'image/jpeg'
    ));

return $insert;

    }


        public function createPost(CreatePostValidate $request){

          $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        $post = new Post();
        $post->text = $request->text;
        $post->type = $request->type;
        $post->alignment = $request->alignment;
        $post->user_profile_id = $profile->id;
        $post->caption = $request->caption;
        $post->text_location = $request->text_location;
        $post->longitude = $request->longitude;
        $post->latitude = $request->latitude;
        $post->media_url = $request->media_url;
        $post->save();
        return response()->json(['status'=>true,'post'=>Post::find($post->id)],200);
           

        }

        public function myPosts(){

            $user = Auth::user();
        $profile = UserProfile::where('user_id', $user->id)->firstOrFail();

        $posts = Post::where('user_profile_id', $profile->id);
        $posts = $posts->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($posts,200);




        }
}
