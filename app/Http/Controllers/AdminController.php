<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('AccessApi');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    public function login(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'email'        =>  'required',
            'password'     =>  'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $admin = Admin::where('email',$request->email)->first();
        if($admin==null)
        {
            $response['success'] = false;
            $response['message'] = "email tidak terdaftar";
            $response['data']    = null;
        }else
        {
            if (Hash::check($request->password,$admin->password)) {
                $response['success'] = true;
                $response['message'] = "berhasil login";
                $response['data']    = $admin = Admin::where('email',$request->email)->select('id','name','email')->first();
            }else
            {
                $response['success'] = false;
                $response['message'] = "password anda salah";
                $response['data']    = null;
            }
        }

        return response()->json($response,200);
    }

    public function register(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'name'         =>  'required',
            'email'        =>  'required|unique:admins',
            'password'     =>  'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $adminData              = $request->only('name','email','password');
        $adminData['id']        = Uuid::uuid4();
        $adminData['password']  = Hash::make($request->password);
        $adminData['created_at']= date('Y-m-d H:i:s'); 
        $adminData['updated_at']= date('Y-m-d H:i:s'); 
        
        try {

            $admin               = Admin::insert($adminData);
            $response['success'] = true;
            $response['message'] = "berhasil mendaftar";
            $response['data']    = $admin;

        } catch (Exception $e) {
                
            $response['success'] = false;
            $response['message'] = report($e);
        }

        return response()->json($response,200);
    }
}
