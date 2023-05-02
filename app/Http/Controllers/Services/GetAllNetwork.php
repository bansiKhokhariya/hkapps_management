<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202302\ApiException;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\Auth\CredentialsLoader;


/**
 * This example gets the current network.
 *
 * It is meant to be run from a command line (not as a webpage) and requires
 * that you've setup an `adsapi_php.ini` file in your home directory with your
 * API credentials and settings. See README.md for more info.
 */
class GetAllNetwork
{

    /**
     * Gets the current network.
     *
     * @param ServiceFactory $serviceFactory the factory class for creating a
     *     network service client
     * @param AdManagerSession $session the session containing configurations
     *     for creating a network service client
     * @throws ApiException if the request for getting all networks fails
     */
    public static function runExample(
        ServiceFactory   $serviceFactory,
        AdManagerSession $session
    )
    {

        $networkService = $serviceFactory->createNetworkService($session);

        //  Get All Network
        $getAllNetworks = $networkService->getAllNetworks();
        $networks = array();

        foreach ($getAllNetworks as $network) {
            $id = $network->getId();
            $displayName = $network->getDisplayName();
            $networkCode = $network->getNetworkCode();
            $propertyCode = $network->getPropertyCode();
            $timeZone = $network->getTimeZone();
            $currencyCode = $network->getCurrencyCode();
            $secondaryCurrencyCodes = $network->getSecondaryCurrencyCodes();
            $effectiveRootAdUnitId = $network->getEffectiveRootAdUnitId();
            $isTest = $network->getIsTest();
            $childPublishers = $network->getChildPublishers();

            $object = (object)array('id' => $id, 'displayName' => $displayName, 'networkCode' => $networkCode, 'propertyCode' => $propertyCode, 'timeZone' => $timeZone, 'currencyCode' => $currencyCode, 'secondaryCurrencyCodes' => $secondaryCurrencyCodes, 'effectiveRootAdUnitId' => $effectiveRootAdUnitId, 'isTest' => $isTest, 'childPublishers' => $childPublishers);
            array_push($networks, $object);
        }

        return $networks;


    }

    public static function main()
    {

        // Path to your credentials file.
//        $iniPath = 'C:/Users/01/adsapi_php.ini';
//        $jsonPath = 'C:/Users/01/ad-manager-json.json';


        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()
            ->build();

        // Construct an API session configured from an `adsapi_php.ini` file
        // and the OAuth2 credentials above.
        $session = (new AdManagerSessionBuilder())->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        self::runExample(new ServiceFactory(), $session);
    }
}

//GetCurrentNetwork::main();
