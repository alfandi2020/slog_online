<?php

namespace Tests\Feature\Backups;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\BrowserKitTestCase;

class ManageBackupTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_create_new_backup_file()
    {
        $this->loginAsAdmin();

        $this->visit(route('admin.backups.index'));
        $this->seePageIs(route('admin.backups.index'));
        $this->type('new_backup.1231231231','file_name');
        $this->press(trans('backup.create'));

        $this->seePageIs(route('admin.backups.index'));

        $this->assertTrue(file_exists(storage_path('app/backup/db') . '/new_backup.1231231231.gz'));
        unlink(storage_path('app/backup/db') . '/new_backup.1231231231.gz');
        $this->assertFalse(file_exists(storage_path('app/backup/db') . '/new_backup.1231231231.gz'));
    }

    /** @test */
    public function it_can_delete_a_backup_file()
    {
        $this->loginAsAdmin();

        $this->visit(route('admin.backups.index'));
        $this->seePageIs(route('admin.backups.index'));
        $this->type('new_backup1231231231','file_name');
        $this->press(trans('backup.create'));

        $this->seePageIs(route('admin.backups.index'));
        $this->assertTrue(file_exists(storage_path('app/backup/db') . '/new_backup1231231231.gz'));

        $this->click('del_new_backup1231231231');
        $this->press(trans('backup.confirm_delete'));
        $this->assertFalse(file_exists(storage_path('app/backup/db') . '/new_backup1231231231.gz'));
    }

    /** @test */
    public function it_can_upload_a_backup_file()
    {
        $this->loginAsAdmin();

        $this->visit(route('admin.backups.index'));
        $this->seePageIs(route('admin.backups.index'));
        $this->attach(storage_path('app') . '/backup_test_file.gz', 'backup_file');
        $this->press(trans('backup.upload'));

        $this->seePageIs(route('admin.backups.index'));
        $this->assertTrue(file_exists(storage_path('app/backup/db') . '/backup_test_file.gz'));
        unlink(storage_path('app/backup/db') . '/backup_test_file.gz');
        $this->assertFalse(file_exists(storage_path('app/backup/db') . '/backup_test_file.gz'));
    }

    /** @test */
    public function it_can_download_a_backup_file()
    {
        $this->loginAsAdmin();

        $this->visit(route('admin.backups.index'));
        $this->seePageIs(route('admin.backups.index'));
        $this->attach(storage_path('app') . '/backup_test_file.gz', 'backup_file');
        $this->press(trans('backup.upload'));

        $this->seePageIs(route('admin.backups.index'));
        $this->assertTrue(file_exists(storage_path('app/backup/db') . '/backup_test_file.gz'));
        $response = $this->get(route('admin.backups.download', 'backup_test_file.gz'));
        $this->assertResponseOk();
        unlink(storage_path('app/backup/db') . '/backup_test_file.gz');
        $this->assertFalse(file_exists(storage_path('app/backup/db') . '/backup_test_file.gz'));
    }
}
