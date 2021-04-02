<?php

namespace App;

use App\utils\randstring;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    public function __construct()
    {
        // Do nothing
    }
}
