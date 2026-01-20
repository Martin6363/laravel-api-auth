<?php

namespace Martin6363\ApiAuth\Http\Controllers\v1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
}