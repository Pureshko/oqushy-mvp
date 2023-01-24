<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostFiles extends Model
{
    use HasFactory;
    protected $table = "lostfiles";
    public function createLostFile($url,$lostId){
        return $this->insert(['url'=>$url,'lost_id'=>$lostId]);
    }
}
