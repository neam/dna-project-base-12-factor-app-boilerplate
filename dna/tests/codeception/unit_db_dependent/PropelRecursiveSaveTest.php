<?php

class PropelRecursiveSaveTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testSavingFileAlsoSavesNewFileInstance()
    {

        $file = new \propel\models\File();
        $fileInstance = $file->getFileInstanceRelatedByLocalFileInstanceId();
        $this->assertEmpty($fileInstance);
        if (!($fileInstance)) {
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->setStorageComponentRef('foo');
            $file->setFileInstanceRelatedByLocalFileInstanceId($fileInstance);
        }
        $fileInstance->setDataJson("foo");
        $file->save();

        $fetchedFile = \propel\models\FileQuery::create()->findPk(1);
        $fetchedFileInstance = $fetchedFile->getFileInstanceRelatedByLocalFileInstanceId();

        $this->assertEquals($file->getId(), $fetchedFile->getId());
        $this->assertEquals($fileInstance->getDataJson(), $fetchedFileInstance->getDataJson());
        $this->assertEquals($fileInstance->getId(), $fetchedFileInstance->getId());
        $this->assertEquals($fileInstance->getStorageComponentRef(), $fetchedFileInstance->getStorageComponentRef());

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testSavingFileAlsoSavesExistingFileInstance()
    {

        $file = \propel\models\FileQuery::create()->findPk(1);
        $fileInstance = $file->getFileInstanceRelatedByLocalFileInstanceId();
        $this->assertNotEmpty($fileInstance);
        if (!($fileInstance)) {
            $fileInstance = new \propel\models\FileInstance();
            $fileInstance->setStorageComponentRef('foo');
            $file->setFileInstanceRelatedByLocalFileInstanceId($fileInstance);
        }
        $fileInstance->setDataJson("foo");
        $file->save();

        $fetchedFile = \propel\models\FileQuery::create()->findPk(1);
        $fetchedFileInstance = $fetchedFile->getFileInstanceRelatedByLocalFileInstanceId();

        $this->assertEquals($file->getId(), $fetchedFile->getId());
        $this->assertEquals($fileInstance->getDataJson(), $fetchedFileInstance->getDataJson());
        $this->assertEquals($fileInstance->getId(), $fetchedFileInstance->getId());
        $this->assertEquals($fileInstance->getStorageComponentRef(), $fetchedFileInstance->getStorageComponentRef());

    }

}
