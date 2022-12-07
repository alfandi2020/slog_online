<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Entities\Services\Service;

class ServiceReferenceTest extends TestCase
{
    /** @test */
    public function retrieve_services_list()
    {
        $service = new Service;

        $this->assertEquals([
            11 => __('service.exp'),
            21 => __('service.eco'),
            22 => __('service.reg'),
            41 => __('service.brg'),
        ], $service->toArray());
    }

    /** @test */
    public function retrieve_service_name_by_id()
    {
        $service = new Service;
        $this->assertEquals(__('service.exp'), $service->getNameById(11));
        $this->assertEquals(__('service.eco'), $service->getNameById(21));
        $this->assertEquals(__('service.reg'), $service->getNameById(22));
        $this->assertEquals(__('service.brg'), $service->getNameById(41));
    }
}
