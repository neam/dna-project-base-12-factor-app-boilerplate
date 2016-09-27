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

        $expected = "SELECT * FROM";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        $query->setPrimaryTableName(\propel\models\Map\ClerkLedgerEntryTableMap::TABLE_NAME);

        $expected = "SELECT * FROM `clerk_ledger_entry`";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        $query->filterByStemmingFromClerkTransactionRowId(5);

        $expected = "SELECT * FROM `clerk_ledger_entry` LEFT JOIN `clerk_transaction_row` `clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id` ON (`clerk_ledger_entry`.`id`=`clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id`.`receiving_party_clerk_ledger_entry_id`) LEFT JOIN `clerk_transaction_row` `clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id` ON (`clerk_ledger_entry`.`id`=`clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id`.`sending_party_clerk_ledger_entry_id`) WHERE (`clerk_transaction_row_related_by_receiving_party_clerk_ledger_entry_id`.`id`='5' OR `clerk_transaction_row_related_by_sending_party_clerk_ledger_entry_id`.`id`='5')";
        $actual = AppUtil::propelQuerySQL($query);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

    }

    /**
     * @group data:test-clean-db,coverage:full
     */
    public function testCombineCloneAndWhereForClerkTransactionRows()
    {

        // find ctrs within the date boundaries of the current fiscal year
        $transactionRowsCommonQuery = \propel\models\ClerkTransactionRowQuery::create();

        // named conditions for use below
        $transactionRowsCommonQuery
            ->condition(
                'do_not_ignore_receiving_party',
                'ClerkTransactionRow.IgnoreReceivingPartySinceAlreadyHandledBySymmetricCtr = 0 OR ClerkTransactionRow.IgnoreReceivingPartySinceAlreadyHandledBySymmetricCtr IS NULL'
            )
            ->condition(
                'do_not_ignore_sending_party',
                'ClerkTransactionRow.IgnoreSendingPartySinceAlreadyHandledBySymmetricCtr = 0 OR ClerkTransactionRow.IgnoreSendingPartySinceAlreadyHandledBySymmetricCtr IS NULL'
            );

        $expected = "SELECT * FROM";
        $actual = AppUtil::propelQuerySQL($transactionRowsCommonQuery);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        // where the receiving tax entity is current fiscal year's tax entity
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingParty = clone $transactionRowsCommonQuery;
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingParty
            ->condition(
                'sending_unknown_or_not_internal_transaction',
                '(ClerkTransactionRow.sending_party_clerk_tax_entity_id IS NULL OR ClerkTransactionRow.receiving_party_clerk_tax_entity_id <> ClerkTransactionRow.sending_party_clerk_tax_entity_id)'
            )
            ->where(['do_not_ignore_receiving_party', 'sending_unknown_or_not_internal_transaction'], 'and');

        $expected = "SELECT * FROM `clerk_transaction_row` WHERE (`clerk_transaction_row`.`ignore_receiving_party_since_already_handled_by_symmetric_ctr` = 0 OR `clerk_transaction_row`.`ignore_receiving_party_since_already_handled_by_symmetric_ctr` IS NULL AND (`clerk_transaction_row`.`sending_party_clerk_tax_entity_id` IS NULL OR `clerk_transaction_row`.`receiving_party_clerk_tax_entity_id` <> `clerk_transaction_row`.`sending_party_clerk_tax_entity_id`))";
        $actual = AppUtil::propelQuerySQL($unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingParty);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        // where the sending tax entity is current fiscal year's tax entity
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneySendingParty = clone $transactionRowsCommonQuery;
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneySendingParty
            ->condition(
                'receiving_unknown_or_not_internal_transaction',
                '(ClerkTransactionRow.receiving_party_clerk_tax_entity_id IS NULL OR ClerkTransactionRow.receiving_party_clerk_tax_entity_id <> ClerkTransactionRow.sending_party_clerk_tax_entity_id)'
            )
            ->where(['do_not_ignore_sending_party', 'receiving_unknown_or_not_internal_transaction'], 'and');

        $expected = "SELECT * FROM `clerk_transaction_row` WHERE (`clerk_transaction_row`.`ignore_sending_party_since_already_handled_by_symmetric_ctr` = 0 OR `clerk_transaction_row`.`ignore_sending_party_since_already_handled_by_symmetric_ctr` IS NULL AND (`clerk_transaction_row`.`receiving_party_clerk_tax_entity_id` IS NULL OR `clerk_transaction_row`.`receiving_party_clerk_tax_entity_id` <> `clerk_transaction_row`.`sending_party_clerk_tax_entity_id`))";
        $actual = AppUtil::propelQuerySQL($unledgeredTransactionRowsWhereTaxEntityIsTheMoneySendingParty);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

        // where the sending and receiving tax entity is current fiscal year's tax entity
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingPartyAndMoneySendingParty = clone $transactionRowsCommonQuery;
        $unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingPartyAndMoneySendingParty
            ->combine(
                ['do_not_ignore_sending_party', 'do_not_ignore_receiving_party'],
                'and',
                'do_not_ignore_sending_nor_receiving_party'
            )
            ->condition(
                'internal_transaction',
                'ClerkTransactionRow.receiving_party_clerk_tax_entity_id = ClerkTransactionRow.sending_party_clerk_tax_entity_id'
            )
            ->where(['do_not_ignore_sending_nor_receiving_party', 'internal_transaction'], 'and');

        $expected = "SELECT * FROM `clerk_transaction_row` WHERE ((`clerk_transaction_row`.`ignore_sending_party_since_already_handled_by_symmetric_ctr` = 0 OR `clerk_transaction_row`.`ignore_sending_party_since_already_handled_by_symmetric_ctr` IS NULL AND `clerk_transaction_row`.`ignore_receiving_party_since_already_handled_by_symmetric_ctr` = 0 OR `clerk_transaction_row`.`ignore_receiving_party_since_already_handled_by_symmetric_ctr` IS NULL) AND `clerk_transaction_row`.`receiving_party_clerk_tax_entity_id` = `clerk_transaction_row`.`sending_party_clerk_tax_entity_id`)";
        $actual = AppUtil::propelQuerySQL($unledgeredTransactionRowsWhereTaxEntityIsTheMoneyReceivingPartyAndMoneySendingParty);
        codecept_debug($actual);
        $this->assertEquals($expected, $actual);

    }

}
