<?php

class ControllerExtensionModuleGoogleSheetsClient extends Controller
{
    private $client;
    private $service;
    private $tokenPath = __DIR__ . '/credentials/token.json';
    private $clientLoginUrl = '';

    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once DIR_SYSTEM . '/library/google_sheets/vendor/autoload.php';

        $this->client = $this->initClientApi();
        if ($this->client) {
            $this->service = new Google_Service_Sheets($this->client);
        }

    }

    public function getSheets()
    {
        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');

        $sheets = [];
        foreach ($this->service->spreadsheets->get($spreadsheetId)->getSheets() as $s) {
            $sheets[] = $s['properties'];
        }
        return $sheets;
    }


    public function getRow($row = 1)
    {
        $range = "_PRODUCTS_!A{$row}:Z{$row}";
        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');
        $result = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        return ($result) ? $result->getValues()[0] : [];
    }

    public function getRows($row_from = 1, $row_to = 1)
    {
        $range = "_PRODUCTS_!A{$row_from}:AZ{$row_to}";

        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');
        $result = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        return $result->getValues();
    }

    public function getTotalRows()
    {
        $range = "_PRODUCTS_";

        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');
        $result = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        return $result->getValues() != null ? count($result->getValues()) : 0;
    }

    public function addRows($rows)
    {
        $range = "_PRODUCTS_";

        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');

        $body = new Google_Service_Sheets_ValueRange([
            'values' => $rows
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];

        $this->service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }

    public function clearSheets()
    {
        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');

        foreach ($this->getSheets() as $sheet) {

            $requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
            $requestBody->setRequests([
                'updateCells' => [
                    'range'  => ['sheetId' => $sheet['sheetId']],
                    'fields' => "userEnteredValue",
                ]
            ]);

            $this->service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);
        }
    }

    public function deleteSheets()
    {
        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');

        $first = true;
        foreach ($this->getSheets() as $sheet) {
            if ($first) continue;
            $first = false;

            $requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
            $requestBody->setRequests([
                'deleteSheet' => ['sheetId' => $sheet['sheetId']]
            ]);

            $this->service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);
        }
    }

    public function renameFirstSheet($title)
    {
        $spreadsheetId = $this->config->get('google_sheets_setting_sheet_id');

        foreach ($this->getSheets() as $sheet) {

            $requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
            $requestBody->setRequests([
                new Google_Service_Sheets_Request([
                    'updateSheetProperties' => [
                        'properties' => [
                            "sheetId" => $sheet['sheetId'],
                            'title'   => $title
                        ],
                        'fields'     => 'title'
                    ]
                ])
            ]);

            $this->service->spreadsheets->batchUpdate($spreadsheetId, $requestBody);

            break;
        }
    }


    /**
     * @return Google_Client
     */
    public function getClient()
    {

        return $this->client;
    }

    /**
     * @return Google_Service_Sheets
     */
    public function getService()
    {
        return $this->service;
    }


    private function initClientApi()
    {
        // configure the Google Client
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');

        if ( file_exists( $this->tokenPath ) ) {
            $client->setAuthConfig($this->tokenPath);
        }

        return $client;
    }



}
