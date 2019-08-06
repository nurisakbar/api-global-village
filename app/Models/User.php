<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    //use Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','id', 'password','phone','photo','api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function harvests()
    {
        return $this->hasMany('App\Models\Harvest');
    }

    public function village()
    {
        return $this->belongsTo('App\Models\Village');
    }

    public function full_address()
    {
        return $this->belongsTo('App\Models\ViewRegion','village_id','village_id');
    }

    public function lands()
    {
        return $this->hasMany('App\Models\Land');
    }

    public function order_item()
    {
        return $this->hasMany('App\Models\OrderItemView','user_id_seller')->where('order_id',null);
    }



    public $incrementing = false;
}
