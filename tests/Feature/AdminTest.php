<?php

namespace Tests\Feature;

use App\adminlist;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function testAdminRecognize()
    {
        $this->assertTrue(adminlist::isadmin("admin"));
    }
}
