<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SFToken extends Model
{
    use HasFactory;
    protected $table="sf_tokens";

    protected $guarded = [];

}
