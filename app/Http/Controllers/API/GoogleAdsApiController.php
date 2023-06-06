<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\GoogleAdManager;
use Carbon\Carbon;
use Google\AdsApi\AdManager\AdManagerServices;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\Util\v202302\StatementBuilder;
use Google\AdsApi\AdManager\v202302\AdUnit;
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
use Illuminate\Support\Facades\Validator;


require __DIR__ . '/../../../../vendor/autoload.php';

class GoogleAdsApiController extends Controller
{

    public function AuthConnection($id)
    {
        $googleAdManager = GoogleAdManager::find($id);

        $jsonFilePath = "/www/wwwroot/panel.goldadx.com/public/storage/" . $googleAdManager->jsonFilePath;
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

    public function saveNetwork(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'currentNetworkCode' => 'required'
        ]);

        $id = $request->id;
        $networkCode = $request->currentNetworkCode;

        if ($validator->fails()) {
            return response()->json($validator->errors())->setStatusCode(422);
        } else {
            $googleAdManager = GoogleAdManager::find($id);
            $googleAdManager->currentNetworkCode = $networkCode;
            $googleAdManager->save();
            return response()->json('network code saved!');
        }

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
            $googleAdManager = GoogleAdManager::find($id);
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

        $lineItemData = [
            'banner' => $lineItem->getData()->banner[0]->name,
            'inter' => $lineItem->getData()->inter[0]->name,
            'native' => $lineItem->getData()->native[0]->name,
            'openApp' => $lineItem->getData()->openApp[0]->name
        ];

        // save lineItemId sql //
        $googleAdManager->lineItemId = [$lineItemData];
        $googleAdManager->save();

        return response()->json(['message' => 'Advertise , Order , LineItems created Sucessfully!']);
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
        $company->setName('Hk Advertiser -' . uniqid());
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
        $order->setName('hk Order -' . uniqid());
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

        //  line item Banner //

        $inventoryTargeting = new InventoryTargeting();
        $inventoryTargeting->setTargetedPlacementIds([$placementId]);

        $targeting = new Targeting();
        $targeting->setInventoryTargeting($inventoryTargeting);

        $lineItem = new LineItem();
        $lineItem->setName($orderId . ' - Banner');
        $lineItem->setOrderId($orderId);
        $lineItem->setTargeting($targeting);
        $lineItem->setLineItemType(LineItemType::AD_EXCHANGE);
        $lineItem->setAllowOverbook(true);

        $creativePlaceholdersBanner = array();
        $creativePlaceholderBanner1 = new CreativePlaceholder();
        $creativePlaceholderBanner1->setSize(new Size(300, 50)); // 300x50 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner1;
        $creativePlaceholderBanner2 = new CreativePlaceholder();
        $creativePlaceholderBanner2->setSize(new Size(300, 100)); // 300x100 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner2;
        $creativePlaceholderBanner3 = new CreativePlaceholder();
        $creativePlaceholderBanner3->setSize(new Size(300, 250)); // 300x250 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner3;
        $creativePlaceholderBanner4 = new CreativePlaceholder();
        $creativePlaceholderBanner4->setSize(new Size(320, 50)); // 320x50 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner4;
        $creativePlaceholderBanner5 = new CreativePlaceholder();
        $creativePlaceholderBanner5->setSize(new Size(320, 100)); // 320x100 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner5;
        $creativePlaceholderBanner6 = new CreativePlaceholder();
        $creativePlaceholderBanner6->setSize(new Size(320, 250)); // 320x250 ad unit
        $creativePlaceholdersBanner[] = $creativePlaceholderBanner6;

        $lineItem->setCreativePlaceholders($creativePlaceholdersBanner);

        $lineItem->setCreativeRotationType(CreativeRotationType::EVEN);

        $lineItem->setStartDateTimeType(StartDateTimeType::IMMEDIATELY);
        $lineItem->setUnlimitedEndDateTime(true);

        $lineItem->setCostType(CostType::CPM);
        $lineItem->setCostPerUnit(new Money('USD', 0));

        $goal = new Goal();
        $goal->setUnits(-1);
        $goal->setUnitType(UnitType::IMPRESSIONS);
        $goal->setGoalType(GoalType::NONE);

        $lineItem->setPrimaryGoal($goal);
        $lineItem->setWebPropertyCode($web_property_code);

        $resultsBanner = $lineItemService->createLineItems([$lineItem]);

        //  ?????????????????

        // line item Inter //

        $inventoryTargeting1 = new InventoryTargeting();
        $inventoryTargeting1->setTargetedPlacementIds([$placementId]);

        $targeting1 = new Targeting();
        $targeting1->setInventoryTargeting($inventoryTargeting1);

        $lineItem1 = new LineItem();
        $lineItem1->setName($orderId . ' - Inter');
        $lineItem1->setOrderId($orderId);
        $lineItem1->setTargeting($targeting);
        $lineItem1->setLineItemType(LineItemType::AD_EXCHANGE);
        $lineItem1->setAllowOverbook(true);

        $creativePlaceholdersInter = array();
        $creativePlaceholderInter1 = new CreativePlaceholder();
        $creativePlaceholderInter1->setSize(new Size(320, 480)); // 320x480 ad unit
        $creativePlaceholdersInter[] = $creativePlaceholderInter1;
        $creativePlaceholderInter2 = new CreativePlaceholder();
        $creativePlaceholderInter2->setSize(new Size(480, 320)); // 480x320 ad unit
        $creativePlaceholdersInter[] = $creativePlaceholderInter2;
        $creativePlaceholderInter3 = new CreativePlaceholder();
        $creativePlaceholderInter3->setSize(new Size(768, 1024)); // 768x1024  ad unit
        $creativePlaceholdersInter[] = $creativePlaceholderInter3;
        $creativePlaceholderInter4 = new CreativePlaceholder();
        $creativePlaceholderInter4->setSize(new Size(1024, 768)); // 1024x768  ad unit
        $creativePlaceholdersInter[] = $creativePlaceholderInter4;

        $lineItem1->setCreativePlaceholders($creativePlaceholdersInter);

        $lineItem1->setCreativeRotationType(CreativeRotationType::EVEN);

        $lineItem1->setStartDateTimeType(StartDateTimeType::IMMEDIATELY);
        $lineItem1->setUnlimitedEndDateTime(true);

        $lineItem1->setCostType(CostType::CPM);
        $lineItem1->setCostPerUnit(new Money('USD', 0));

        $goal1 = new Goal();
        $goal1->setUnits(-1);
        $goal1->setUnitType(UnitType::IMPRESSIONS);
        $goal1->setGoalType(GoalType::NONE);
        $lineItem1->setPrimaryGoal($goal1);
        $lineItem1->setWebPropertyCode($web_property_code);

        $resultsInter = $lineItemService->createLineItems([$lineItem1]);

        //  ????????????????????

        //  line item Native //
        $inventoryTargeting2 = new InventoryTargeting();
        $inventoryTargeting2->setTargetedPlacementIds([$placementId]);

        $targeting2 = new Targeting();
        $targeting2->setInventoryTargeting($inventoryTargeting2);

        $lineItem2 = new LineItem();
        $lineItem2->setName($orderId . ' - Native');
        $lineItem2->setOrderId($orderId);
        $lineItem2->setTargeting($targeting);
        $lineItem2->setLineItemType(LineItemType::AD_EXCHANGE);
        $lineItem2->setAllowOverbook(true);

        $creativePlaceholdersNative = array();
        $creativePlaceholderNative1 = new CreativePlaceholder();
        $creativePlaceholderNative1->setSize(new Size(1, 1)); // 1x1 ad unit
        $creativePlaceholdersNative[] = $creativePlaceholderNative1;

        $lineItem2->setCreativePlaceholders($creativePlaceholdersNative);

        $lineItem2->setCreativeRotationType(CreativeRotationType::EVEN);

        $lineItem2->setStartDateTimeType(StartDateTimeType::IMMEDIATELY);
        $lineItem2->setUnlimitedEndDateTime(true);

        $lineItem2->setCostType(CostType::CPM);
        $lineItem2->setCostPerUnit(new Money('USD', 0));

        $goal2 = new Goal();
        $goal2->setUnits(-1);
        $goal2->setUnitType(UnitType::IMPRESSIONS);
        $goal2->setGoalType(GoalType::NONE);
        $lineItem2->setPrimaryGoal($goal2);
        $lineItem2->setWebPropertyCode($web_property_code);

        $resultsNative = $lineItemService->createLineItems([$lineItem2]);

        //  ????????????????

        // line item OpenApp //

        $inventoryTargeting3 = new InventoryTargeting();
        $inventoryTargeting3->setTargetedPlacementIds([$placementId]);

        $targeting3 = new Targeting();
        $targeting3->setInventoryTargeting($inventoryTargeting3);

        $lineItem3 = new LineItem();
        $lineItem3->setName($orderId . ' - OpenApp');
        $lineItem3->setOrderId($orderId);
        $lineItem3->setTargeting($targeting);
        $lineItem3->setLineItemType(LineItemType::AD_EXCHANGE);
        $lineItem3->setAllowOverbook(true);


        $creativePlaceholdersOpenApp = array();
        $creativePlaceholderOpenApp1 = new CreativePlaceholder();
        $creativePlaceholderOpenApp1->setSize(new Size(320, 480)); // 320x480 ad unit
        $creativePlaceholdersOpenApp[] = $creativePlaceholderOpenApp1;
        $creativePlaceholderOpenApp2 = new CreativePlaceholder();
        $creativePlaceholderOpenApp2->setSize(new Size(480, 320)); // 480x320 ad unit
        $creativePlaceholdersOpenApp[] = $creativePlaceholderOpenApp2;
        $creativePlaceholderOpenApp3 = new CreativePlaceholder();
        $creativePlaceholderOpenApp3->setSize(new Size(768, 1024)); // 768x1024 ad unit
        $creativePlaceholdersOpenApp[] = $creativePlaceholderOpenApp3;
        $creativePlaceholderOpenApp4 = new CreativePlaceholder();
        $creativePlaceholderOpenApp4->setSize(new Size(1024, 768)); // 1024x768 ad unit
        $creativePlaceholdersOpenApp[] = $creativePlaceholderOpenApp4;

        $lineItem3->setCreativePlaceholders($creativePlaceholdersOpenApp);

        $lineItem3->setCreativeRotationType(CreativeRotationType::EVEN);

        $lineItem3->setStartDateTimeType(StartDateTimeType::IMMEDIATELY);
        $lineItem3->setUnlimitedEndDateTime(true);

        $lineItem3->setCostType(CostType::CPM);
        $lineItem3->setCostPerUnit(new Money('USD', 0));

        $goal3 = new Goal();
        $goal3->setUnits(-1);
        $goal3->setUnitType(UnitType::IMPRESSIONS);
        $goal3->setGoalType(GoalType::NONE);
        $lineItem3->setPrimaryGoal($goal3);
        $lineItem3->setWebPropertyCode($web_property_code);

        // Create the line items on the server.
        $resultsOpenApp = $lineItemService->createLineItems([$lineItem3]);

        //  ???????????????

        // print line item banner //
        $lineItemsBanner = array();
        foreach ($resultsBanner as $i => $lineItemBanner) {
            $id = $lineItem->getId();
            $name = $lineItem->getName();
            $object = (object)array('id' => $id, 'name' => $name);
            array_push($lineItemsBanner, $object);
        }
        ////////////////

        // print line item inter //
        $lineItemsInter = array();
        foreach ($resultsInter as $i => $lineItemInter) {
            $id = $lineItem1->getId();
            $name = $lineItem1->getName();
            $object1 = (object)array('id' => $id, 'name' => $name);
            array_push($lineItemsInter, $object1);
        }
        ////////////

        // print line item native //
        $lineItemsNative = array();
        foreach ($resultsNative as $i => $lineItemNative) {
            $id = $lineItem2->getId();
            $name = $lineItem2->getName();
            $object2 = (object)array('id' => $id, 'name' => $name);
            array_push($lineItemsNative, $object2);
        }
        ///////////

        // print line item openApp //
        $lineItemsOpenApp = array();
        foreach ($resultsOpenApp as $i => $lineItemOpenApp) {
            $id = $lineItem3->getId();
            $name = $lineItem3->getName();
            $object3 = (object)array('id' => $id, 'name' => $name);
            array_push($lineItemsOpenApp, $object3);
        }
        ////////////

        return response()->json(['banner' => $lineItemsBanner, 'inter' => $lineItemsInter, 'native' => $lineItemsNative, 'openApp' => $lineItemsOpenApp]);

    }

    public function createMobileAppAdUnit($id, Request $request)
    {

        $validator = Validator::make($request->all(), [
//            'currentNetworkCode' => 'required',
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

                $getApplication = $this->getAppSoreID($id, $package_name);
                $applicationId = $getApplication[0]->applicationId;

                $this->createAdUnit($id, $package_name, $applicationId);
                return response()->json(['message' => 'Mobile Application and AdUnits are created Successfully!'], 200);

            } else {

                $getApplication = $this->getAppSoreID($id, $package_name);
                $applicationId = $getApplication[0]->applicationId;

                $checkIsAvailable = $this->getAdUnit($id, $applicationId);

                if ($checkIsAvailable->getStatusCode() == 400) {

                    $this->createAdUnit($id, $package_name, $applicationId);
                    return response()->json(['message' => 'AdUnits was created Successfully!'], 200);

                } else {
                    return response()->json(['message' => 'This application already create.'], 422);
                }


            }
        }

    }

    public function getAdUnit($id, $applicationId)
    {
        $adManagerSession = $this->AuthConnection($id);

        $serviceFactory = new ServiceFactory();

        $inventoryService = $serviceFactory->createInventoryService($adManagerSession);

        // Create a statement to select ad units.
        $pageSize = StatementBuilder::SUGGESTED_PAGE_LIMIT;
        $statementBuilder = (new StatementBuilder())->orderBy('id ASC')
            ->limit($pageSize);

        // Retrieve a small amount of ad units at a time, paging
        // through until all ad units have been retrieved.
        $totalResultSetSize = 0;
        do {
            $page = $inventoryService->getAdUnitsByStatement(
                $statementBuilder->toStatement()
            );


            // Print out some information for each ad unit.
            if ($page->getResults() !== null) {
                $totalResultSetSize = $page->getTotalResultSetSize();
                $i = $page->getStartIndex();

                $adUnitsArray = array();


                foreach ($page->getResults() as $adUnit) {
                    $id = $adUnit->getId();
                    $getName = $adUnit->getName();
                    $getApplicationId = $adUnit->getApplicationId();
                    $getApplicationId = $adUnit->getAdUnitCode();

                    $object = (object)array('id' => $id, 'name' => $getName, 'applicationId' => $getApplicationId);
                    array_push($adUnitsArray, $object);
                }

                $filteredArray = Arr::where($adUnitsArray, function ($value, $key) use ($applicationId) {
                    return $value->applicationId == $applicationId;
                });


                $checkArray = array_values($filteredArray);

                if ($checkArray) {
                    return response()->json(['message' => 'Mobile Application and adUnits both are created', 'data' => $checkArray], 200);
                } else {
                    return response()->json(['message' => 'Only Mobile Application is created AdUnits not created'], 400);
                }
            }

            $statementBuilder->increaseOffsetBy($pageSize);
        } while ($statementBuilder->getOffset() < $totalResultSetSize);

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
                'displayName' => 'HK Application - ' . uniqid(),
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

    public function getAppSoreID($id, $package_name)
    {

        $session = $this->AuthConnection($id);
        $adManagerServices = new AdManagerServices();

        $mobileApplicationService = $adManagerServices->get($session, MobileApplicationService::class);

        $statementBuilder = (new StatementBuilder())->orderBy('id DESC');

        do {
            $page = $mobileApplicationService->getMobileApplicationsByStatement(
                $statementBuilder->toStatement()
            );

            $applications = array();
            if ($page->getResults() !== null) {
                foreach ($page->getResults() as $mobileApplication) {

                    $id = $mobileApplication->getId();
                    $applicationId = $mobileApplication->getApplicationId();
                    $appStoreId = $mobileApplication->getAppStoreId();

                    $object = (object)array('id' => $id, 'applicationId' => $applicationId, 'appStoreId' => $appStoreId);
                    array_push($applications, $object);
                }


                $filteredArray = Arr::where($applications, function ($value, $key) use ($package_name) {
                    return $value->appStoreId == $package_name;
                });

                return array_values($filteredArray);

            }

        } while ($statementBuilder->getOffset() < $page->getTotalResultSetSize());

    }

    public function createAdUnit($id, $package_name, $applicationId)
    {

        $adManagerSession = $this->AuthConnection($id);

        $adManagerServices = new AdManagerServices();
        $inventoryService = $adManagerServices->get($adManagerSession, InventoryService::class);

        $serviceFactory = $serviceFactory = new ServiceFactory();
        $networkService = $serviceFactory->createNetworkService($adManagerSession);

        $network = $networkService->getCurrentNetwork();
        $effectiveRootAdUnitId = $network->getEffectiveRootAdUnitId();


        // ad unit for banner //

        $adUnitBanner = new AdUnit();
        $adUnitBanner->setName($package_name . ' - Banner');
        $adUnitBanner->setAdUnitCode($package_name . 'banner');
        $adUnitBanner->setParentId($effectiveRootAdUnitId);
        $adUnitBanner->setTargetWindow(AdUnitTargetWindow::TOP);
        $adUnitBanner->setApplicationId($applicationId);

        $adUnitSizesBanner = [];
        $adUnitSizeBanner = new AdUnitSize();
        $adUnitSizeBanner->setSize(['width' => 300, 'height' => 50]);
        $adUnitSizeBanner->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner;
        $adUnitSizeBanner1 = new AdUnitSize();
        $adUnitSizeBanner1->setSize(['width' => 300, 'height' => 100]);
        $adUnitSizeBanner1->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner1;
        $adUnitSizeBanner2 = new AdUnitSize();
        $adUnitSizeBanner2->setSize(['width' => 300, 'height' => 250]);
        $adUnitSizeBanner2->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner2;
        $adUnitSizeBanner3 = new AdUnitSize();
        $adUnitSizeBanner3->setSize(['width' => 320, 'height' => 50]);
        $adUnitSizeBanner3->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner3;
        $adUnitSizeBanner4 = new AdUnitSize();
        $adUnitSizeBanner4->setSize(['width' => 320, 'height' => 100]);
        $adUnitSizeBanner4->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner4;
        $adUnitSizeBanner5 = new AdUnitSize();
        $adUnitSizeBanner5->setSize(['width' => 320, 'height' => 250]);
        $adUnitSizeBanner5->setEnvironmentType('BROWSER');
        $adUnitSizesBanner[] = $adUnitSizeBanner5;

        $adUnitBanner->setAdUnitSizes($adUnitSizesBanner);


        ///////////////////

        // ad unit for Inter //
        $adUnitInter = new AdUnit();
        $adUnitInter->setName($package_name . ' - Inter');
        $adUnitInter->setAdUnitCode($package_name . 'inter');
        $adUnitInter->setParentId($effectiveRootAdUnitId);
        $adUnitInter->setTargetWindow(AdUnitTargetWindow::TOP);
        $adUnitInter->setApplicationId($applicationId);

        $adUnitSizesInter = [];
        $adUnitSizeInter = new AdUnitSize();
        $adUnitSizeInter->setSize(['width' => 320, 'height' => 480]);
        $adUnitSizeInter->setEnvironmentType('BROWSER');
        $adUnitSizesInter[] = $adUnitSizeInter;
        $adUnitSizeInter1 = new AdUnitSize();
        $adUnitSizeInter1->setSize(['width' => 480, 'height' => 320]);
        $adUnitSizeInter1->setEnvironmentType('BROWSER');
        $adUnitSizesInter[] = $adUnitSizeInter1;
        $adUnitSizeInter2 = new AdUnitSize();
        $adUnitSizeInter2->setSize(['width' => 768, 'height' => 1024]);
        $adUnitSizeInter2->setEnvironmentType('BROWSER');
        $adUnitSizesInter[] = $adUnitSizeInter2;
        $adUnitSizeInter3 = new AdUnitSize();
        $adUnitSizeInter3->setSize(['width' => 1024, 'height' => 768]);
        $adUnitSizeInter3->setEnvironmentType('BROWSER');
        $adUnitSizesInter[] = $adUnitSizeInter3;

        $adUnitInter->setAdUnitSizes($adUnitSizesInter);
        /////////////////////

        // ad unit for Native //
        $adUnitNative = new AdUnit();
        $adUnitNative->setName($package_name . ' - Native');
        $adUnitNative->setAdUnitCode($package_name . 'native');
        $adUnitNative->setParentId($effectiveRootAdUnitId);
        $adUnitNative->setTargetWindow(AdUnitTargetWindow::TOP);
        $adUnitNative->setApplicationId($applicationId);
        $adUnitNative->setIsFluid(true);
        $adUnitNative->setIsNative(true);

        $adUnitSizesNative = [];
        $adUnitSizeNative = new AdUnitSize();
        $adUnitSizeNative->setSize(['width' => 1, 'height' => 1]);
        $adUnitSizeNative->setEnvironmentType('BROWSER');
        $adUnitSizesNative[] = $adUnitSizeNative;


        $adUnitNative->setAdUnitSizes($adUnitSizesNative);

        /////////////////////

        // ad unit for openApp //
        $adUnitOpenApp = new AdUnit();
        $adUnitOpenApp->setName($package_name . ' - OpenApp');
        $adUnitOpenApp->setAdUnitCode($package_name . 'openApp');
        $adUnitOpenApp->setParentId($effectiveRootAdUnitId);
        $adUnitOpenApp->setTargetWindow(AdUnitTargetWindow::TOP);
        $adUnitOpenApp->setApplicationId($applicationId);

        $adUnitSizesOpenApp = [];
        $adUnitSizeOpenApp = new AdUnitSize();
        $adUnitSizeOpenApp->setSize(['width' => 320, 'height' => 480]);
        $adUnitSizeOpenApp->setEnvironmentType('BROWSER');
        $adUnitSizesOpenApp[] = $adUnitSizeOpenApp;
        $adUnitSizesOpenApp1 = new AdUnitSize();
        $adUnitSizesOpenApp1->setSize(['width' => 480, 'height' => 320]);
        $adUnitSizesOpenApp1->setEnvironmentType('BROWSER');
        $adUnitSizesOpenApp[] = $adUnitSizesOpenApp1;
        $adUnitSizesOpenApp2 = new AdUnitSize();
        $adUnitSizesOpenApp2->setSize(['width' => 768, 'height' => 1024]);
        $adUnitSizesOpenApp2->setEnvironmentType('BROWSER');
        $adUnitSizesOpenApp[] = $adUnitSizesOpenApp2;
        $adUnitSizesOpenApp3 = new AdUnitSize();
        $adUnitSizesOpenApp3->setSize(['width' => 1024, 'height' => 768]);
        $adUnitSizesOpenApp3->setEnvironmentType('BROWSER');
        $adUnitSizesOpenApp[] = $adUnitSizesOpenApp3;

        $adUnitOpenApp->setAdUnitSizes($adUnitSizesOpenApp);


        $inventoryService->createAdUnits([$adUnitBanner, $adUnitInter, $adUnitNative, $adUnitOpenApp]);
//        $inventoryService->createAdUnits([$adUnitNative]);

        return 'AdUnits created!';

    }

    ////////////////////////////////////

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
        $placement->setName('My Placement ' . uniqid());
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
