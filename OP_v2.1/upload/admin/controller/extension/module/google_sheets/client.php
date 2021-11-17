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

        $this->clientLoginUrl = $this->url->link('extension/module/google_sheets/client/getClientLogin', 'token=' . $this->session->data['token'], 'SSL');

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

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                return null;
            }
        }
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

        $client = new Google_Client();
        $client->setApplicationName('Google Sheets API PHP');

        $client->setClientId($this->config->get('google_sheets_setting_ClientId'));
        $client->setClientSecret($this->config->get('google_sheets_setting_ClientSecret'));

        $client->setRedirectUri($this->clientLoginUrl);
        $client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');

        $client->setScopes([Google_Service_Sheets::DRIVE, Google_Service_Sheets::DRIVE_FILE, Google_Service_Sheets::SPREADSHEETS_READONLY]);


        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');


        $tokenPath = $this->tokenPath;
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);

            // Save the token to a file.
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }


    function getClientLogin()
    {
        error_reporting(0);

        $this->load->language('extension/module/google_sheets/setting');
        $client = $this->client;
        $tokenPath = $this->tokenPath;

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();

                echo '<a target="_blank" href="' . $authUrl . '">' . $this->language->get('login_auth_text') . '</a>';

                echo '<form action="' . $this->clientLoginUrl . '" method="post">';
                echo '<input name="code">';
                echo '<button>Send</button>';
                echo '</form>';

                $authCode = @$_REQUEST['code'];

                if ($authCode) {

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
//                    throw new Exception(join(', ', $accessToken));
                    }

                    // Save the token to a file.
                    if (!file_exists(dirname($tokenPath))) {
                        mkdir(dirname($tokenPath), 0700, true);
                    }
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));

                    echo '<script>window.close();</script>';

                } else {

                }
            }
        }
        return $client;
    }


}
