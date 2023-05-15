<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\GoogleAdManager;
use Carbon\Carbon;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v202302\StatementBuilder;
use Google\AdsApi\AdManager\v202302\AdUnit;
use Google\AdsApi\AdManager\v202302\AdUnitTargeting;
use Google\AdsApi\AdManager\v202302\AdUnitTargetWindow;
use Google\AdsApi\AdManager\v202302\Company;
use Google\AdsApi\AdManager\v202302\CompanyType;
use Google\AdsApi\AdManager\v202302\CostType;
use Google\AdsApi\AdManager\v202302\CreativePlaceholder;
use Google\AdsApi\AdManager\v202302\CreativeRotationType;
use Google\AdsApi\AdManager\v202302\Goal;
use Google\AdsApi\AdManager\v202302\GoalType;
use Google\AdsApi\AdManager\v202302\InventoryService;
use Google\AdsApi\AdManager\v202302\InventoryTargeting;
use Google\AdsApi\AdManager\v202302\LineItem;
use Google\AdsApi\AdManager\v202302\LineItemType;
use Google\AdsApi\AdManager\v202302\MobileApplication;
use Google\AdsApi\AdManager\v202302\MobileApplicationService;
use Google\AdsApi\AdManager\v202302\Money;
use Google\AdsApi\AdManager\v202302\Order;
use Google\AdsApi\AdManager\v202302\Placement;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\AdManager\v202302\AdUnitSize;
use Google\AdsApi\AdManager\v202302\Size;
use Google\AdsApi\AdManager\v202302\StartDateTimeType;
use Google\AdsApi\AdManager\v202302\Targeting;
use Google\AdsApi\AdManager\v202302\UnitType;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\AdManager\v202302\ApproveOrders as ApproveOrdersAction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


require __DIR__ . '/../../../../vendor/autoload.php';

class GoogleAdsApiController extends Controller
{

    public function AuthConnection($id)
    {
        $googleAdManager = GoogleAdManager::find($id);

        $jsonFilePath = "C:/xampp/htdocs/hkapps_management/public/storage/" . $googleAdManager->jsonFilePath;
        if ($googleAdManager->currentNetworkCode) {
            $networkCode = $googleAdManager->currentNetworkCode;
        } else {
            $networkCode = '22869856784';
        }

        $oauth2Credential = (new OAuth2TokenBuilder())
            ->withJsonKeyFilePath($jsonFilePath)
            ->withScopes('https://www.googleapis.com/auth/dfp')
            ->build();


        $session = (new AdManagerSessionBuilder())
            ->withNetworkCode($networkCode)
            ->withApplicationName('HkAppsManagement')
            ->withOAuth2Credential($oauth2Credential)->build();

        return $session;

    }

    public function GetAllNetwork($id)
    {

        $session = $this->AuthConnection($id);

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

    public function GetAllPlacements($id)
    {

        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

        $placementService = $serviceFactory->createPlacementService($session);


        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->orderBy('id ASC');


        $totalResultSetSize = 0;
        do {
            $page = $placementService->getPlacementsByStatement(
                $statementBuilder->toStatement()
            );

            // Print out some information for each placement.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();
                $placements = array();
                foreach ($page->getResults() as $placement) {
                    $id = $placement->getId();
                    $name = $placement->getName();
                    $description = $placement->getDescription();
                    $placementCode = $placement->getPlacementCode();
                    $status = $placement->getStatus();
                    $targetedAdUnitIds = $placement->getTargetedAdUnitIds();
                    $lastModifiedDateTime = $placement->getLastModifiedDateTime();

                    $object = (object)array('id' => $id, 'name' => $name, 'description' => $description, 'placementCode' => $placementCode, 'status' => $status, 'targetedAdUnitIds' => $targetedAdUnitIds, 'lastModifiedDateTime' => $lastModifiedDateTime);
                    array_push($placements, $object);

                }
                return $placements;
            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);

        printf("Number of results found: %d%s", $totalResultSetSize, PHP_EOL);
    }

    public function GetAllUsers($id)
    {

        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

        $userService = $serviceFactory->createUserService($session);

        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->orderBy('id ASC');

        $totalResultSetSize = 0;
        do {
            $page = $userService->getUsersByStatement(
                $statementBuilder->toStatement()
            );

            // Print out some information for each user.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();

                $users = array();
                foreach ($page->getResults() as $user) {
                    $id = $user->getId();
                    $name = $user->getName();
                    $email = $user->getEmail();
                    $roleId = $user->getRoleId();
                    $roleName = $user->getRoleName();
                    $isActive = $user->getIsActive();
                    $isEmailNotificationAllowed = $user->getIsEmailNotificationAllowed();
                    $externalId = $user->getExternalId();
                    $isServiceAccount = $user->getIsServiceAccount();
                    $ordersUiLocalTimeZoneId = $user->getOrdersUiLocalTimeZoneId();

                    $object = (object)array('id' => $id, 'name' => $name, 'email' => $email, 'roleId' => $roleId, 'roleName' => $roleName, 'isActive' => $isActive, 'isEmailNotificationAllowed' => $isEmailNotificationAllowed, 'externalId' => $externalId, 'isServiceAccount' => $isServiceAccount, 'ordersUiLocalTimeZoneId' => $ordersUiLocalTimeZoneId);
                    array_push($users, $object);
                }

                $filteredArray = Arr::where($users, function ($value, $key) {
                    return $value->roleName == 'Trafficker';
                });

                return array_values($filteredArray);

            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);

        printf("Number of results found: %d%s", $totalResultSetSize, PHP_EOL);

    }

    public function selectNetwork(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'currentNetworkCode' => 'required',
            'web_property_code' => 'required',
            'placementId' => 'required',
            'trafficker_id' => 'required',
        ]);

        $id = $request->id;
        $networkCode = $request->currentNetworkCode;
        $web_property_code = $request->web_property_code;
        $placementId = $request->placementId;
        $trafficker_id = $request->trafficker_id;

        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(422);
        } else {
            $googleAdManager = GoogleAdManager::find(1);
            $googleAdManager->currentNetworkCode = $networkCode;
            $googleAdManager->web_property_code = $web_property_code;
            $googleAdManager->placementId = $placementId;
            $googleAdManager->trafficker_id = $trafficker_id;
            $googleAdManager->save();
        }

        // current network //
        $currentNetwork = $this->GetCurrentNetwork($id);

        // save name sql //
        $googleAdManager->name = $currentNetwork[0]->displayName;
        $googleAdManager->save();

        // create advertise //
        $advertiser = $this->CreateAdvertiser($id);

        // save advertise_id sql //
        $googleAdManager->advertise_id = $advertiser[0]->id;
        $googleAdManager->save();

        // create order //
        $order = $this->CreateOrders($advertiser[0]->id, $id, $trafficker_id);

        // save order_id sql //
        $googleAdManager->order_id = $order[0]->id;
        $googleAdManager->save();

        // create line item //
        $lineItem = $this->CreateLineItems($order[0]->id, $id, $placementId, $web_property_code);

        // save lineItemId sql //
        $googleAdManager->lineItemId = $lineItem[0]->id;
        $googleAdManager->save();

        return response()->json(['message' => 'Advertise , Order , LineItems created Successfully!']);
    }

    public function GetCurrentNetwork($id)
    {

        $session = $this->AuthConnection($id);

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

    public function CreateAdvertiser($id)
    {
        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

        $companyService = $serviceFactory->createCompanyService($session);

        $company = new Company();
        $company->setName('Hk Advertiser #' . uniqid());
        $company->setType(CompanyType::ADVERTISER);


        // Create the company on the server.
        $results = $companyService->createCompanies([$company]);

        $advertise = array();

        // Print out some information for each created company.
        foreach ($results as $i => $company) {
            $id = $company->getId();
            $name = $company->getName();
            $type = $company->getType();
            $address = $company->getAddress();
            $email = $company->getEmail();
            $faxPhone = $company->getFaxPhone();
            $primaryPhone = $company->getPrimaryPhone();
            $externalId = $company->getExternalId();
            $comment = $company->getComment();
            $creditStatus = $company->getCreditStatus();
            $settings = $company->getSettings();
            $appliedLabels = $company->getAppliedLabels();
            $primaryContactId = $company->getPrimaryContactId();
            $appliedTeamIds = $company->getAppliedLabels();
            $thirdPartyCompanyId = $company->getThirdPartyCompanyId();
            $lastModifiedDateTime = $company->getLastModifiedDateTime();
            $childPublisher = $company->getChildPublisher();
            $viewabilityProvider = $company->getViewabilityProvider();

            $object = (object)array('id' => $id, 'name' => $name, 'type' => $type, 'address' => $address, 'email' => $email, 'faxPhone' => $faxPhone, 'primaryPhone' => $primaryPhone, 'externalId' => $externalId, 'comment' => $comment, 'creditStatus' => $creditStatus, 'settings' => $settings, 'appliedLabels' => $appliedLabels, 'primaryContactId' => $primaryContactId, 'appliedTeamIds' => $appliedTeamIds, 'thirdPartyCompanyId' => $thirdPartyCompanyId, 'lastModifiedDateTime' => $lastModifiedDateTime, 'childPublisher' => $childPublisher, 'viewabilityProvider' => $viewabilityProvider);
            array_push($advertise, $object);

        }

        return $advertise;

    }

    public function CreateOrders($advertiserId, $id, $trafficker_id)
    {
        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

//        $advertiserId = '5303157522';
        $salespersonId = $trafficker_id;
//        $traffickerId = '248963779';

        $orderService = $serviceFactory->createOrderService($session);
        $order = new Order();
        $order->setName('hk Order #' . uniqid());
        $order->setAdvertiserId($advertiserId);
        $order->setSalespersonId($salespersonId);
        $order->setTraffickerId($trafficker_id);
        $order->setLastModifiedDateTime(Carbon::now());

        // Create the order on the server.
        $results = $orderService->createOrders([$order]);

        $orders = array();

        foreach ($results as $i => $order) {

            $id = $order->getId();
            $name = $order->getName();
            $startDateTime = $order->getStartDateTime();
            $endDateTime = $order->getEndDateTime();
            $unlimitedEndDateTime = $order->getUnlimitedEndDateTime();
            $status = $order->getStatus();
            $isArchived = $order->getIsArchived();
            $notes = $order->getNotes();
            $externalOrderId = $order->getExternalOrderId();
            $poNumber = $order->getPoNumber();
            $currencyCode = $order->getCurrencyCode();
            $advertiserId = $order->getAdvertiserId();
            $advertiserContactIds = $order->getAdvertiserContactIds();
            $agencyId = $order->getAgencyId();
            $agencyContactIds = $order->getAgencyContactIds();
            $creatorId = $order->getCreatorId();
            $traffickerId = $order->getTraffickerId();
            $secondaryTraffickerIds = $order->getSecondaryTraffickerIds();
            $salespersonId = $order->getSalespersonId();
            $secondarySalespersonIds = $order->getSecondarySalespersonIds();
            $totalImpressionsDelivered = $order->getTotalImpressionsDelivered();
            $totalClicksDelivered = $order->getTotalClicksDelivered();
            $totalViewableImpressionsDelivered = $order->getTotalViewableImpressionsDelivered();
            $totalBudget = $order->getTotalBudget();
            $appliedLabels = $order->getAppliedLabels();
            $effectiveAppliedLabels = $order->getEffectiveAppliedLabels();
            $lastModifiedByApp = $order->getLastModifiedByApp();
            $isProgrammatic = $order->getIsProgrammatic();
            $appliedTeamIds = $order->getAppliedLabels();
            $lastModifiedDateTime = $order->getLastModifiedDateTime();

            $object = (object)array('id' => $id, 'name' => $name, 'startDateTime' => $startDateTime, 'endDateTime' => $endDateTime, 'unlimitedEndDateTime' => $unlimitedEndDateTime, 'status' => $status, 'isArchived' => $isArchived, 'notes' => $notes, 'externalOrderId' => $externalOrderId, 'poNumber' => $poNumber, 'currencyCode' => $currencyCode, 'advertiserId' => $advertiserId, 'advertiserContactIds' => $advertiserContactIds, 'agencyId' => $agencyId, 'agencyContactIds' => $agencyContactIds, 'creatorId' => $creatorId, 'traffickerId' => $traffickerId, 'secondaryTraffickerIds' => $secondaryTraffickerIds, 'salespersonId' => $salespersonId, 'secondarySalespersonIds' => $secondarySalespersonIds, 'totalImpressionsDelivered' => $totalImpressionsDelivered, 'totalClicksDelivered' => $totalClicksDelivered, 'totalViewableImpressionsDelivered' => $totalViewableImpressionsDelivered, 'totalBudget' => $totalBudget, 'appliedLabels' => $appliedLabels, 'effectiveAppliedLabels' => $effectiveAppliedLabels, 'lastModifiedByApp' => $lastModifiedByApp, 'isProgrammatic' => $isProgrammatic, 'appliedTeamIds' => $appliedTeamIds, 'lastModifiedDateTime' => $lastModifiedDateTime);
            array_push($orders, $object);
        }

        return $orders;
    }

    public function CreateLineItems($orderId, $id, $placementId, $web_property_code)
    {
        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

        $lineItemService = $serviceFactory->createLineItemService($session);

        // Create inventory targeting.
        $inventoryTargeting = new InventoryTargeting();
        $inventoryTargeting->setTargetedPlacementIds([$placementId]);

        // Create targeting.
        $targeting = new Targeting();
        $targeting->setInventoryTargeting($inventoryTargeting);

        // Now setup the line item.
        $lineItem = new LineItem();
        $lineItem->setName('hk Line item #' . uniqid());
        $lineItem->setOrderId($orderId);
        $lineItem->setTargeting($targeting);
        $lineItem->setLineItemType(LineItemType::AD_EXCHANGE);
        $lineItem->setAllowOverbook(true);


        // Define the creative placeholders for the ad unit sizes.
        $creativePlaceholders = array();
        $creativePlaceholder1 = new CreativePlaceholder();
        $creativePlaceholder1->setSize(new Size(300, 250)); // 300x250 ad unit
        $creativePlaceholders[] = $creativePlaceholder1;
        $creativePlaceholder2 = new CreativePlaceholder();
        $creativePlaceholder2->setSize(new Size(320, 100)); // 320x100 ad unit
        $creativePlaceholders[] = $creativePlaceholder2;
        $creativePlaceholder3 = new CreativePlaceholder();
        $creativePlaceholder3->setSize(new Size(300, 50)); // 320x100 ad unit
        $creativePlaceholders[] = $creativePlaceholder3;

// Set the creative placeholders for the line item.
        $lineItem->setCreativePlaceholders($creativePlaceholders);


        // Set the size of creatives that can be associated with this line item.
//        $lineItem->setCreativePlaceholders([$creativePlaceholder]);

        // Set the creative rotation type to even.
        $lineItem->setCreativeRotationType(CreativeRotationType::EVEN);

        // Set the length of the line item to run.
        $lineItem->setStartDateTimeType(StartDateTimeType::IMMEDIATELY);
        $lineItem->setUnlimitedEndDateTime(true);


        // Set the cost per unit to $2.
        $lineItem->setCostType(CostType::CPM);
        $lineItem->setCostPerUnit(new Money('USD', 0));

        // Set the number of units bought to 500,000 so that the budget is
        // $1,000.
        $goal = new Goal();
        $goal->setUnits(-1);
        $goal->setUnitType(UnitType::IMPRESSIONS);
        $goal->setGoalType(GoalType::NONE);
        $lineItem->setPrimaryGoal($goal);
        $lineItem->setWebPropertyCode($web_property_code);

        // Create the line items on the server.
        $results = $lineItemService->createLineItems([$lineItem]);

        // Print out some information for each created line item.
        $lineItems = array();

        foreach ($results as $i => $lineItem) {

            $id = $lineItem->getId();
            $name = $lineItem->getName();

            $object = (object)array('id' => $id, 'name' => $name);
            array_push($lineItems, $object);

        }

        return $lineItems;

    }

    public function createMobileAppAdUnit($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
            'package_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(422);
        } else {
            $package_name = $request->package_name;

            $getAllApps = $this->getAllApplication($id);

            $appStoreId = [];
            foreach ($getAllApps as $getApp) {
                $storeId = $getApp->appStoreId;
                array_push($appStoreId, $storeId);
            }

            if (!in_array($package_name, $appStoreId)) {

                $this->CreateMobileApplication($id, $package_name);
                $this->createAdUnit($id);
                return response()->json(['message' => 'Mobile Application and AdUnits are created Successfully!'], 422);

            } else {
                return response()->json(['message' => 'This application already create.'], 422);
            }
        }


    }

    public function getAllApplication($id)
    {

        $session = $this->AuthConnection($id);
        $adManagerServices = new AdManagerServices();

        $mobileApplicationService = $adManagerServices->get($session, MobileApplicationService::class);

//        $pageSize = 500;

        $statementBuilder = (new StatementBuilder())->orderBy('id DESC');

        do {
            $page = $mobileApplicationService->getMobileApplicationsByStatement(
                $statementBuilder->toStatement()
            );
            dd($page);
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

//            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $page->getTotalResultSetSize());
    }

    public function CreateMobileApplication($id, $package_name)
    {

        $session = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();
        $adManagerServices = new AdManagerServices();
        $applications = [
            [
                'displayName' => 'HK Application '.uniqid(),
                'appStoreId' => $package_name,
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

            // Create the application service and use it to create the new application.
            $applicationService = $adManagerServices->get($session, MobileApplicationService::class);
            $application = $applicationService->createMobileApplications([$application]);

        }
        return 'application created!';

    }

    public function createAdUnit($id)
    {

        // Set up the Google Ad Manager API client.
        $adManagerSession = $this->AuthConnection($id);

        // set up the Ad Manager services and inventory service
        $adManagerServices = new AdManagerServices();
        $inventoryService = $adManagerServices->get($adManagerSession, InventoryService::class);


        $adUnit = new AdUnit();
        $adUnit->setName('My Ad Unit '.uniqid());
        $adUnit->setParentId(22907596760); // ID of the parent ad unit (optional)
        $adUnit->setTargetWindow(AdUnitTargetWindow::BLANK); // open links in a new window
        $adUnitSizes = [];
        $adUnitSize = new AdUnitSize();
        $adUnitSize->setSize(['width' => 300, 'height' => 250]);
        $adUnitSize->setEnvironmentType('BROWSER');
        $adUnitSizes[] = $adUnitSize;
        $adUnit->setAdUnitSizes($adUnitSizes);

// create the ad unit in Ad Manager
        $adUnits = $inventoryService->createAdUnits([$adUnit]);

// print the ID of the new ad unit
//        echo 'Ad unit created with ID: ' . $adUnits[0]->getId();

        return 'AdUnits created!';

    }

 //////////////////////////////////////////////////////////

    public function CreatePlacements($id)
    {
        $session = $this->AuthConnection($id);

        // set up the Ad Manager services and inventory service
        $adManagerServices = new AdManagerServices();
        $inventoryService = $adManagerServices->get($session, InventoryService::class);

        // Create a Size object
        $size = new Size();
        $size->setWidth(300);
        $size->setHeight(250);

// Create a placement object
        $placement = new Placement();
        $placement->setName('My Placement '.uniqid());
        $placement->setDescription('This is my placement.');
        $placement->setTargetedAdUnitIds(['AD_UNIT_ID']);
        $placement->set([$size]);

// Create the placement on the server
        $placement = $inventoryService->createPlacement($placement);


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

}
