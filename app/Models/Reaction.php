<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    const UPDATED_AT =null;
    use HasFactory;
   
    protected $fillable = ['user_id','type', 'object_id','object_type'];
    
    /**
     * get the parent object model (Post or Comment)
     */
    public function object():MorphTo{
        return $this->morphTo();
    }
}