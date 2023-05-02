<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';


use DateTime;
use DateTimeZone;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v202302\AdManagerDateTimes;
use Google\AdsApi\AdManager\v202302\BrowserTargeting;
use Google\AdsApi\AdManager\v202302\CostType;
use Google\AdsApi\AdManager\v202302\CreativePlaceholder;
use Google\AdsApi\AdManager\v202302\CreativeRotationType;
use Google\AdsApi\AdManager\v202302\DayOfWeek;
use Google\AdsApi\AdManager\v202302\DayPart;
use Google\AdsApi\AdManager\v202302\DayPartTargeting;
use Google\AdsApi\AdManager\v202302\GeoTargeting;
use Google\AdsApi\AdManager\v202302\Goal;
use Google\AdsApi\AdManager\v202302\GoalType;
use Google\AdsApi\AdManager\v202302\InventoryTargeting;
use Google\AdsApi\AdManager\v202302\LineItem;
use Google\AdsApi\AdManager\v202302\LineItemType;
use Google\AdsApi\AdManager\v202302\Location;
use Google\AdsApi\AdManager\v202302\MinuteOfHour;
use Google\AdsApi\AdManager\v202302\Money;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\AdManager\v202302\Size;
use Google\AdsApi\AdManager\v202302\StartDateTimeType;
use Google\AdsApi\AdManager\v202302\Targeting;
use Google\AdsApi\AdManager\v202302\Technology;
use Google\AdsApi\AdManager\v202302\TechnologyTargeting;
use Google\AdsApi\AdManager\v202302\TimeOfDay;
use Google\AdsApi\AdManager\v202302\UnitType;
use Google\AdsApi\AdManager\v202302\UserDomainTargeting;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\Util\v202302\StatementBuilder;



/**
 * This example gets the current network.
 *
 * It is meant to be run from a command line (not as a webpage) and requires
 * that you've setup an `adsapi_php.ini` file in your home directory with your
 * API credentials and settings. See README.md for more info.
 */
class GetAllLineItems
{

    public static function runExample(
        ServiceFactory $serviceFactory,
        AdManagerSession $session
    ) {
        $lineItemService = $serviceFactory->createLineItemService($session);

        // Create a statement to select line items.
        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->orderBy('id ASC')
            ->limit($pageSize);

        // Retrieve a small amount of line items at a time, paging
        // through until all line items have been retrieved.
        $totalResultSetSize = 0;
        do {
            $page = $lineItemService->getLineItemsByStatement(
                $statementBuilder->toStatement()
            );

            dd($page);
            // Print out some information for each line item.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();
                foreach ($page->getResults() as $lineItem) {
                    printf(
                        "%d) Line item with ID %d and name '%s' was found.%s",
                        $i++,
                        $lineItem->getId(),
                        $lineItem->getName(),
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
