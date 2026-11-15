<?php
namespace App\Http\Controllers;

abstract class Controller
{
    protected function cities(): array { return config('janwar.cities'); }
}
