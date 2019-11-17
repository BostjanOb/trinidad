<?php

namespace App\Http\Controllers;

use App\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Domain $domain)
    {
        //
    }

    public function update(Request $request, Domain $domain)
    {
        //
    }

    public function destroy(Domain $domain)
    {
        //
    }
}
