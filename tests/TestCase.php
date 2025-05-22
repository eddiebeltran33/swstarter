<?php

namespace Tests;

use App\Services\SWAAPIClient;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    public function mockSWAAPIClient()
    {
        $mock = Mockery::mock(SWAAPIClient::class);
        $this->instance(SWAAPIClient::class, $mock);
        return $mock;
    }
}
