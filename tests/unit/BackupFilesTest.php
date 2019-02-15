<?php

class BackupFilesTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \DataStaging\BackupFiles
     */
    protected $backupFiles;
    protected $originalEnvImportDir;
    protected $originalEnvExportDir;
    protected $originalEnvBackupDir;

    protected function _before()
    {
        // Set env variables for this test
        $this->originalEnvImportDir = getenv('IMPORT_DIR');
        $this->originalEnvExportDir = getenv('EXPORT_DIR');
        $this->originalEnvBackupDir = getenv('BACKUP_DIR');

        putenv('IMPORT_DIR=/var/jail/home/');
        putenv('EXPORT_DIR=/var/jail/ucentive/home/');
        putenv('BACKUP_DIR=/var/schooldata/');

        // Mock School
        $schoolStub = $this->getMockBuilder('\DataStaging\Models\School')
            ->setMethods(['getFullImportPath', 'getFullExportPath', 'getBackupPath',])
            ->getMock();

        $schoolStub->method('getFullImportPath')
            ->willReturn('/var/jail/home/taylor');

        $schoolStub->method('getFullExportPath')
            ->willReturn('/var/jail/ucentive/home/taylor');

        $schoolStub->method('getBackupPath')
            ->willReturn('taylor');

        // Mock Filesystem interaction
//        $fileSystemMock = $this->getMockBuilder('Illuminate\Filesystem\Filesystem')
//            ->setMethods(['files', 'copy'])
//            ->getMock();

        $this->backupFiles = app('\DataStaging\BackupFiles', [$schoolStub]);
    }

    protected function _after()
    {
        // reset the environment vars that I monkeyed with
        putenv("IMPORT_DIR={$this->originalEnvImportDir}");
        putenv("EXPORT_DIR={$this->originalEnvExportDir}");
        putenv("BACKUP_DIR={$this->originalEnvBackupDir}");
    }

    // tests
    public function testShouldDoBackupNowTrue()
    {
        $this->tester->amGoingTo('Test that the backup runs on the hour its scheduled to run.');

        $currentHour = \Carbon\Carbon::now()->format('G');
        putenv("BACKUP_SCHEDULE=$currentHour");

        $this->assertTrue($this->backupFiles->shouldDoBackupNow());
    }

    public function testShouldDoBackupNowFalse()
    {
        $this->tester->amGoingTo('Test that the backup does NOT on an hour it is NOT scheduled to run.');

        $nonCurrentHour = new \Carbon\Carbon('+5 hours');
        putenv("BACKUP_SCHEDULE=$nonCurrentHour");

        $this->assertFalse($this->backupFiles->shouldDoBackupNow());
    }

    /**
     * @dataProvider directoriesSetCorrectlyProvider
     * @param $backupType
     * @param $sourceDir
     */
    public function testDirectoriesSetCorrectly($backupType, $sourceDir)
    {
        $this->tester->amGoingTo('Test that the sourceDir and DestinationDir are set correctly.');

        // setup
        $now = \Carbon\Carbon::now();
        $year = $now->format('Y');
        $month = $now->format('M');

        // act
        $this->backupFiles
            ->setBackupType($backupType);

        // assert
        $this->assertEquals( // test SOURCE directory
            $sourceDir,
            $this->backupFiles->getSourceDir()
        );


        $this->assertEquals(    // Test DESTINATION directory
            "/var/schooldata/taylor/$year/$month/$backupType/",
            $this->backupFiles->getDestinationDir()
        );
    }

    public function directoriesSetCorrectlyProvider()
    {
        return [
            ['school_data_files', '/var/jail/home/taylor'], //'for files from school' =>
            ['sidewalk_files', '/var/jail/ucentive/home/taylor'], //'for files we send to sidewalk' =>
        ];
    }

//    public function testBackup()
//    {
//        $root = vfsStream::setup('var');
//        // use the actual file system to seed the virtual file sys?
////        vfsStream::copyFromFileSystem('/home/vagrant/projects/mockRepoServer/taylor/data_files', $root);
//
////        dump(vfsStream::url('var/jail/home/taylor/data_files/test.csv'));
////        file_put_contents(vfsStream::url('var/jail/home/taylor/data_files/test.csv'), 'test contents');
//        vfsStream::newFile('test.csv')->at($root)->setContent("The new contents of the file");
//
////        dd($root->hasChild('var/test.txt'));
//
//        $this->backupFiles
//            ->setBackupType('school_data_files')
//            ->copyFileToDestinationAddingTimestamp(vfsStream::url('var/test.csv'));
//
//
//    }
}