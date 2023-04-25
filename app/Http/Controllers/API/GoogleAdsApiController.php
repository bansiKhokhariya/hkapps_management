<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GetCurrentNetwork;
use App\Http\Controllers\Services\GetAllNetwork;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202302\ApiException;
use Google\AdsApi\AdManager\v202302\ServiceFactory;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Illuminate\Http\Request;
use Google\AdsApi\AdWords\AdWordsServices;
use Google\AdsApi\AdWords\AdWordsSessionBuilder;
use Google\AdsApi\AdWords\v201809\cm\CampaignService;
use Google\Auth\OAuth2;

require __DIR__ . '/../../../../vendor/autoload.php';

class GoogleAdsApiController extends Controller
{
//    public function index()
//    {
//
//        $oAuth2Credential = (new OAuth2TokenBuilder())
//            ->fromFile()
//            ->build();
//
//        $session = (new AdManagerSessionBuilder())
//            ->fromFile()
//            ->withOAuth2Credential($oAuth2Credential)
//            ->build();
//
//        $serviceFactory = new ServiceFactory();
//
//        $networkService = $serviceFactory->createNetworkService($session);
//
//
////        // Get the current network.
//        $network = $networkService->getCurrentNetwork();
//
//
//        printf(
//            "Network with code %d and display name '%s' was found.\n",
//            $network->getNetworkCode(),
//            $network->getDisplayName()
//        );
//
//    }

//    public static function GetCurrentNetwork()
//    {
//        $getCurrentNetwork = GetCurrentNetwork::main();
//    }
//
    public static function GetAllNetwork()
    {
        $getAllNetwork = GetAllNetwork::main();
    }

//    public function index()
//    {
//
//        session_start();
//
//        $oauth2 = new OAuth2([
//            'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
//            'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
//            'redirectUri' => 'https://panel.goldadx.com/',
//            'clientId' => '35728980000-ctd48dctv9rfo336mdq1vko2jrgortsl.apps.googleusercontent.com',
//            'clientSecret' => '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',
//            'scope' => '****'
//        ]);
//
//        if (!isset($_GET['code'])) {
//            // Create a 'state' token to prevent request forgery.
//            // Store it in the session for later validation.
//            $oauth2->setState(sha1(openssl_random_pseudo_bytes(1024)));
//            $_SESSION['oauth2state'] = $oauth2->getState();
//
//            // Redirect the user to the authorization URL.
//            $config = [
//                // Set to 'offline' if you require offline access.
//                'access_type' => 'online'
//            ];
//            header('Location: ' . $oauth2->buildFullAuthorizationUri($config));
//            exit;
//        } elseif (empty($_GET['state'])
//            || ($_GET['state'] !== $_SESSION['oauth2state'])) {
//            unset($_SESSION['oauth2state']);
//            exit('Invalid state.');
//        } else {
//            $oauth2->setCode($_GET['code']);
//            $authToken = $oauth2->fetchAuthToken();
//
//            // Store the refresh token for your user in your local storage if you
//            // requested offline access.
//            $refreshToken = $authToken['refresh_token'];
//
//        }
//
//        $session = (new AdWordsSessionBuilder())
//            ->fromFile()
//            ->withOAuth2Credential($oauth2)
//            ->build();
//
//        $adWordsServices = new AdWordsServices();
//
//        $campaignService = $adWordsServices->get($session, CampaignService::class);
//    }

}
