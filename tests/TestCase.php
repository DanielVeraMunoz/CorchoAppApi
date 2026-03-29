<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {

        parent::setUp();

        if (in_array(
            \Illuminate\Foundation\Testing\RefreshDatabase::class,
            class_uses_recursive($this)
        )) {
            $this->createPassportClient();
        }
    }

    protected function createPassportClient(): void
      {
          \Laravel\Passport\Client::create([
              'name' => 'Test Personal Access Client',
              'secret' => null,
              'provider' => 'users',
              'redirect_uris' => [],
              'grant_types' => ['personal_access'],
              'revoked' => false,
          ]);
      }
}
