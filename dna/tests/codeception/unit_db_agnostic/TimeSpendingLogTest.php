<?php

class TimeSpendingLogTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public static function testStartsWithOptionallySuffixedTokenMethodDataProvider()
    {
        $testDataMatrix = [];
        $tlp = new TimeLogParser();
        $tokens = $tlp->tokens();
        $keyword = "pause";
        foreach ($tokens[$keyword] as $token) {
            $tokenSpecific = [
                [$token . '->2010-04-03 7:58', $keyword, '->', $token],
                [$token . '->2010-04-03 7:58', $keyword, null, $token],
                [$token . ' 2016-05-16 09:55->', $keyword, '->', false],
                [$token . ' 2016-05-16 09:55->', $keyword, ' ', $token],
                [$token . ' 2016-05-16 09:55->', $keyword, null, $token],
                [$token . '->|$', $keyword, '->|$', $token],
            ];
            $testDataMatrix = array_merge($testDataMatrix, $tokenSpecific);
        }
        return $testDataMatrix;
    }

    /**
     * @group coverage:full
     * @dataProvider testStartsWithOptionallySuffixedTokenMethodDataProvider
     */
    public function testStartsWithOptionallySuffixedTokenMethod($haystack, $keyword, $suffix, $expectedReturnValue)
    {
        $tlp = new TimeLogParser();
        $return = $tlp->startsWithOptionallySuffixedToken($haystack, $keyword, $suffix);
        $this->assertEquals(
            $expectedReturnValue,
            $return,
            'TimeLogParser->startsWithOptionallySuffixedToken() behaves as expected'
        );
    }

    public static function testSecondsToDurationProvider()
    {
        return [
            [60, "1min"],
            [65, "1min"],
            [120, "2min"],
            [99 * 60, "1h39min"],
            [200 * 60, "3h20min"],
            [184 * 60, "3h4min"],
            [70 * 60, "1h10min"],
            [4397 * 60, "3d1h17min"],
            [4397 * 60 + 3600 * 24 * 7 * 5, "5w3d1h17min"],
            //[13, "13s"],
            [13, "0min"],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider testSecondsToDurationProvider
     */
    public function testSecondsToDuration($seconds, $expectedReturnValue)
    {
        $tlp = new TimeLogParser();
        $return = $tlp->secondsToDuration($seconds);
        $this->assertEquals(
            $expectedReturnValue,
            $return,
            'TimeLogParser->secondsToDuration() behaves as expected'
        );
    }

    public static function testDurationToMinutesProvider()
    {
        return [
            ["4min", "4"],
            ["99min", "99"],
            ["200min", "200"],
            ["3h4min", 184],
            ["1h10min", 70],
            ["3d1h17min", 4397],
            ["5w3d1h17min", 4397 + 3600 * 24 * 7 * 5 / 60],
            ["13s", 13 / 60],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider testDurationToMinutesProvider
     */
    public function testDurationToMinutes($duration, $expectedReturnValue)
    {
        $tlp = new TimeLogParser();
        $return = $tlp->durationToMinutes($duration);
        $this->assertEquals(
            $expectedReturnValue,
            $return,
            'TimeLogParser->durationToMinutes() behaves as expected'
        );
    }

    public static function testAddZeroFilledDatesProvider()
    {
        return [
            [
                [
                    "2013-03-28" => "foo",
                    "2013-04-02" => "foo",
                ],
                [
                    "2013-03-28" => "foo",
                    "2013-03-29" => 0,
                    "2013-03-30" => 0,
                    "2013-03-31" => 0,
                    "2013-04-01" => 0,
                    "2013-04-02" => "foo",
                ]
            ],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider testAddZeroFilledDatesProvider
     */
    public function testAddZeroFilledDates($times, $expectedReturnValue)
    {
        $tlp = new TimeLogParser();
        $return = $tlp->addZeroFilledDates($times);
        $this->assertEquals(
            $expectedReturnValue,
            $return,
            'TimeLogParser->addZeroFilledDates() behaves as expected'
        );
    }

    public static function testDetectTimeStampAndSetTsAndDateDataProvider()
    {
        return [
            ['foo 2016-05-25T14:50:00Z bar', '2016-05-25T14:50:00Z', '14:50:00', DateTime::ISO8601, 'Europe/Stockholm', '2016-05-01', true, '2016-05-25 14:50:00'],
            ['foo 2016-05-25T14:50:00+03:00 bar', '2016-05-25T14:50:00+03:00', '14:50:00', DateTime::ISO8601, 'Europe/Stockholm', '2016-05-01', true, '2016-05-25 11:50:00'],
            ['foo 2016-05-25T14:50:00+UTC bar', '2016-05-25T14:50:00+UTC', '14:50:00', DateTime::ISO8601, 'Europe/Stockholm', '2016-05-01', true, '2016-05-25 14:50:00'],
            ['foo 2016-05-25 14:50 bar', '2016-05-25 14:50', '14:50', 'Y-m-d H:i', 'UTC', '2016-05-01', true, '2016-05-25 14:50:00'],
            ['foo 2016-05-25 14:50 bar', '2016-05-25 14:50', '14:50', 'Y-m-d H:i', 'Europe/Stockholm', '2016-05-01', true, '2016-05-25 12:50:00'],
            ['foo 2016-05-25 14:50 bar', '2016-05-25 14:50', '14:50', 'Y-m-d H:i', 'Europe/Helsinki', '2016-05-01', true, '2016-05-25 11:50:00'],
            ['foo 2014-09-01 14:50 bar', '2014-09-01 14:50', '14:50', 'Y-m-d H:i', 'UTC', '2016-05-01', true, '2014-09-01 14:50:00'],
            ['foo 2014-09-01 14:50 bar', '2014-09-01 14:50', '14:50', 'Y-m-d H:i', 'Europe/Stockholm', '2016-05-01', true, '2014-09-01 12:50:00'],
            ['foo 2014-09-01 14:50 bar', '2014-09-01 14:50', '14:50', 'Y-m-d H:i', 'Europe/Helsinki', '2016-05-01', true, '2014-09-01 11:50:00'],
            ['foo 2014-11-21 14:50 bar', '2014-11-21 14:50', '14:50', 'Y-m-d H:i', 'UTC', '2016-05-01', true, '2014-11-21 14:50:00'],
            ['foo 2014-11-21 14:50 bar', '2014-11-21 14:50', '14:50', 'Y-m-d H:i', 'Europe/Stockholm', '2016-05-01', true, '2014-11-21 13:50:00'],
            ['foo 2014-11-21 14:50 bar', '2014-11-21 14:50', '14:50', 'Y-m-d H:i', 'Europe/Helsinki', '2016-05-01', true, '2014-11-21 12:50:00'],
            ['foo 2014-09-01T12:42:21+02:00 bar', '2014-09-01T12:42:21+02:00', '12:42:21', DateTime::ISO8601, 'Europe/Stockholm', '2016-05-01', true, '2014-09-01 10:42:21'],
            ['foo 2014-09-01 12:42 bar', '2014-09-01 12:42', '12:42', 'Y-m-d H:i', 'Europe/Stockholm', '2016-05-01', true, '2014-09-01 10:42:00'],
            ['foo 14:35 bar', '14:35', '14:35', 'H:i', 'Europe/Stockholm', '2016-05-01', true, '2016-05-01 12:35:00'],
            ['foo 14.35 bar', '14.35', '14.35', 'H.i', 'Europe/Stockholm', '2016-05-01', true, '2016-05-01 12:35:00'],
            ['foo bar', false, false, false, 'Europe/Stockholm', '2016-05-01', false, false],
            ['paus 18:45ca->', '18:45ca', '18:45ca', 'H:i', 'Europe/Stockholm', '2016-05-01', true, '2016-05-01 16:45:00'],
            ['paus 2016-05-25 18:45ca->', '2016-05-25 18:45ca', '18:45ca', 'Y-m-d H:i', 'Europe/Stockholm', '2016-05-01', true, '2016-05-25 16:45:00'],
            ['paus ??->', false, false, false, 'Europe/Stockholm', '2016-05-01', false, false],
            ['start 2014-11-21 17:49', '2014-11-21 17:49', '17:49', 'Y-m-d H:i', 'Europe/Stockholm', '2014-11-21', true, '2014-11-21 16:49:00'],
            [' 2014-11-21T15:51:00+UTC, <just before paus>', '2014-11-21T15:51:00+UTC', '15:51:00', DateTime::ISO8601, 'Europe/Stockholm', '2014-11-21', true, '2014-11-21 15:51:00'],
        ];
    }

    /**
     * @group coverage:full
     * @dataProvider testDetectTimeStampAndSetTsAndDateDataProvider
     */
    public function testDetectTimeStampAndSetTsAndDate(
        $linefordatecheck,
        $expectedMetadataDateRaw,
        $expectedMetadataTimeRaw,
        $expectedMetadataDateRawFormat,
        $lastKnownTimeZone,
        $lastKnownDate,
        $expectedToBeValid,
        $expectedUtcDateString
    ) {
        $tlp = new TimeLogParser();
        $metadata = [];
        $tlp->lastKnownDate = $lastKnownDate;
        $tlp->lastKnownTimeZone = $lastKnownTimeZone;
        $tlp->detectTimeStamp($linefordatecheck, $metadata);
        codecept_debug(compact("metadata"));
        $this->assertEquals(
            $expectedMetadataDateRaw,
            $metadata['date_raw'],
            'TimeLogParser->detectTimeStamp() detects date_raw as expected'
        );
        $this->assertEquals(
            $expectedMetadataTimeRaw,
            $metadata['time_raw'],
            'TimeLogParser->detectTimeStamp() detects time_raw as expected'
        );
        $this->assertEquals(
            $expectedMetadataDateRawFormat,
            $metadata['date_raw_format'],
            'TimeLogParser->detectTimeStamp() detects the datetime with the expected format'
        );

        $ts = null;
        $date = null;
        /** @var DateTime $datetime */
        $datetime = null;
        $tlp->set_ts_and_date($metadata["date_raw"], $ts, $date, null, $datetime);
        codecept_debug(compact("ts", "date", "datetime"));
        $valid = !empty($date);

        $this->assertEquals(
            $expectedToBeValid,
            $valid,
            'TimeLogParser->set_ts_and_date() detects valid datetimes as expected'
        );
        $this->assertEquals(
            $lastKnownTimeZone,
            $tlp->lastKnownTimeZone,
            'TimeLogParser->set_ts_and_date() does not change the last known timezone by parsing a timestamp string'
        );

        if ($expectedToBeValid) {

            $datetime->setTimezone(new DateTimeZone('UTC'));
            $this->assertEquals(
                $expectedUtcDateString,
                $datetime->format('Y-m-d H:i:s'),
                'TimeLogParser->set_ts_and_date() behaves as expected'
            );

        }

    }

    public static function correctTimeSpendingLogContentsProvider()
    {
        $timeSpendingLogPaths = glob(codecept_data_dir('time-spending-logs/correct') . '/*.tslog');
        $providerData = [];
        foreach ($timeSpendingLogPaths as $timeSpendingLogPath) {
            $correspondingCsvDataFilePath = str_replace(".tslog", ".csv", $timeSpendingLogPath);
            $providerData[] = [$timeSpendingLogPath, $correspondingCsvDataFilePath];
        }
        return $providerData;
    }

    protected function processedTimeSpendingLog($timeSpendingLogPath)
    {

        $timeSpendingLogContents = file_get_contents($timeSpendingLogPath);
        if (is_file($timeSpendingLogPath . ".tzFirst")) {
            $tzFirst = trim(file_get_contents($timeSpendingLogPath . ".tzFirst"));
        } else {
            $tzFirst = 'UTC';
        }

        $timeSpendingLog = new TimeSpendingLog();
        $timeSpendingLog->rawLogContents = $timeSpendingLogContents;
        $timeSpendingLog->tzFirst = $tzFirst;
        $processedTimeSpendingLog = new ProcessedTimeSpendingLog($timeSpendingLog);

        return $processedTimeSpendingLog;

    }

    /**
     * @group coverage:full
     * @dataProvider correctTimeSpendingLogContentsProvider
     */
    public function testProcessCorrectTimeSpendingLogs($timeSpendingLogPath, $correspondingCsvDataFilePath)
    {

        codecept_debug(__LINE__ . " - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MiB");

        $thrownException = null;
        $processedTimeSpendingLog = null;
        try {
            codecept_debug(__LINE__ . " - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MiB");

            $processedTimeSpendingLog = $this->processedTimeSpendingLog($timeSpendingLogPath);

            codecept_debug(__LINE__ . " - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MiB");

            //codecept_debug($processedTimeSpendingLog->timeReportCsv);

            // To make it easier to update with correct contents for the first time
            file_put_contents(
                $correspondingCsvDataFilePath . ".latest-run.csv",
                $processedTimeSpendingLog->timeReportCsv
            );

            $timeLogEntriesWithMetadata = $processedTimeSpendingLog->getTimeLogEntriesWithMetadata();

            codecept_debug(__LINE__ . " - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MiB");

            //codecept_debug($timeLogEntriesWithMetadata);
            codecept_debug(count($timeLogEntriesWithMetadata) . " time log entries");

            // All tested time logs should include at least 1 time log entry
            $this->assertGreaterThan(0, count($timeLogEntriesWithMetadata));

            // Save processedLogContentsWithTimeMarkers_debug in order to make debugging easier
            file_put_contents(
                $timeSpendingLogPath . ".latest-run.timeLogEntriesWithMetadata.json",
                json_encode($timeLogEntriesWithMetadata)
            );

        } catch (TimeSpendingLogProcessingErrorsEncounteredException $e) {

            $thrownException = $e;
            $processedTimeSpendingLog = $e->processedTimeSpendingLog;

            $errorsJson = json_encode($e->processedTimeSpendingLog->getProcessingErrors());

            // To make it easier to update with correct contents for the first time
            file_put_contents(
                $timeSpendingLogPath . ".latest-run.processing-errors.json",
                $errorsJson
            );

        }

        // Save preProcessedContents in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.preProcessedContents.txt",
            $processedTimeSpendingLog->preProcessedContents
        );

        // Save processedLogContentsWithTimeMarkers in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.processedLogContentsWithTimeMarkers.txt",
            $processedTimeSpendingLog->processedLogContentsWithTimeMarkers
        );

        // Save processedLogContentsWithTimeMarkers_debug in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.processedLogContentsWithTimeMarkers_debug.json",
            $processedTimeSpendingLog->processedLogContentsWithTimeMarkers_debug
        );

        $this->assertNotInstanceOf("TimeSpendingLogProcessingErrorsEncounteredException", $thrownException);

        codecept_debug(__LINE__ . " - Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MiB");

    }

    /**
     * @group coverage:full
     * @dataProvider correctTimeSpendingLogContentsProvider
     */
    public function testCorrectTimeSpendingLogsCorrectness($timeSpendingLogPath, $correspondingCsvDataFilePath)
    {

        $correspondingCsvDataFileContents = file_get_contents($correspondingCsvDataFilePath);
        $processedTimeSpendingLog = $this->processedTimeSpendingLog($timeSpendingLogPath);
        $this->assertEquals($correspondingCsvDataFileContents, $processedTimeSpendingLog->timeReportCsv);

    }

    public static function incorrectTimeSpendingLogContentsProvider()
    {
        $timeSpendingLogPaths = glob(codecept_data_dir('time-spending-logs/incorrect') . '/*.tslog');
        $providerData = [];
        foreach ($timeSpendingLogPaths as $timeSpendingLogPath) {
            $processingErrorsJsonFilePath = str_replace(".tslog", ".processing-errors.json", $timeSpendingLogPath);
            $providerData[] = [$timeSpendingLogPath, $processingErrorsJsonFilePath];
        }
        return $providerData;
    }

    /**
     * @group coverage:full
     * @dataProvider incorrectTimeSpendingLogContentsProvider
     */
    public function testCorrectlyReportedProcessingErrorSourcelines($timeSpendingLogPath, $processingErrorsJsonFilePath)
    {

        $thrownException = null;
        $processedTimeSpendingLog = null;
        try {
            $processedTimeSpendingLog = $this->processedTimeSpendingLog($timeSpendingLogPath);
        } catch (TimeSpendingLogProcessingErrorsEncounteredException $e) {

            $thrownException = $e;
            $processedTimeSpendingLog = $e->processedTimeSpendingLog;

            codecept_debug($e->processedTimeSpendingLog->getTroubleshootingInfo());
            //codecept_debug($e->processedTimeSpendingLog->getTimeLogParser()->preProcessedContentsSourceLineContentsSourceLineMap);

            $errorsJson = json_encode($e->processedTimeSpendingLog->getProcessingErrors());

            // To make it easier to update with correct contents for the first time
            file_put_contents(
                $timeSpendingLogPath . ".latest-run.processing-errors.json",
                $errorsJson
            );

        }

        // Save timeReportCsv in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.timeReportCsv.csv",
            $processedTimeSpendingLog->timeReportCsv
        );

        // Save preProcessedContents in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.preProcessedContents.txt",
            $processedTimeSpendingLog->preProcessedContents
        );

        // Save processedLogContentsWithTimeMarkers in order to make debugging easier
        file_put_contents(
            $timeSpendingLogPath . ".latest-run.processedLogContentsWithTimeMarkers.txt",
            $processedTimeSpendingLog->processedLogContentsWithTimeMarkers
        );

        if (!empty($thrownException)) {
            $processingErrorsJsonFileContents = file_get_contents($processingErrorsJsonFilePath);
            $this->assertEquals($processingErrorsJsonFileContents, $errorsJson);
        }

        $this->assertInstanceOf("TimeSpendingLogProcessingErrorsEncounteredException", $thrownException);

    }

}