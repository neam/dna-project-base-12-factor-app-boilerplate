<?php

class PropelQueryTest extends \PHPUnit_Framework_TestCase
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
    public function testClerkLedgerEntryStemmingFromClerkTransactionRowId()
    {

        $query = \propel\models\ClerkLedgerEntryQuery::create();

        $expected = "SELECT  FROM";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        $query->setPrimaryTableName(\propel\models\Map\ClerkLedgerEntryTableMap::TABLE_NAME);

        $expected = "SELECT  FROM `clerk_ledger_entry`";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        $query->filterByStemmingFromClerkTransactionRowId(5);

        $expected = "SELECT  FROM `clerk_ledger_entry` LEFT JOIN `clerk_transaction_row` `clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id` ON (`clerk_ledger_entry`.`id`=`clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id`.`receiving_party_clerk_ledger_entry_id`) LEFT JOIN `clerk_transaction_row` `clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id` ON (`clerk_ledger_entry`.`id`=`clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id`.`sending_party_clerk_ledger_entry_id`) WHERE (`clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id`.`id`='5' OR `clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id`.`id`='5')";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

    }

}
