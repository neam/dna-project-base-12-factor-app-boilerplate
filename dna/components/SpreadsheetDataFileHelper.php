<?php

class SpreadsheetDataFileHelper
{

    static public $processingWarnings = [];
    static public $processingErrors = [];

    /**
     * @param $inputFileName
     * @param int $columnLimit
     * @return array
     * @throws Exception
     * @throws PHPExcel_Reader_Exception
     */
    static public function getSpreadsheetCellData(
        $inputFileName,
        \propel\models\InputContentType $inputContentType = null,
        $columnLimit = 50
    ) {

        try {

            /**  Tell PHPExcel that we want to use the Advanced Value Binder  **/
            PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());


            // TODO: Setting a locale may help reading some cell values properly
            $locale = 'en_us';
            $validLocale = PHPExcel_Settings::setLocale($locale);
            if (!$validLocale) {
                throw new Exception('Unable to set locale to ' . $locale . " - reverting to en_us");
            }

            // TODO: Activating a cache method allows us to read larger files without hitting memory limits
            /*
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
            */

            // Load the spreadsheet data differently based on the input content type (if specified)
            if ($inputContentType) {
                $inputContentTypeRef = $inputContentType->getRef();
            } else {
                $inputContentTypeRef = null;
            }
            /** @var PHPExcel_Reader_IReader $excelReader */
            $excelReader = null;
            switch ($inputContentTypeRef) {
                case "exported-transaction-file/skatteverket.se.skattekonto":
                    $excelReader = PHPExcel_IOFactory::createReader('CSV')
                        ->setDelimiter(';')
                        ->setEnclosure('"')
                        ->setSheetIndex(0)
                        ->setInputEncoding('ISO-8859-1');
                    break;
                case "exported-transaction-file/nordea.se.internetbanken-foretag.xls":
                    $excelReader = PHPExcel_IOFactory::createReader('CSV')
                        ->setDelimiter("\t")
                        ->setEnclosure('"')
                        ->setSheetIndex(0)
                        ->setInputEncoding('ISO-8859-1');
                    break;

                case "exported-transaction-file/banknorwegian.se":
                    $excelReader = new PHPExcel_Reader_Excel2007_XNamespace();
                    break;
                case "custom-kalkyl-fw-ink13-worksheet":
                default:
                    $excelReader = PHPExcel_IOFactory::createReaderForFile($inputFileName);
            }

            // Dummy check - if PHPExcel_Reader_Excel2007 can't list worksheet names from the file
            // it is likely that the file is generated a namespace which is not supported out-of-the-box by PHPExcel
            // https://github.com/PHPOffice/PHPExcel/issues/571
            if ($excelReader instanceof PHPExcel_Reader_Excel2007) {
                $worksheetNames = $excelReader->listWorksheetNames($inputFileName);
                if (empty($worksheetNames)) {
                    $excelReader = new PHPExcel_Reader_Excel2007_XNamespace();
                    $worksheetNames = $excelReader->listWorksheetNames($inputFileName);
                    if (empty($worksheetNames)) {
                        throw new Exception(
                            "PHPExcel_Reader_Excel2007 could not list the worksheet names in " . $inputFileName
                        );
                    }
                }
            }

            /** @var PHPExcel $objPHPExcel */
            $objPHPExcel = $excelReader->load($inputFileName);

            // Read raw file contents
            $cellData = [];
            foreach ($objPHPExcel->getSheetNames() as $sheetName) {

                $cellData[$sheetName] = [];
                $objWorksheet = $objPHPExcel->getSheetByName($sheetName);

                $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

                //var_dump(__LINE__, $sheetName, $highestRow, $highestColumnIndex, $objWorksheet->getActiveCell());die();

                // Only read at most $columnLimit columns from the file TODO: Auto-detect amount of columns based on assumed header row
                if ($highestColumnIndex > ($columnLimit - 1)) {
                    $highestColumnIndex = ($columnLimit - 1);
                }

                for ($row = 1; $row <= $highestRow; ++$row) {
                    $cellData[$sheetName][$row] = [];
                    for ($colIndex = 0; $colIndex <= $highestColumnIndex; ++$colIndex) {
                        $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                        $cellData[$sheetName][$row][$col] = [];
                        $objCell = $objWorksheet->getCellByColumnAndRow($colIndex, $row);

                        // Parse cell value
                        $cellDataType = $objCell->getDataType();
                        $cellData[$sheetName][$row][$col]["data-type"] = $cellDataType;
                        if ($cellDataType === "f") {
                            // Formula
                            $cellData[$sheetName][$row][$col]["formula"] = $objCell->getValue();
                            $cellData[$sheetName][$row][$col]["value"] = $objCell->getOldCalculatedValue();
                            try {
                                $cellData[$sheetName][$row][$col]["calculated-value"] = $objCell->getCalculatedValue();
                            } catch (PHPExcel_Calculation_Exception $e) {
                                $processingWarning = compact("sheetName", "row", "col");
                                $processingWarning["field"] = "calculated-value";
                                $processingWarning["exception"] = [
                                    "message" => $e->getMessage(),
                                    "file" => $e->getFile(),
                                    "line" => $e->getLine(),
                                ];
                                static::$processingWarnings[] = $processingWarning;
                                $cellData[$sheetName][$row][$col]["calculated-value.exception"] = $e->getMessage();
                            }

                        } else {
                            // Non-formula
                            $cellData[$sheetName][$row][$col]["value"] = $objCell->getValue();
                        }

                        try {
                            $cellData[$sheetName][$row][$col]["formatted-value"] = $objCell->getFormattedValue();
                        } catch (PHPExcel_Calculation_Exception $e) {
                            $processingWarning = compact("sheetName", "row", "col");
                            $processingWarning["field"] = "formatted-value";
                            $processingWarning["exception"] = [
                                "message" => $e->getMessage(),
                                "file" => $e->getFile(),
                                "line" => $e->getLine(),
                            ];
                            static::$processingWarnings[] = $processingWarning;
                            $cellData[$sheetName][$row][$col]["formatted-value.exception"] = $e->getMessage();
                            $cellData[$sheetName][$row][$col]["formatted-value.debug"] = static::testFormula($objCell);
                        }
                        $cellData[$sheetName][$row][$col]["formatted-date"] = PHPExcel_Style_NumberFormat::toFormattedString(
                            $objCell->getValue(),
                            'YYYY-MM-DD'
                        );

                        // Unless the cell data type was null we must always return a non-null value
                        if ($cellDataType !== "null" && $cellData[$sheetName][$row][$col]["value"] === null) {
                            throw new Exception(
                                "Unexpected NULL cell value interpreted at row $row, col $col: " . print_r(
                                    $cellData[$sheetName][$row][$col],
                                    true
                                )
                            );
                        }

                    }
                }

            }

        } catch (PHPExcel_Reader_Exception $e) {
            throw $e;
            //throw new Exception('Error loading file: ' . $e->getMessage());
        }

        return $cellData;

    }

    static public function extractFirstValidDateFromRowCellData($rowCellData)
    {

        // Find the first available date in the row, or skip the data row if no date can be found
        $date = null;
        foreach ($rowCellData as $col => $cellData) {
            try {
                $date = SpreadsheetDataFileHelper::extractValidExcelDateFromCellData($cellData);
                return $date;
            } catch (SpreadsheetDataFileHelperInvalidDateException $e) {
                continue;
            }
        }

    }

    static public function extractValidExcelDateFromCellData($cellData)
    {

        // Check if the digit value includes an excel date
        if (is_integer($cellData["value"]) || is_float($cellData["value"]) || ctype_digit($cellData["value"])) {

            // First calculate the date behind the excel date value
            $fromExcelValue = PHPExcel_Shared_Date::ExcelToPHP($cellData["value"]);
            $dateTimeFromExcelValue = new DateTime();
            $dateTimeFromExcelValue->setTimestamp($fromExcelValue);

            $dateTimeFormattedExcelDate = $cellData["formatted-date"];
            $formattedDateCandidate = $dateTimeFromExcelValue->format("Y-m-d");

            if ($dateTimeFormattedExcelDate !== $formattedDateCandidate) {
                throw new SpreadsheetDataFileHelperInvalidDateException(
                    "String '{$cellData["value"]}' was not parsed as a date. Mismatch between formatted dates ('$dateTimeFormattedExcelDate' and '$formattedDateCandidate')"
                );
            }

            return $dateTimeFromExcelValue;

        }

        // Check if we can parse the value as a date
        try {
            $dateTimeFromExcelValue = new DateTime($cellData["value"]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'DateTime::__construct') !== false) {
                throw new SpreadsheetDataFileHelperInvalidDateException(
                    "String '{$cellData["value"]}' was not parsed as a date: " . $e->getMessage()
                );
            } else {
                throw $e;
            }
        }

        if (!empty($dateTimeFromExcelValue)) {
            return $dateTimeFromExcelValue;
        }

        throw new SpreadsheetDataFileHelperInvalidDateException(
            "String '{$cellData["value"]}' was not parsed as a date"
        );

    }

    /**
     * http://stackoverflow.com/a/15131539/682317
     * @return string Detailed log of how the lexer and parser evaluate the formula
     */
    static public function testFormula(PHPExcel_Cell $objCell)
    {
        $debugMessage = "";

        $formulaValue = $objCell->getValue();
        $debugMessage .= 'Formula Value is ' . $formulaValue . PHP_EOL;
        $expectedValue = $objCell->getOldCalculatedValue();
        $debugMessage .= 'Expected Value is ' . ((!is_null($expectedValue)) ? $expectedValue : 'UNKNOWN') . PHP_EOL;

        PHPExcel_Calculation::getInstance()->writeDebugLog = true;
        $calculate = false;
        try {
            $tokens = PHPExcel_Calculation::getInstance()->parseFormula($formulaValue, $objCell);
            $debugMessage .= 'Parser Stack :-' . PHP_EOL;
            $debugMessage .= print_r($tokens, true);
            $debugMessage .= PHP_EOL;
            $calculate = true;
        } catch (Exception $e) {
            $debugMessage .= 'PARSER ERROR: ' . $e->getMessage() . PHP_EOL;
        }

        if ($calculate) {
            try {
                $cellValue = $objCell->getCalculatedValue();
                $debugMessage .= 'Calculated Value is ' . $cellValue . PHP_EOL;
                $debugMessage .= 'Evaluation Log:' . PHP_EOL;
                $debugLog = PHPExcel_Calculation::getInstance()->getDebugLog();
                $debugMessage .= print_r($debugLog, true);
                $debugMessage .= PHP_EOL;
            } catch (Exception $e) {
                $debugMessage .= 'CALCULATION ENGINE ERROR: ' . $e->getMessage() . PHP_EOL;

                $debugMessage .= 'Evaluation Log:' . PHP_EOL;
                $debugLog = PHPExcel_Calculation::getInstance()->getDebugLog();
                $debugMessage .= print_r($debugLog, true);
                $debugMessage .= PHP_EOL;
            }
        }

        return $debugMessage;
    }

}

class SpreadsheetDataFileHelperInvalidDateException extends Exception
{
}