<?php

namespace Tests\Unit;

use App\Entities\Manifests\Type as ManifestType;
use Tests\TestCase;

class ManifestTypeTest extends TestCase
{
    /** @test */
    public function retrieve_manifest_types_list()
    {
        $manifestType = new ManifestType;

        $this->assertEquals([
            1 => trans('manifest.handover'),
            trans('manifest.delivery'),
            trans('manifest.distribution'),
            trans('manifest.return'),
            trans('manifest.accounting'),
            trans('manifest.problem'),
        ], $manifestType->toArray());
    }

    /** @test */
    public function retrieve_manifest_type_code_by_id()
    {
        $manifestType = new ManifestType;
        $this->assertEquals('handover', $manifestType->getById(1));
        $this->assertEquals('delivery', $manifestType->getById(2));
        $this->assertEquals('distribution', $manifestType->getById(3));
        $this->assertEquals('return', $manifestType->getById(4));
        $this->assertEquals('accounting', $manifestType->getById(5));
        $this->assertEquals('problem', $manifestType->getById(6));
    }

    /** @test */
    public function retrieve_manifest_type_name_by_id()
    {
        $manifestType = new ManifestType;
        $this->assertEquals(trans('manifest.handover'), $manifestType->getNameById(1));
        $this->assertEquals(trans('manifest.delivery'), $manifestType->getNameById(2));
        $this->assertEquals(trans('manifest.distribution'), $manifestType->getNameById(3));
        $this->assertEquals(trans('manifest.return'), $manifestType->getNameById(4));
        $this->assertEquals(trans('manifest.accounting'), $manifestType->getNameById(5));
        $this->assertEquals(trans('manifest.problem'), $manifestType->getNameById(6));
    }

    /** @test */
    public function retrieve_manifest_plural_type_code_by_id()
    {
        $manifestType = new ManifestType;
        $this->assertEquals('handovers', $manifestType->getPluralCodeById(1));
        $this->assertEquals('deliveries', $manifestType->getPluralCodeById(2));
        $this->assertEquals('distributions', $manifestType->getPluralCodeById(3));
        $this->assertEquals('returns', $manifestType->getPluralCodeById(4));
        $this->assertEquals('accountings', $manifestType->getPluralCodeById(5));
        $this->assertEquals('problems', $manifestType->getPluralCodeById(6));
    }
}
