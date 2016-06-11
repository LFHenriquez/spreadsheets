<?php
namespace Spreadsheets;
use \Google_Client;
use Cake\Core\Configure;

class Spreadsheets
{
    protected $client;
    protected $service;
    protected $spreadsheetsValues;
	protected $spreadsheetId;

    public function __construct($client, $spreadsheetId)
    {
        $this->client = $client;
        $this->service = new Google_Service_Sheets($client);
        $this->$spreadsheetsValues = $service->spreadsheets_values;
        $this->spreadsheetId = $spreadsheetId;
    }

    public function getUnique($sheet, $item, $itemValue, $column = null)
	{
        $indices = $this->header($sheet);
        $range = $sheet."!A2:".$this->getNameFromNumber(count($indices));
        $response = $spreadsheetsValues->get($spreadsheetId, $range);
        $values = $response->getValues();
        foreach ($values as $value)
        {
            if ($value[$indices[$item]] == $itemValue)
                if ($column != null)
                    return $value[$column];
                else
                    return $value;
        }
        return false;
    }

    public function setUnique($sheet, $item, $itemValue, $column, $newValue)
    {
        $indices = $this->header($sheet);
        $range = $sheet."!A2:".$this->getNameFromNumber(count($indices));
        $response = $spreadsheetsValues->get($spreadsheetId, $range);
        $values = $response->getValues();
        $valueIndex = 1;
        foreach ($values as $value) {
            $valueIndex++;
            if ($value[$indices[$item]] == $itemValue)
            {
                $valueRange = new Google_Service_Sheets_ValueRange();
                $valueRange->setMajorDimension('ROWS');
                $cell = $this->getNameFromNumber($indices[$column]).$valueIndex;
                $range = $sheet."!".$cell.":".$cell;
                $valueRange->setRange($range);
                $valueRange->setValues([[$newValue]]);
                $spreadsheetsValues->update($spreadsheetId, $range, $valueRange, ['valueInputOption' => 'USER_ENTERED']);
            }
        }
    }

    private function header($sheet)
    {
        $range = $sheet."!1:1";
        $response = $spreadsheetsValues->get($spreadsheetId, $range);
        $row = $response->getValues();
        foreach ($row[0] as $index => $title) {
            $indices[$title] = $index;
        }
        return $indices;
    }

    private function getNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
        var_dump($this);
    }
}