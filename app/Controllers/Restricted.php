<?php

namespace  App\Controllers;
class Restricted extends BaseController
{

	public function __construct()
	{
		
	}

    public function index()
	{
        return view('restricted', [
            'title' => 'Restricted! Access Denied'
        ]);
    }

}
