<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    /**
     * Returns the full table name with db prefix for a model
     *
     * @return string
     */
    public static function getFullTableName()
    {
        return \DB::getTablePrefix() . with(new static)->getTable();
    }
}
