<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202302\Company;
use Google\AdsApi\AdManager\v202302\CompanyType;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\AdManagerServices;


/**
 * This example gets the current network.
 *
 * It is meant to be run from a command line (not as a webpage) and requires
 * that you've setup an `adsapi_php.ini` file in your home directory with your
 * API credentials and settings. See README.md for more info.
 */
class CreateAdvertiser
{

    public static function runExample(
        ServiceFactory   $serviceFactory,
        AdManagerSession $session
    )
    {
        $companyService = $serviceFactory->createCompanyService($session);

        $company = new Company();
        $company->setName('Advertiser #' . uniqid());
        $company->setType(CompanyType::ADVERTISER);

        // Create the company on the server.
        $results = $companyService->createCompanies([$company]);

        // Print out some information for each created company.
        foreach ($results as $i => $company) {
            printf(
                "%d) Company with ID %d and name '%s' was created.%s",
                $i,
                $company->getId(),
                $company->getName(),
                PHP_EOL
            );
        }
    }

    public static function main(
        ServiceFactory   $serviceFactory,
        AdManagerSession $session
    )
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
