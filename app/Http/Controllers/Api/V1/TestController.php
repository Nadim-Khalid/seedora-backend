<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseApiController;

class TestController extends BaseApiController
{
    public function index()
    {
        $data = [
            'name'=> 'nadim',
            'project'=> 'seedora backend',
        ];
        return $this->sendResponse($data,'Test Api Fetched successfully');
    }
}
