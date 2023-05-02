<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\CreateAdvertiser;
use App\Http\Controllers\Services\CreateCreatives;
use App\Http\Controllers\Services\CreateLineItems;
use App\Http\Controllers\Services\GetAllCreatives;
use App\Http\Controllers\Services\GetAllLineItems;
use App\Http\Controllers\Services\GetCurrentNetwork;
use App\Http\Controllers\Services\GetAllNetwork;
use App\Http\Controllers\Services\CreateOrders;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v202302\StatementBuilder;
use Google\AdsApi\AdManager\v202302\AdUnit;
use Google\AdsApi\AdManager\v202302\AdUnitTargetWindow;
use Google\AdsApi\AdManager\v202302\CreativePlaceholder;
use Google\AdsApi\AdManager\v202302\InventoryService;
use Google\AdsApi\AdManager\v202302\MobileApplication;
use Google\AdsApi\AdManager\v202302\MobileApplicationService;
use Google\AdsApi\AdManager\v202302\Placement;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\AdManager\v202302\AdUnitSize;
use Google\AdsApi\AdManager\v202302\Size;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\v202302\ApproveOrders as ApproveOrdersAction;


use Illuminate\Http\Request;


require __DIR__ . '/../../../../vendor/autoload.php';

class GoogleAdsApiController extends Controller
{

    public function AuthConnection()
    {
//        ->fromFile('C:/Users/01/adsapi_php.ini')
        $oauth2Credential = (new OAuth2TokenBuilder())
            ->withJsonKeyFilePath('C:/Users/01/ad-manager-json.json')
            ->withScopes('https://www.googleapis.com/auth/dfp')
            ->build();


        $session = (new AdManagerSessionBuilder())
            ->withNetworkCode('22869856784')
            ->withApplicationName('HkAppsManagement')
            ->withOAuth2Credential($oauth2Credential)->build();

        return $session;

    }

    public function GetCurrentNetwork()
    {

        $session = $this->AuthConnection();

        $serviceFactory = new ServiceFactory();
        $networkService = $serviceFactory->createNetworkService($session);

        // Get the current network.
        $network = $networkService->getCurrentNetwork();

        $networks = array();

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

        return $networks;

    }

    public function GetAllNetwork()
    {

        $session = $this->AuthConnection();

        $serviceFactory = new ServiceFactory();
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

    public function CreateAdvertiser()
    {
        CreateAdvertiser::main();
    }

    public function CreateOrders()
    {
        CreateOrders::main();
    }

    public function CreateLineItems()
    {

        CreateLineItems::main();
    }

    public function getLineItem()
    {
        GetAllLineItems::main();
    }

    public function GetAllCreatives()
    {
        GetAllCreatives::main();
    }

    public function CreateCreatives()
    {
        CreateCreatives::main();
    }

    public function CreatePlacements()
    {
        $session = $this->AuthConnection();

        $serviceFactory = new ServiceFactory();
        $networkService = $serviceFactory->createPlacementService($session);

        // Create a placement.
        $placement = new Placement();
        $placement->setName('INSERT_PLACEMENT_NAME_HERE');
        $placement->setDescription('INSERT_PLACEMENT_DESCRIPTION_HERE');
        $placement->setPlacementCode('INSERT_PLACEMENT_CODE');
        $placement->setStatus('INSERT_STATUS');
        $placement->setTargetedAdUnitIds(['INSERT_AD_UNIT_ID_HERE']);


        $size = new Size();
        $size->setWidth(300);
        $size->setHeight(250);

        $size->setIsAspectRatio();

// Create an AdUnitSize object for the placement's ad unit size.
        $adUnitSize = new AdUnitSize();
        $adUnitSize->setSize($size);

// Set the isAspectRatio property to false.
        $adUnitSize->getSize()->setIsAspectRatio(false);

// Add the ad unit size to the placement's ad unit sizes.
        $placement->setAdUnitSize([$adUnitSize]);


        dd('stop');
        // Create the placement on the server.
        $createdPlacement = $networkService->createPlacements($placement);


    }

    public function ApproveOrder()
    {

        $orderId = "3193475473";

        $session = $this->AuthConnection();

        $serviceFactory = new ServiceFactory();

        $orderService = $serviceFactory->createOrderService($session);

        // Create a statement to select the orders to approve.
        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->where('id = :id')
            ->orderBy('id ASC')
            ->limit($pageSize)
            ->withBindVariableValue('id', $orderId);

        // Retrieve a small amount of orders at a time, paging through until all
        // orders have been retrieved.
        $totalResultSetSize = 0;
        do {
            $page = $orderService->getOrdersByStatement(
                $statementBuilder->toStatement()
            );

            // Print out some information for the orders to be approved.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();
                foreach ($page->getResults() as $order) {
                    printf(
                        "%d) Order with ID %d, name '%s',"
                        . " and advertiser ID %d will be approved.%s",
                        $i++,
                        $order->getId(),
                        $order->getName(),
                        $order->getAdvertiserId(),
                        PHP_EOL
                    );
                }
            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);

        printf(
            "Total number of orders to be approved: %d%s",
            $totalResultSetSize,
            PHP_EOL
        );

        if ($totalResultSetSize > 0) {
            // Remove limit and offset from statement so we can reuse the
            // statement.
            $statementBuilder->removeLimitAndOffset();

            // Create and perform action.
            $action = new ApproveOrdersAction();
            $result = $orderService->performOrderAction(
                $action,
                $statementBuilder->toStatement()
            );

            if ($result !== null && $result->getNumChanges() > 0) {
                printf(
                    "Number of orders approved: %d%s",
                    $result->getNumChanges(),
                    PHP_EOL
                );
            } else {
                printf("No orders were approved.%s", PHP_EOL);
            }
        }
    }

    public function CreateMobileApplication()
    {

        $session = $this->AuthConnection();
        $serviceFactory = new ServiceFactory();
        $adManagerServices = new AdManagerServices();

        $applications = [
            [
                'displayName' => 'Application 1',
                'appStoreId' => 'com.dis.epfbalance.uan',
                'appStores' => ['GOOGLE_PLAY'],
            ]
        ];

        // Create each application in the array.
        foreach ($applications as $app) {
            // Create the new application object.
            $application = new MobileApplication();
            $application->setDisplayName($app['displayName']);
            $application->setAppStoreId($app['appStoreId']);
            $application->setAppStores($app['appStores']);
//
//            // Create the application service and use it to create the new application.
            $applicationService = $adManagerServices->get($session, MobileApplicationService::class);
            $application = $applicationService->createMobileApplications([$application]);

            // Print the ID of the new application.

            $getApplications = array();

            $id = $application->getId();
            $applicationId = $application->getApplicationId();
            $displayName = $application->getDisplayName();
            $appStoreId = $application->getAppStoreId();
            $appStores = $application->getAppStores();
            $isArchived = $application->getIsArchived();
            $appStoreName = $application->getAppStoreName();
            $applicationCode = $application->getApplicationCode();
            $developerName = $application->getDeveloperName();
            $platform = $application->getPlatform();
            $isFree = $application->getIsFree();
            $downloadUrl = $application->getDownloadUrl();

            $object = (object)array('id' => $id, 'applicationId' => $applicationId, 'displayName' => $displayName, 'appStoreId' => $appStoreId, 'appStores' => $appStores, 'isArchived' => $isArchived, 'appStoreName' => $appStoreName, 'applicationCode' => $applicationCode, 'developerName' => $developerName, 'platform' => $platform, 'isFree' => $isFree, 'downloadUrl' => $downloadUrl);
            array_push($getApplications, $object);

            return $getApplications;
        }


    }

    public function getAllApplication()
    {

        $session = $this->AuthConnection();
        $adManagerServices = new AdManagerServices();

        $mobileApplicationService = $adManagerServices->get($session, MobileApplicationService::class);

        $pageSize = 500;

        $statementBuilder = (new StatementBuilder())->orderBy('id ASC')->limit($pageSize);

        do {
            $page = $mobileApplicationService->getMobileApplicationsByStatement(
                $statementBuilder->toStatement()
            );
            $applications = array();
            if ($page->getResults() !== null) {
                foreach ($page->getResults() as $mobileApplication) {

                    $id = $mobileApplication->getId();
                    $applicationId = $mobileApplication->getApplicationId();
                    $displayName = $mobileApplication->getDisplayName();
                    $appStoreId = $mobileApplication->getAppStoreId();
                    $appStores = $mobileApplication->getAppStores();
                    $isArchived = $mobileApplication->getIsArchived();
                    $appStoreName = $mobileApplication->getAppStoreName();
                    $applicationCode = $mobileApplication->getApplicationCode();
                    $developerName = $mobileApplication->getDeveloperName();
                    $platform = $mobileApplication->getPlatform();
                    $isFree = $mobileApplication->getIsFree();
                    $downloadUrl = $mobileApplication->getDownloadUrl();


                    $object = (object)array('id' => $id, 'applicationId' => $applicationId, 'displayName' => $displayName, 'appStoreId' => $appStoreId, 'appStores' => $appStores, 'isArchived' => $isArchived, 'appStoreName' => $appStoreName, 'applicationCode' => $applicationCode, 'developerName' => $developerName, 'platform' => $platform, 'isFree' => $isFree, 'downloadUrl' => $downloadUrl);
                    array_push($applications, $object);
                }
                return $applications;
            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $page->getTotalResultSetSize());
    }

    public function createAdUnit()
    {
        // Set the size of the ad unit.
        $adUnitSize = new Size();
        $adUnitSize->setWidth(300);
        $adUnitSize->setHeight(250);

// Create an AdUnit object.
        $adUnit = new AdUnit();
        $adUnit->setName('insert name');
        $adUnit->setParentId(' parent id');
        $adUnit->setTargetWindow(AdUnitTargetWindow::BLANK);
        $adUnit->setAdUnitSizes([$adUnitSize]);

// Set up the Google Ad Manager API client.
        $session = $this->AuthConnection();
        $adManagerServices = new AdManagerServices();

// Create an InventoryService instance.
        $inventoryService = $adManagerServices->get($session, InventoryService::class);

// Create the ad unit on the server.
        $result = $inventoryService->createAdUnits([$adUnit]);

// Print the ID of the created ad unit.
        printf("Ad unit with ID %d was created.\n", $result[0]->getId());
    }


}
