<?php

namespace App\Http\Controllers\Services;

require __DIR__ . '/../../../../vendor/autoload.php';

use Carbon\Carbon;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202302\Order;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;

class CreateOrders
{

// Set the advertiser (company), salesperson, and trafficker to assign to
    // each order.
    const ADVERTISER_ID = '5303157522';
    const SALESPERSON_ID = '248963779';
    const TRAFFICKER_ID = '248963779';

    public static function runExample(
        ServiceFactory   $serviceFactory,
        AdManagerSession $session,
        int              $advertiserId,
        int              $salespersonId,
        int              $traffickerId
    )
    {
        $orderService = $serviceFactory->createOrderService($session);
        $order = new Order();
        $order->setName('Order #' . uniqid());
        $order->setAdvertiserId($advertiserId);
        $order->setSalespersonId($salespersonId);
        $order->setTraffickerId($traffickerId);
        $order->setLastModifiedDateTime(Carbon::now());


        // Create the order on the server.
        $results = $orderService->createOrders([$order]);

        // Print out some information for each created order.
        foreach ($results as $i => $order) {
            printf(
                "%d) Order with ID %d and name '%s' was created.%s",
                $i,
                $order->getId(),
                $order->getName(),
                PHP_EOL
            );
        }
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

        self::runExample(
            new ServiceFactory(),
            $session,
            intval(self::ADVERTISER_ID),
            intval(self::SALESPERSON_ID),
            intval(self::TRAFFICKER_ID)
        );
    }

}
