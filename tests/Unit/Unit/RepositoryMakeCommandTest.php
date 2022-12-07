<?php

namespace Tests\Unit\Unit;

use Tests\TestCase;

class RepositoryMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_make_repository_class()
    {
        $this->artisan('make:repository', ['name' => 'Test', '--no-interaction' => true]);

        $this->assertTrue(file_exists(app_path('Entities/Tests/TestsRepository.php')));
        $string = "<?php

namespace App\Entities\Tests;

use App\Entities\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class TestsRepository extends BaseRepository
{
    protected \$model;

    public function __construct(Test \$model)
    {
        parent::__construct(\$model);
    }


}";
        $this->assertEquals($string, file_get_contents(app_path('Entities/Tests/TestsRepository.php')));
        if (file_exists(app_path('Entities/Tests')))
            exec('rm -r app/Entities/Tests');
        $this->assertFalse(file_exists(app_path('Entities/Tests/TestsRepository.php')));
        $this->assertFalse(file_exists(app_path('Entities/Tests')));
    }
}
