<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseApiFilesModel extends Model
{
    use HasFactory;
    abstract protected function createFile($url,$id);
    abstract protected function destroyFile($id);

}
