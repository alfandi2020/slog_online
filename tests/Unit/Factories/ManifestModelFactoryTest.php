<?php

namespace Tests\Unit\Factories;

use App\Entities\Manifests\Manifest;
use App\Entities\Manifests\Type as ManifestType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManifestModelFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_manifest_can_have_a_type_name()
    {
        $typeCode = ManifestType::getById(1);
        $manifest = factory(Manifest::class, $typeCode)->create();
        $this->assertEquals(trans('manifest.' . $typeCode), $manifest->type());
    }

    /** @test */
    public function a_manifest_can_have_a_type_code()
    {
        $typeCode = ManifestType::getById(1);
        $manifest = factory(Manifest::class, $typeCode)->create();
        $this->assertEquals($typeCode, $manifest->typeCode());
    }

    /** @test */
    public function a_manifest_can_have_a_type_color()
    {
        $typeCode = ManifestType::getById(1);
        $manifest = factory(Manifest::class, $typeCode)->create();
        $this->assertEquals(ManifestType::getColorById(1), $manifest->typeColor());
    }

    /** @test */
    public function generate_an_accounting_manifest()
    {
        $number = 'M563000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT);
        $manifest = factory(Manifest::class, 'accounting')->create(['number' => $number]);
        $expectedManifestData = [
            'id' => $manifest->id,
            'number' => $number,
            'type_id' => 5,
            'orig_network_id' => 1, // Kalsel
            'dest_network_id' => 1, // Kalsel
            'weight' => null,
            'pcs_count' => null,
            'creator_id' => 5, // Seeded CS User
            'handler_id' => null,
            'deliver_at' => null,
            'received_at' => null,
            'notes' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->assertEquals($expectedManifestData, $manifest->toArray());
    }

    /** @test */
    public function generate_a_sent_accounting_manifest()
    {
        $number = 'M563000000' . date('ym') . str_pad(rand(1,999), 3, STR_PAD_LEFT);
        $manifest = factory(Manifest::class, 'accounting')->states('sent')->create(['number' => $number]);
        $expectedManifestData = [
            'id' => $manifest->id,
            'number' => $number,
            'type_id' => 5,
            'orig_network_id' => 1, // Kalsel
            'dest_network_id' => 1, // Kalsel
            'weight' => null,
            'pcs_count' => null,
            'creator_id' => 5, // Seeded CS User
            'handler_id' => null,
            'deliver_at' => date('Y-m-d H:i:s'),
            'received_at' => null,
            'notes' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->assertEquals($expectedManifestData, $manifest->toArray());
    }
}
