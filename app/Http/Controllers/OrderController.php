<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Order;
use Ramsey\Uuid\Uuid;
use App\Transformers\OrderItemTransformer;
use App\Transformers\PurchaseTransformer;
use App\Transformers\PurchaseDetailTransformer;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Resource\Item as Item;
use App\Models\User;
use App\Models\OrderHistory;

class OrderController extends Controller
{
    private $fractal;
    private $OrderItemTransformer;
    //private $OrderTransformer;

    public function __construct(PurchaseTransformer $PurchaseTransformer,OrderItemTransformer $OrderItemTransformer,Fractal\Manager $fractal)
    {
        $this->middleware('AccessApi');
        $this->OrderItemTransformer = $OrderItemTransformer;
        $this->PurchaseTransformer = $PurchaseTransformer;
        //$this->OrderTransformer = $OrderTransformer;
        $this->fractal = new Fractal\Manager();
    }

    
    function addItem(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'token'        =>  'required',
            'product_id'   =>  'required:integer',
            'qty'          =>  'required:integer'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$request->token)->first();
        $product    = \DB::table('products')->select('id','price','user_id')->where('id',$request->product_id)->first();

        if(!isset($user))
        {
            $response['message'] = "invalid token";
            $response['success'] = false;
            $statusCode  = 202;
        }elseif(!isset($product))
        {
            $response['message'] = "invalid product id";
            $response['success'] = false;
            $statusCode  = 404;
        }elseif($user->id==$product->user_id)
        {
            $response['message'] = "gak boleh beli barang sendiri";
            $response['success'] = false;
            $statusCode  = 404;
        }
        else{

            $isExist = \DB::table('order_items')
                        ->where('user_id',$user->id)
                        ->where('order_id',null)
                        ->where('product_id',$request->product_id)
                        ->first();
            if($isExist==null)
            {
                // insert new
                $input = [
                    'user_id'       =>  $user->id,
                    'id'            =>  Uuid::uuid4(),
                    'product_id'    =>  $request->product_id,
                    'qty'           =>  $request->qty,
                    'price'         =>  $product->price,
                    'created_at'    => date('y-m-d H:i:s'),
                    'updated_at'    => date('y-m-d H:i:s')
                ];
                \DB::table('order_items')->insert($input);
            }else
            {
                \DB::table('order_items')
                        ->where('user_id',$user->id)
                        ->where('product_id',$request->product_id)
                        ->where('order_id',null)
                        ->update(['qty'=>$isExist->qty+1]);
            }

            $response['success'] = true;
            $response['message'] = "Berhasil Menambahkan Ke Keranjang Belanja";
            $statusCode  = 200;
        }
        return response()->json($response,$statusCode);
    }

    // melakukan update qty order item
    public function updateQty(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            'id'            =>  'required:integer',
            'qty'           =>  'required:integer',
            'token'         =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')
                        ->where('api_token',$request->token)
                        ->first();

        $orderItem  = OrderItem::where('id',$request->id)
                        ->where('order_id',null)
                        ->where('user_id',$user->id)
                        ->first();
        
        if($user==null)
        {
            $response['success'] = false;
            $response['message'] = "unauthorize";
            $statusCode  = 401;
        }
        elseif($orderItem==null)
        {
            $response['success'] = false;
            $response['message'] = "id tidak ditemukan";
            $statusCode  = 401;
        }else
        {
            $orderItem->qty = $request->qty;
            $orderItem->update();

            $response['success'] = true;
            $response['message'] = "Berhasil Update Qty";
            $statusCode  = 200;
        }

        return response()->json($response,$statusCode);
    }

    public function createInvoiceNumber()
    {
        $char = "INV".date('Ymd');
        $order = \DB::select("SELECT max(invoice_number) as maxInvoice FROM orders where left(invoice_number,11)='".$char."'");
        $maxInvoice = $order[0]->maxInvoice;
        $noUrut = (int) substr($maxInvoice, 11, 4);
        $noUrut++;
        return $char . sprintf("%04s", $noUrut);
    }

    public function checkOut(Request $request)
    {
        $validator          = \Validator::make($request->all(), [
            //'id'            =>  'required:integer',
            //'qty'           =>  'required:integer',
            'token'         =>  'required'
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')
                        ->select('id')
                        ->where('api_token',$request->token)
                        ->first();
        if($user!=null)
        {
            $orderItem = OrderItem::where('order_id',null)
            ->where('user_id',$user->id)
            //->where('order_id',null)
            ->first();
            //dd($orderItem);

            if($orderItem==null)
            {
                $response['success'] = false;
                $response['message'] = "belum ada item belanja";
                $statusCode  = 401;
            }else
            {
                $seller = [];
                $orderItem = OrderItem::where('user_id',$user->id)->where('order_id',null)->get();
                foreach($orderItem as $item)
                {
                    array_push($seller,$item->product->user_id);
                }

                $sellers = array_unique($seller);
                foreach($sellers as $seller)
                {
                    if(isset($request->note))
                    {
                        $note = $request->note;
                    }else
                    {
                        $note = null;
                    }
                    $orderId = Uuid::uuid4();
                    $order = new Order();
                    $order->id              = $orderId;
                    $order->order_status    = 'pending';
                    $order->user_id_seller  = $seller;
                    $order->user_id_buyer   = $user->id;
                    $order->note            = $note;
                    $order->invoice_number  = $this->createInvoiceNumber();
                    $order->save();

                    \DB::table('order_histories')->insert(['id'=>Uuid::uuid4(),'order_id'=>$orderId,'status'=>'pending','description'=>'menunggu pembayaran','created_at'=>date('Y-m-d H:i:s')]);

                    // update status order
                    $orderItemId = \DB::table('order_item_view')
                                ->select('id')
                                ->where('order_id',null)
                                ->where('user_id_buyer',$user->id)
                                ->where('user_id_seller',$seller)
                                ->get();
                
                    foreach($orderItemId as $w)
                    {
                        \DB::select('update order_items SET order_id="'.$orderId.'" where id="'.$w->id.'"');
                    } 
                }


                $response['success'] = true;
                $response['message'] = "Berhasil Membuat Order";
                $statusCode  = 200;
            }
                    }
        else
        {
            $response['success'] = false;
            $response['message'] = "token invalid";
            $statusCode  = 401;
        }
        

        return response()->json($response,$statusCode);
    }

    function getItem($token,$orderId=null)
    {
        if(isset($token))
        {
            $user       = \DB::table('users')->select('id')->where('api_token',$token)->first();
            if(isset($user))
            {
                $seler = [];
                $orderItem = OrderItem::where('user_id',$user->id)->where('order_id',null)->get();
                foreach($orderItem as $item)
                {
                    array_push($seler,$item->product->user_id);
                }
                $response['success'] = true;
                $response['status']  = 200;
                $response['data'] = User::with('order_item')->whereIn('id',array_unique($seler))->select('id','name as seller_name')->get();
                
            }else
            {
                $response['success'] = false;
                $response['message'] = "invalid token";
                $response['status']  = 401;
            }

        }else
        {
            $response['success'] = false;
            $response['message'] = "token tidak boleh kosong";
            $response['status']  = 404;
        }
        return response()->json($response,$response['status']);
    }

    

    // data yang di keranjang belanja
    function getItems($token,$orderId=null)
    {
        if(isset($token))
        {
            $user       = \DB::table('users')->select('id')->where('api_token',$token)->first();
            return $user->id;
            
            if(isset($user))
            {
                $orderItems = OrderItem::where('user_id',$user->id)->where('order_id',null)->orderBy('created_at','DESC')->get();
                $orderItems = new Collection($orderItems, $this->OrderItemTransformer);
                $orderItems = $this->fractal->createData($orderItems); 
                $response = $orderItems->toArray();
                $response['success'] = true;
                $response['status']  = 200;
            }else
            {
                $response['success'] = false;
                $response['message'] = "invalid token";
                $response['status']  = 401;
            }

        }else
        {
            $response['success'] = false;
            $response['message'] = "token tidak boleh kosong";
            $response['status']  = 404;
        }
        return response()->json($response,$response['status']);
    }

    function deleteItem($id)
    {
        $orderItem = OrderItem::find($id);
        if($orderItem==null)
        {
            $response['status'] = false;
            $response['message'] = "id product invalid";
            $statusCode  = 404;
        }else
        {
            $orderItem->delete();
            $response['status'] = false;
            $response['message'] = "berhasil menghapus product";
            $statusCode  = 200;
        }
        return response()->json($response,$statusCode);
    }

    function purchase($token)
    {
        $user       = \DB::table('users')->select('id')->where('api_token',$token)->first();
        if($user==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid token";
            $statusCode             = 401;
        }else
        {
            $purchase               = Order::where('user_id_buyer',$user->id)->orderBy('created_at','DESC')->get();
            $purchase = new Collection($purchase, $this->PurchaseTransformer);
            $purchase = $this->fractal->createData($purchase); 
            $response = $purchase->toArray();
            $response['status']     = true;
            $statusCode             = 200;
            //$response['message']    = "berhasil menghapus product";
        }
        return response()->json($response,$statusCode);
    }

    public function purchaseDetail($id, PurchaseDetailTransformer $PurchaseDetailTransformer)
    {
        $order                  = Order::where('id',$id)->get();
        $order                  = new Collection($order, $PurchaseDetailTransformer);
        $order                  = $this->fractal->createData($order); 
        $response               = $order->toArray();
        $response['status']     = true;
        $statusCode             = 200;

        return response()->json($response,$statusCode);
    }

    function selling($token)
    {
        $user       = \DB::table('users')->select('id')->where('api_token',$token)->first();
        if($user==null)
        {
            $response['status']     = false;
            $response['message']    = "invalid token";
            $statusCode             = 401;
        }else
        {
            $selling                = Order::where('user_id_seller',$user->id)->orderBy('created_at','DESC')->get();
            $selling                = new Collection($selling, $this->PurchaseTransformer);
            $selling                = $this->fractal->createData($selling); 
            $response               = $selling->toArray();
            $response['status']     = true;
            $statusCode             = 200;
        }
        return response()->json($response,$statusCode);
    }


    public function confirm(Request $request){
        $validator          = \Validator::make($request->all(), [
            'token'         =>  'required|unique:articles',
            'order_id'      =>  'required',
            'activity'      =>  'required',
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $user       = \DB::table('users')->select('id')->where('api_token',$token)->first();
        if($user==null)
        {
            $response['message'] = "invalid token";
        }else{
            if($request->activity=='pengiriman')
            {
                $order = Order::where('user_id_seller',$user->id)->first();
                if($order!==null)
                {
                    // update status order menjadi pengiriman
                    $order->order_status = "pengiriman";
                    $order->update();
                    \DB::table('order_histories')->insert(['id'=>Uuid::uuid4(),'order_id'=>$order->id,'status'=>'pengiriman','description'=>'penjual','created_at'=>date('Y-m-d H:i:s')]);
                }else
                {
                    $response['message'] = "un authorized";
                }
            }elseif($request->activity=='penerimaan')
            {
                $order = Order::where('user_id_buyer',$user->id)->first();
                if($order!==null)
                {
                    // update status order menjadi selesai
                }else
                {
                    $response['message'] = "un authorized";
                }
            }
        }
        return response()->json($response,$statusCode);
    }
    
}
