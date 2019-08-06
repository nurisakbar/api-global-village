<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\UserDeliveryDestination;
use Auth;
use Illuminate\Support\Str;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Transformers\ProductTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\HarvestTransformer;
use App\Transformers\LandTtransformer;
use App\Transformers\UserDeliveryDestinationTransformer;
use App\Services\UploadService;
use Nasution\ZenzivaSms\Client as Sms;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{

    private $fractal;
    private $UserTransformer;
    protected $url;
    protected $client;

    public function __construct(UserTransformer $UserTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->UserTransformer = $UserTransformer;
        $this->fractal = new Fractal\Manager();
        $this->url =  env("API_URL");
        $this->client = new \GuzzleHttp\Client();
    }

    public function index()
    {
        $users                  = User::orderBy('created_at','DESC')->get();
        $users                  = new Collection($users, $this->UserTransformer);
        $users                  = $this->fractal->createData($users); 
        $response               = $users->toArray();
        $response['success']    = true;
        $response['status']     = 210;
        return response()->json($response,200);
    }


    public function register(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'name'          =>  'required',
            //'address'       =>  'required',
            'email'         =>  'required|email|unique:users',
            //'phone'         =>'required|unique:users',
            'password'         =>'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $input                  = $request->only('name','email','password');
        $input['address']       = null;
        $input['phone']         = null;
        $id                     = Uuid::uuid4();
        $input['id']            = $id;
        $input['wallet_saldo']  = 0;              
        $token                  = Str::random(32);
        $input['api_token']     = $token;
        //dd($input);
        $user                   = User::create($input);
        $response['status']     = true;
        $response['data']       = [
                                    'name'  =>  $request->name,
                                    'email' =>  $request->email,
                                    'id'    =>  $id,
                                    'token' =>  $token
                                ];

        $response['message']    = "Pendaftaran Berhasil";
        $statusCode             = 201;
        return response()->json($response,200);
    }


    public function login(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'email'         =>  'required',
            'password'         =>'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user = User::where('email',$request->email)->first();
        if($user!=null)
        {
            if (\Hash::check($request->password, $user->password)) {
                // The passwords match...
                
                $user                   = new Item($user, $this->UserTransformer);
                $user                   = $this->fractal->createData($user); 

                // update token
                $userData = User::where('email',$request->email)->first(); 
                $userData->api_token = Str::random(32);
                $userData->update();
                // end update token

                $user_baru = User::where('email',$request->email)->first();
                $response = [];

                $user_baru              = new Item($user_baru, $this->UserTransformer);
                $user_baru              = $this->fractal->createData($user_baru); 
                $response               = $user_baru->toArray();
                $statusCode             = 200;
                $response['message']    = "Login Berhasil";
                $response['status']     = true;
    
            }else
            {
                $response['status']     = false;
                $response['data']       = null;
                $response['message']    = "Email Atau Password Salah";
                $statusCode             = 404;
            } 
        }else
        {
            $response['status']     = false;
            $response['data']       = null;
            $response['message']    = "Akun Tidak Terdaftar";
            $statusCode             = 404;
        }
        
        
        return response()->json($response,200);
    }

    // fungsi untuk melakukan update profil User
    public function updateProfile(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'token'          =>  'required',
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'village'=>'required',
            'address'=>'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $User = User::where('api_token',$request->token)->first();
        
        if($User!=null)
        {
            $User->address      = $request->address;
            $User->village_id   = $request->village;
            $User->name         = $request->name;
            $User->email        = $request->email;
            $User->phone        = $request->phone;
            $User->update();

            // set response
            
            $User                   = new Item($User, $this->UserTransformer);
            $User                   = $this->fractal->createData($User); 
            $response               = $User->toArray();
            $response['status']     = true;
            $response['message']    = 'Update Profile Berhasil';
            $statusCode             = 201;
        }else
        {
            $response['status']     = false;
            $response['data']       = null;
            $response['message']    = "Resource Not Found";
            $statusCode             = 400;
        }

        return response()->json($response,$statusCode);
    }


    public function updateProfileImage(Request $request,UploadService $upload)
    {
        //return $request->all();

        $validator          = \Validator::make($request->all(), [
            'token'          =>  'required',
            'image'         =>'required|mimes:jpeg,jpg,png'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        //$folderDestination = "img_user";

        if ($request->hasFile('image')) {

            // $file       = $request->file('image');
            // $fileName   = $file->getClientOriginalName();
            // $file->move($folderDestination,$fileName);

            $upload_image   = $upload->image($request,'image','img_user');
            $fileName       = $upload_image['data']['file_name'];

            // update User
            $User = User::where('api_token',$request->token)->first();
            if($User==null)
            {
                $response['status']     = false;
                $response['message']    = "account not found";
                $response['data']       = null;
                $statusCode             = 400;
            }else
            {
                $User->photo = $fileName;
                $User->update();
                $response['status']     =   true;
                $response['message']    =   "Update Foto Berhasil";
                $response['data']       =   [
                                                'file_name' =>  $fileName,
                                                'url'       =>  secure_asset($folderDestination.'/'.$fileName)
                                            ];
                $statusCode             = 200;
            }
        }else
        {
            $response['status'] = "Terjadi Kesalahan";
            $response['data']   = null;
            $statusCode         = 400;
        }

        return response()->json($response,$statusCode );
    }

    // function untuk mendapatkan informasi User
    function profile($token)
    {
       if(!isset($token))
       {
        $response['status']     = false;
        $response['data']       = null;
        $response['message']    = "Token Tidak Boleh Kosong";
        $statusCode             = 400;

       }else
       {

        $user = User::where('api_token',$token)->first();
     
        if($user!=null)
        {
            $response['status']     =   true;
            $response['message']    =   null;
            $user       = User::where('api_token',$token)->first();
            $profile    = new Item($user, $this->UserTransformer);
            $profile    = $this->fractal->createData($profile); 
            $response   = $profile->toArray();
            $statusCode = 200;
        }else
        {
            $response['status']     = false;
            $response['data']       = null;
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }
       }
        return response()->json($response,$statusCode);
    }

    // mendapatkan informasi product yang di upload
    public function products($token, ProductTransformer $pt)
    {
        if(!isset($token))
       {
        $response['status']     = "failed";
        $response['data']       = null;
        $response['message']    = "Token Tidak Boleh Kosong";
        $statusCode             = 200;
       }else
       {

        $user = User::where('api_token',$token)->orWhere('id',$token)->first();
     
        if($user!=null)
        {
            $response['status']     ='success';
            $response['message']    = null;
            $user     = User::where('api_token',$token)->orWhere('id',$token)->first();
            $products = $user->products;
            $products = new Collection($products, $pt);
            $products = $this->fractal->createData($products); 
            $response = $products->toArray();
            $statusCode = 200;
        }else
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }
       }
        return response()->json($response,$statusCode);
    }



    // mendapatkan informasi product yang di upload
    public function harvest($token, HarvestTransformer $ht)
    {
        if(!isset($token))
       {
        $response['status']     = "failed";
        $response['data']       = null;
        $response['message']    = "Token Tidak Boleh Kosong";
        $statusCode             = 200;
       }else
       {

        $user = User::where('api_token',$token)->orWhere('id',$token)->first();
     
        if($user!=null)
        {
            
            $user     = User::where('api_token',$token)->orWhere('id',$token)->first();
            $harvests = $user->harvests;
            //dd($harvests);
            $harvests = new Collection($harvests, $ht);
            $harvests = $this->fractal->createData($harvests); 
            $response = $harvests->toArray();
            $response['status']     ='success';
            $response['message']    = null;
            $statusCode = 200;
        }else
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }
       }
        return response()->json($response,$statusCode);
    }

    // mendapatkan informasi product yang di upload
    public function land($token, LandTtransformer $lt)
    {
        if(!isset($token))
       {
        $response['status']     = "failed";
        $response['data']       = null;
        $response['message']    = "Token Tidak Boleh Kosong";
        $statusCode             = 200;
       }else
       {

        $user = User::where('api_token',$token)->orWhere('id',$token)->first();
     
        if($user!=null)
        {
            
            $user     = User::where('api_token',$token)->orWhere('id',$token)->first();
            $lands = $user->lands;
            //dd($harvests);
            $lands = new Collection($lands, $lt);
            $lands = $this->fractal->createData($lands); 
            $response = $lands->toArray();
            $response['status']     ='success';
            $response['message']    = null;
            $statusCode = 200;
        }else
        {
            $response['status']     = "failed";
            $response['data']       = null;
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }
       }
        return response()->json($response,$statusCode);
    }


    public function forgetPassword(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if($user!=null)
        {
            $newpassword = strtolower(Str::random(6));

            $userkey = "j3d9i1";
            $passkey = "nh09f05s75";
            $nohp    = $user->phone;
            $pesan   = "Pusat Bantuan Global Village - Password Baru Anda adalah :  ".$newpassword." Dan Email : ".$user->email.", Jangan Beritahukan Password Anda Kepada Siapapun ";
            $url = "https://reguler.zenziva.net/apps/smsapi.php?userkey=$userkey&passkey=$passkey&nohp=$nohp&pesan=$pesan";
            
            $request                = $this->client->get($url);
            //$response               = json_decode($request->getBody()->getContents());
            $fileContents   = $request->getBody();
            $fileContents   = str_replace(array("\n", "\r", "\t"), '', $fileContents);
            $fileContents   = trim(str_replace('"', "'", $fileContents));
            $simpleXml      = simplexml_load_string($fileContents);
            $hasiljson      = json_encode($simpleXml);
            $hasildecode    = json_decode($hasiljson);
     

            $user->password = $newpassword;
            $user->update();

            $response['message'] = "password baru sedang dikirim via SMS";
            $statusCode          = 200;
        }else{
            $response['message'] = "email tidak ditemukan";
            $statusCode          = 404;
        }
        
        return response()->json($response,$statusCode);
    }

    public function changePassword(request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'token'          =>  'required',
            'old_password'   => 'required',
            'new_password'   => 'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user = User::where('api_token',$request->token)->first();
        if($user==null)
        {
            $response['status']     = "failed";
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }else{
            if (\Hash::check($request->old_password, $user->password)) {
                // true

                $user->password = $request->new_password;
                $user->update();
                $response['status']     = "success";
                $response['message']    = "Update Password Berhasil";
                $statusCode             = 201;
            }else
            {
                // false
                $response['status']     = "failed";
                $response['message']    = "Password Lama Salah";
                $statusCode             = 401;
            }
        }

        return response()->json($response,$statusCode);
    }

    public function registerAmbasador(Request $request)
    {
        $validator              = \Validator::make($request->all(), [
            'token'             =>  'required',
            'village_id'        => 'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = User::where('api_token',$request->token)->first();
        $village    = \DB::table('villages')->where('id',$request->village_id)->first();
        
        if($user==null and $village==null)
        {
            $response['status']     = false;
            $response['message']    = "Invalid Token";
            $statusCode             = 401;
        }else{

            \DB::table('village_ambasadors')->insert([
                'id'            =>  Uuid::uuid4(),
                'user_id'       =>  $user->id,
                'village_id'    =>  $request->village_id,
                'created_at'    =>  date('Y-m-d H:i:s'),
                'created_at'    =>  date('Y-m-d H:i:s')
            ]);

            $response['status']     = true;
            $response['message']    = "Pendaftaran Ambasador Berhasil";
            $statusCode             = 201;
        }

        return response()->json($response,$statusCode);
    }


    public function storeDeliveryDestination(Request $request)
    {
        $validator              = \Validator::make($request->all(), [
            'token'             =>  'required',
            'village_id'        =>  'required',
            'street'           =>  'required',
            'phone'             =>  'required',
            'name'              =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = User::where('api_token',$request->token)
                      ->first();

        $village    = \DB::table('villages')
                        ->where('id',$request->village_id)
                        ->first();

        if($user==null)
        {
            $response['status']     = false;
            $response['message']    = "Gagal Mendaftarkan Alamat";
            $statusCode             = 400;

        }else
        {
            if(\DB::table('user_delivery_destinations')->where('user_id',$user->id)->first()==null)
            {
                $deliveryDestination = [
                    'id'            =>  Uuid::uuid4(),
                    'name'          =>  $request->name,
                    'phone'         =>  $request->phone,
                    'street'        =>  $request->street,
                    'village_id'    =>  $request->village_id,
                    'user_id'       =>  $user->id,
                    'default'       => 'y'
                ];
            }else
            {
                $deliveryDestination = [
                    'id'            =>  Uuid::uuid4(),
                    'name'          =>  $request->name,
                    'phone'         =>  $request->phone,
                    'street'        =>  $request->street,
                    'village_id'    =>  $request->village_id,
                    'user_id'       =>  $user->id,
                    'default'       => 'n'
                ];
            }
        
            \DB::table('user_delivery_destinations')->insert($deliveryDestination);
            
            $response['status']     = true;
            $response['message']    = "Berhasil Menambahkan Alamat";
            $statusCode             = 201;
        }
        return response()->json($response,$statusCode);
    }


    public function getDeliveryDestination($token,UserDeliveryDestinationTransformer $uddt)
    {
        $user       = User::where('api_token',$token)->first();
        if($user==null)
        {
            $response['status']     = false;
            $response['message']    = "token invalid";
            $statusCode             = 400;

        }else
        {
            $userDelivery = UserDeliveryDestination::where('user_id',$user->id)->orderBy('created_at','DESC')->get();
            //dd($userDelivery);
            $userDelivery = new Collection($userDelivery, $uddt);
            $userDelivery = $this->fractal->createData($userDelivery); 
            $response = $userDelivery->toArray();
            $response['status']     = true;
            $response['message']    = "Gagal Mendaftarkan Alamat";
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }
}
