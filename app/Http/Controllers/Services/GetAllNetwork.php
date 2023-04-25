<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202302\ApiException;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;

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
        ServiceFactory $serviceFactory,
        AdManagerSession $session
    ) {

        $networkService = $serviceFactory->createNetworkService($session);
//        Get All Network
        $networks = $networkService->getAllNetworks();
        if (empty($networks)) {
            printf('No accessible networks found.' . PHP_EOL);
            return;
        }

        foreach ($networks as $i => $network) {
            printf(
                "%d) Network with code %d and display name '%s' was found.%s",
                $i,
                $network->getNetworkCode(),
                $network->getDisplayName(),
                PHP_EOL
            );
        }
        printf("Number of results found: %d%s", count($networks), PHP_EOL);


    }

    public static function main()
    {
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
