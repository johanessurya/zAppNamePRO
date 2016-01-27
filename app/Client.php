<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'clientCode', 'name', 'gender', 'type'
    ];

    protected $primaryKey = 'id';
    protected $table = 'client';

    public $timestamps = false;
}
