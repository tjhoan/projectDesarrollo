<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DatabaseTest extends TestCase
{
    public function testUsingTestDatabase()
    {
        // Confirmamos que el nombre de la base de datos actual es 'projectDesarrollo_test'
        $this->assertEquals('projectDesarrollo_test', DB::connection()->getDatabaseName());
    }
}
