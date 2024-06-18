<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $tenancy = true;
    protected $tenant;
    protected $domains;
    protected $domain;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function getTenant()
    {
        return $this->tenant;
    }

    public function initializeTenancy()
    {
        $this->tenant = Tenant::factory()->create([
            'id' => 'asl',
            'plan' => 'premium'
        ]);
        $this->domains = $this->tenant->domains()->create([
            'domain' => 'asl.next.test',
        ]);
        $this->domain = $this->domains->first()->domain;

        tenancy()->initialize($this->tenant);
    }
}
