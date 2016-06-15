<?php

namespace Webvaloa\Helpers;

use Libvaloa\Debug;
use stdClass;
use RuntimeException;
use UnexpectedValueException;

class Smartpost
{
    private $zip;
    private $smartpostUrl;

    public function __construct($zip)
    {
        $this->zip = $zip;

        $this->smartpostUrl = 'https://ohjelmat.posti.fi/pup/v1/';
    }

    public static function validateZipNumber($zip)
    {
        if (strlen($zip) != 5 || !is_numeric($zip)) {
            throw new UnexpectedValueException('Not a valid ZIP code.');
        }

        Debug::__print('Zip is valid');

        return true;
    }

    public function getLongitudeLatitude()
    {
        try {
            self::validateZipNumber($this->zip);
        } catch (UnexpectedValueException $e) {
            Debug::__print('Zip is not valid');

            throw new UnexpectedValueException($e->getMessage());
        }

        $targetUrl = $this->smartpostUrl.'pickuppoints?zipcode='.$this->zip;
        @$response = file_get_contents($targetUrl);

        Debug::__print($this->zip);
        Debug::__print('Smartpost api response');
        Debug::__print($response);
        Debug::__print($targetUrl);

        if ($response === false) {
            throw new RuntimeException('Could not read the Smartpost API.');
        }

        if (!$json = json_decode($response)) {
            throw new RuntimeException('Could not parse JSON response.');
        }

        Debug::__print($json[0]);

        $resp = new stdClass();
        $resp->longitude = 0;
        $resp->latitude = 0;

        if (isset($json[0]->MapLongitude)) {
            $resp->longitude = $json[0]->MapLongitude;
        }

        if (isset($json[0]->MapLatitude)) {
            $resp->latitude = $json[0]->MapLatitude;
        }

        return $resp;
    }

    public function getNearestLocations()
    {
        try {
            $lat = $this->getLongitudeLatitude();
        } catch (UnexpectedValueException $e) {
            Debug::__print($e->getMessage());

            return false;
        } catch (RuntimeException $e) {
            Debug::__print($e->getMessage());

            return false;
        }

        Debug::__print($lat);

        $targetUrl = $this->smartpostUrl.'pickuppoints?type=smartpost&longitude='.$lat->longitude.'&latitude='.$lat->latitude.'&top=8';
        @$response = file_get_contents($targetUrl);

        Debug::__print($targetUrl);

        if ($response === false) {
            throw new IOException('Could not read the Smartpost API.');
        }

        if (!$json = json_decode($response)) {
            throw new IOException('Could not parse JSON response.');
        }

        Debug::__print($json);

        return $json;        
    }
}
