<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';

use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v202302\StatementBuilder;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;

class GetAllCreatives
{

    public static function runExample(
        ServiceFactory $serviceFactory,
        AdManagerSession $session
    ) {
        $creativeService = $serviceFactory->createCreativeService($session);

        // Create a statement to select creatives.
        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->orderBy('id ASC')
            ->limit($pageSize);

        // Retrieve a small amount of creatives at a time, paging
        // through until all creatives have been retrieved.
        $totalResultSetSize = 0;
        do {
            $page = $creativeService->getCreativesByStatement(
                $statementBuilder->toStatement()
            );

            dd($page);
            // Print out some information for each creative.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();
                foreach ($page->getResults() as $creative) {
                    printf(
                        "%d) Creative with ID %d and name '%s' was found.%s",
                        $i++,
                        $creative->getId(),
                        $creative->getName(),
                        PHP_EOL
                    );
                }
            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);

        printf("Number of results found: %d%s", $totalResultSetSize, PHP_EOL);
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
