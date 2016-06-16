<?php

class PropelPdoErrorHandlingTest extends \PHPUnit_Framework_TestCase
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
    public function testCorrectParameters()
    {

        $con = \Propel\Runtime\Propel::getWriteConnection('default');
        $params = [":foo" => 1];
        $sql = "SELECT :foo";
        $stmt = $con->prepare($sql);
        $result = $stmt->execute($params);
        $this->assertTrue($result);
        $rows = $stmt->fetchAll();

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testIncorrectParameters()
    {

        $con = \Propel\Runtime\Propel::getWriteConnection('default');
        $params = [":bar" => 1];
        $sql = "SELECT :foo";
        $stmt = $con->prepare($sql);
        $this->setExpectedException('PDOException');
        $result = $stmt->execute($params);
        $rows = $stmt->fetchAll();

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testParametersGivenWhenNoParametersAreUsedInStatement()
    {

        if ((defined('HHVM_VERSION') && version_compare(PHP_VERSION, '3.13.1') > 0) || version_compare(
                PHP_VERSION,
                '7.0.7'
            ) > 0
        ) {
            $this->markTestSkipped(
                "Skipping testParametersGivenWhenNoParametersAreUsedInStatement for PHP <= 7.0.7 since known bug"
            );
        }

        $con = \Propel\Runtime\Propel::getWriteConnection('default');
        $params = [":bar" => 1];
        $sql = "SELECT 1";
        $stmt = $con->prepare($sql);
        $this->setExpectedException('Exception');
        $result = $stmt->execute($params);
        $this->assertTrue(
            $result,
            'If $stmt->execute() did not throw an exception, the statement should have been executed'
        );
        $rows = $stmt->fetchAll();

    }

}
