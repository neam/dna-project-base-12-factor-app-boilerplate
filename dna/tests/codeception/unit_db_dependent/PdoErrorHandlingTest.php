<?php

class PdoErrorHandlingTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    protected function getPdo()
    {
        $dbh = new PDO(
            'mysql:host=' . DATABASE_HOST . ';port=' . DATABASE_PORT . ';dbname=' . DATABASE_NAME,
            DATABASE_USER,
            DATABASE_PASSWORD
        );
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testPhpBug70313()
    {

        $dbh = $this->getPdo();
        $stmt = $dbh->prepare(");");

        $a = 'a';
        $b = 'b';

        $stmt->bindParam(1, $a);
        $stmt->bindParam(2, $b);
        $this->setExpectedException('PDOException');
        $stmt->execute();

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testCorrectParameters()
    {

        $dbh = $this->getPdo();
        $params = [":foo" => 1];
        $sql = "SELECT :foo";
        $stmt = $dbh->prepare($sql);
        $result = $stmt->execute($params);
        $this->assertTrue($result);
        $rows = $stmt->fetchAll();

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testIncorrectParameters()
    {

        $dbh = $this->getPdo();
        $params = [":bar" => 1];
        $sql = "SELECT :foo";
        $stmt = $dbh->prepare($sql);
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
                "Skipping testParametersGivenWhenNoParametersAreUsedInStatement for HHVM <= 3.13.1 and PHP <= 7.0.7 since known bug"
            );
        }

        $dbh = $this->getPdo();
        $params = [":bar" => 1];
        $sql = "SELECT 1";
        $stmt = $dbh->prepare($sql);
        $this->setExpectedException('Exception');
        $result = $stmt->execute($params);
        $this->assertTrue(
            $result,
            'If $stmt->execute() did not throw an exception, the statement should have been executed'
        );
        $rows = $stmt->fetchAll();

    }

}
