<?php

namespace InstagramAPI;

use InstagramAPI\Exception\InstagramException;
use InstagramAPI\Response\Model\User;
use InstagramAPI\Settings\StorageHandler;

/**
 * Instagram's Private API v7.0.1.
 *
 * TERMS OF USE:
 * - This code is in no way affiliated with, authorized, maintained, sponsored
 *   or endorsed by Instagram or any of its affiliates or subsidiaries. This is
 *   an independent and unofficial API. Use at your own risk.
 * - We do NOT support or tolerate anyone who wants to use this API to send spam
 *   or commit other online crimes.
 * - You will NOT use this API for marketing or other abusive purposes (spam,
 *   botting, harassment, massive bulk messaging...).
 *
 * @author mgp25: Founder, Reversing, Project Leader (https://github.com/mgp25)
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 */
class Instagram implements ExperimentsInterface
{
    const SUPPORTED_EVENTS = [
        'beforeRequest',
        'onResponse'
    ];


    /**
     * Currently active Instagram username.
     *
     * @var string
     */
    public $username;

    /**
     * Currently active Instagram password.
     *
     * @var string
     */
    public $password;

    /**
     * The Android device for the currently active user.
     *
     * @var \InstagramAPI\Devices\DeviceInterface
     */
    public $device;

    /**
     * Toggles API query/response debug output.
     *
     * @var bool
     */
    public $debug;

    /**
     * Toggles truncating long responses when debugging.
     *
     * @var bool
     */
    public $truncatedDebug;

    /**
     * For internal use by Instagram-API developers!
     *
     * Toggles the throwing of exceptions whenever Instagram-API's "Response"
     * classes lack fields that were provided by the server. Useful for
     * discovering that our library classes need updating.
     *
     * This is only settable via this public property and is NOT meant for
     * end-users of this library. It is for contributing developers!
     *
     * @var bool
     */
    public $apiDeveloperDebug = false;

    /**
     * UUID.
     *
     * @var string
     */
    public $uuid;

    /**
     * Google Play Advertising ID.
     *
     * The advertising ID is a unique ID for advertising, provided by Google
     * Play services for use in Google Play apps. Used by Instagram.
     *
     * @var string
     *
     * @see https://support.google.com/googleplay/android-developer/answer/6048248?hl=en
     */
    public $advertising_id;

    /**
     * Device ID.
     *
     * @var string
     */
    public $device_id;

    /**
     * Phone ID.
     *
     * @var string
     */
    public $phone_id;

    /**
     * Numerical UserPK ID of the active user account.
     *
     * @var string
     */
    public $account_id;

    /**
     * Instance of logged user
     * @var User
     */
    public $logged_in_user;

    /**
     * Raw API communication/networking class.
     *
     * @var Client
     */
    public $client;

    /**
     * The account settings storage.
     *
     * @var \InstagramAPI\Settings\StorageHandler|null
     */
    public $settings;

    /**
     * The current application session ID.
     *
     * This is a temporary ID which changes in the official app every time the
     * user closes and re-opens the Instagram application or switches account.
     *
     * @var string
     */
    public $session_id;

    /**
     * A list of experiments enabled on per-account basis.
     *
     * @var array
     */
    public $experiments;

    /**
     *
     * @var array
     */
    protected $_events = [];

    /** @var Request\Account Collection of Account related functions. */
    public $account;
    /** @var Request\Business Collection of Business related functions. */
    public $business;
    /** @var Request\Collection Collection of Collections related functions. */
    public $collection;
    /** @var Request\Creative Collection of Creative related functions. */
    public $creative;
    /** @var Request\Direct Collection of Direct related functions. */
    public $direct;
    /** @var Request\Discover Collection of Discover related functions. */
    public $discover;
    /** @var Request\Hashtag Collection of Hashtag related functions. */
    public $hashtag;
    /** @var Request\Highlight Collection of Highlight related functions. */
    public $highlight;
    /** @var Request\TV Collection of Instagram TV functions. */
    public $tv;
    /** @var Request\Internal Collection of Internal (non-public) functions. */
    public $internal;
    /** @var Request\Live Collection of Live related functions. */
    public $live;
    /** @var Request\Location Collection of Location related functions. */
    public $location;
    /** @var Request\Media Collection of Media related functions. */
    public $media;
    /** @var Request\People Collection of People related functions. */
    public $people;
    /** @var Request\Push Collection of Push related functions. */
    public $push;
    /** @var Request\Shopping Collection of Shopping related functions. */
    public $shopping;
    /** @var Request\Story Collection of Story related functions. */
    public $story;
    /** @var Request\Timeline Collection of Timeline related functions. */
    public $timeline;
    /** @var Request\Usertag Collection of Usertag related functions. */
    public $usertag;

    /**
     * Constructor.
     *
     * @param bool  $debug          Show API queries and responses.
     * @param bool  $truncatedDebug Truncate long responses in debug.
     * @param array $storageConfig  Configuration for the desired
     *                              user settings storage backend.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     */
    public function __construct(
        $debug = false,
        $truncatedDebug = false,
        array $storageConfig = [])
    {
        // Debugging options.
        $this->debug = $debug;
        $this->truncatedDebug = $truncatedDebug;

        // Load all function collections.
        $this->account = new Request\Account($this);
        $this->business = new Request\Business($this);
        $this->collection = new Request\Collection($this);
        $this->creative = new Request\Creative($this);
        $this->direct = new Request\Direct($this);
        $this->discover = new Request\Discover($this);
        $this->hashtag = new Request\Hashtag($this);
        $this->highlight = new Request\Highlight($this);
        $this->tv = new Request\TV($this);
        $this->internal = new Request\Internal($this);
        $this->live = new Request\Live($this);
        $this->location = new Request\Location($this);
        $this->media = new Request\Media($this);
        $this->people = new Request\People($this);
        $this->push = new Request\Push($this);
        $this->shopping = new Request\Shopping($this);
        $this->story = new Request\Story($this);
        $this->timeline = new Request\Timeline($this);
        $this->usertag = new Request\Usertag($this);

        // Configure the settings storage and network client.
        $self = $this;
        $this->settings = Settings\Factory::createHandler(
            $storageConfig,
            [
                // This saves all user session cookies "in bulk" at script exit
                // or when switching to a different user, so that it only needs
                // to write cookies to storage a few times per user session:
                'onCloseUser' => function (StorageHandler $storage) use ($self) {
                    if ($self->client instanceof Client) {
                        $self->client->saveCookieJar();
                        $storage->saveCurrentUserSettings();
                    }
                },
            ]
        );
        $this->client = new Client($this);
        $this->experiments = [];

        $this->addEvent('onResponse', function (Response $responseObject) {
            $this->updateStateFromResponse($responseObject);
        });
    }

    /**
     * Controls the SSL verification behavior of the Client.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#verify
     *
     * @param bool|string $state TRUE to verify using PHP's default CA bundle,
     *                           FALSE to disable SSL verification (this is
     *                           insecure!), String to verify using this path to
     *                           a custom CA bundle file.
     */
    public function setVerifySSL(
        $state)
    {
        $this->client->setVerifySSL($state);
    }

    /**
     * Gets the current SSL verification behavior of the Client.
     *
     * @return bool|string
     */
    public function getVerifySSL()
    {
        return $this->client->getVerifySSL();
    }

    /**
     * Set the proxy to use for requests.
     *
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
     *
     * @param string|array|null $value String or Array specifying a proxy in
     *                                 Guzzle format, or NULL to disable
     *                                 proxying.
     */
    public function setProxy(
        $value)
    {
        $this->client->setProxy($value);
    }

    /**
     * Gets the current proxy used for requests.
     *
     * @return string|array|null
     */
    public function getProxy()
    {
        return $this->client->getProxy();
    }

    /**
     * Sets the network interface override to use.
     *
     * Only works if Guzzle is using the cURL backend. But that's
     * almost always the case, on most PHP installations.
     *
     * @see http://php.net/curl_setopt CURLOPT_INTERFACE
     *
     * @param string|null $value Interface name, IP address or hostname, or NULL
     *                           to disable override and let Guzzle use any
     *                           interface.
     */
    public function setOutputInterface(
        $value)
    {
        $this->client->setOutputInterface($value);
    }

    /**
     * Gets the current network interface override used for requests.
     *
     * @return string|null
     */
    public function getOutputInterface()
    {
        return $this->client->getOutputInterface();
    }

    /**
     * Internal login handler.
     *
     * @param string $username
     * @param string $password
     * @param bool   $forceLogin         Force login to Instagram instead of
     *                                   resuming previous session. Used
     *                                   internally to do a new, full relogin
     *                                   when we detect an expired/invalid
     *                                   previous session.
     * @param int    $appRefreshInterval
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse|null
     *
     * @see Instagram::login() The public login handler with a full description.
     */
    public function login(
        $username,
        $password
    ) {
        if (empty($username) || empty($password))
            throw new \InvalidArgumentException('You must provide a username and password to _login().');

        // Switch the currently active user/pass if the details are different.
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        if (empty($this->logged_in_user->getPk()) || empty($this->account_id)) {
            $this->_sendPreLoginFlow();

            try {
                $response = $this->account->login($username, $password);
            } catch (\InstagramAPI\Exception\InstagramException $e) {
                if ($e->hasResponse() && $e->getResponse()->isTwoFactorRequired()) {
                    // Login failed because two-factor login is required.
                    // Return server response to tell user they need 2-factor.
                    return $e->getResponse();
                } else {
                    // Login failed for some other reason... Re-throw error.
                    throw $e;
                }
            }

            $this->_updateLoginState($response);

            $this->_sendPostLoginFlow();
        }

        return $this->logged_in_user;
    }

    /**
     * Finish a two-factor authenticated login.
     *
     * This function finishes a two-factor challenge that was provided by the
     * regular `login()` function. If you successfully answer their challenge,
     * you will be logged in after this function call.
     *
     * @param string $username            Your Instagram username used for logging
     * @param string $password            Your Instagram password.
     * @param string $twoFactorIdentifier Two factor identifier, obtained in
     *                                    login() response. Format: `123456`.
     * @param string $verificationCode    Verification code you have received
     *                                    via SMS.
     * @param string $verificationMethod  The verification method for 2FA. 1 is SMS,
     *                                    2 is backup codes and 3 is TOTP.
     * @param int    $appRefreshInterval  See `login()` for description of this
     *                                    parameter.
     * @param string $usernameHandler     Instagram username sent in the login response,
     *                                    Email and phone aren't allowed here.
     *                                    Default value is the first argument $username
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse
     */
    public function finishTwoFactorLogin(
        $username,
        $password,
        $twoFactorIdentifier,
        $verificationCode,
        $verificationMethod = '1',
        $appRefreshInterval = 1800,
        $usernameHandler = null)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to finishTwoFactorLogin().');
        }
        if (empty($verificationCode) || empty($twoFactorIdentifier)) {
            throw new \InvalidArgumentException('You must provide a verification code and two-factor identifier to finishTwoFactorLogin().');
        }
        if (!in_array($verificationMethod, ['1', '2', '3'], true)) {
            throw new \InvalidArgumentException('You must provide a valid verification method value.');
        }

        // Switch the currently active user/pass if the details are different.
        // NOTE: The username and password AREN'T actually necessary for THIS
        // endpoint, but this extra step helps people who statelessly embed the
        // library directly into a webpage, so they can `finishTwoFactorLogin()`
        // on their second page load without having to begin any new `login()`
        // call (since they did that in their previous webpage's library calls).
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        $username = ($usernameHandler !== null) ? $usernameHandler : $username;

        $response = $this->account->twoFactorLogin($username, $twoFactorIdentifier, $verificationCode, $verificationMethod);

        $this->_updateLoginState($response);

        $this->_sendPostLoginFlow();

        return $response;
    }

    /**
     * Request a new security code SMS for a Two Factor login account.
     *
     * NOTE: You should first attempt to `login()` which will automatically send
     * you a two factor SMS. This function is just for asking for a new SMS if
     * the old code has expired.
     *
     * NOTE: Instagram can only send you a new code every 60 seconds.
     *
     * @param string $username            Your Instagram username.
     * @param string $password            Your Instagram password.
     * @param string $twoFactorIdentifier Two factor identifier, obtained in
     *                                    `login()` response.
     * @param string $usernameHandler     Instagram username sent in the login response,
     *                                    Email and phone aren't allowed here.
     *                                    Default value is the first argument $username
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\TwoFactorLoginSMSResponse
     */
    public function sendTwoFactorLoginSMS(
        $username,
        $password,
        $twoFactorIdentifier,
        $usernameHandler = null)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to sendTwoFactorLoginSMS().');
        }
        if (empty($twoFactorIdentifier)) {
            throw new \InvalidArgumentException('You must provide a two-factor identifier to sendTwoFactorLoginSMS().');
        }

        // Switch the currently active user/pass if the details are different.
        // NOTE: The password IS NOT actually necessary for THIS
        // endpoint, but this extra step helps people who statelessly embed the
        // library directly into a webpage, so they can `sendTwoFactorLoginSMS()`
        // on their second page load without having to begin any new `login()`
        // call (since they did that in their previous webpage's library calls).
        if ($this->username !== $username || $this->password !== $password) {
            $this->_setUser($username, $password);
        }

        $username = ($usernameHandler !== null) ? $usernameHandler : $username;

        return $this->account->sendTwoFactorLoginSms($username, $twoFactorIdentifier);
    }

    /**
     * Request information about available password recovery methods for an account.
     *
     * This will tell you things such as whether SMS or EMAIL-based recovery is
     * available for the given account name.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\UsersLookupResponse
     */
    public function userLookup(
        $username)
    {
        // Set active user (without pwd), and create database entry if new user.
        $this->_setUserWithoutPassword($username);

        return $this->account->lookup($username);
    }

    /**
     * Request a recovery EMAIL to get back into your account.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\RecoveryResponse
     */
    public function sendRecoveryEmail(
        $username)
    {
        // Verify that they can use the recovery email option.
        $userLookup = $this->userLookup($username);
        if (!$userLookup->getCanEmailReset()) {
            throw new \InstagramAPI\Exception\InternalException('Email recovery is not available, since your account lacks a verified email address.');
        }

        return $this->account->sendRecoveryFlowEmail($username);
    }

    /**
     * Request a recovery SMS to get back into your account.
     *
     * `WARNING:` You can call this function without having called `login()`,
     * but be aware that a user database entry will be created for every
     * username you try to look up. This is ONLY meant for recovering your OWN
     * accounts.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\RecoveryResponse
     */
    public function sendRecoverySMS(
        $username)
    {
        // Verify that they can use the recovery SMS option.
        $userLookup = $this->userLookup($username);
        if (!$userLookup->getHasValidPhone() || !$userLookup->getCanSmsReset()) {
            throw new \InstagramAPI\Exception\InternalException('SMS recovery is not available, since your account lacks a verified phone number.');
        }

        return $this->account->sendRecoverySms($username);
    }

    /**
     * Set the active account for the class instance.
     *
     * We can call this multiple times to switch between multiple accounts.
     *
     * @param string $username Your Instagram username.
     * @param string $password Your Instagram password.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _setUser(
        $username,
        $password)
    {
        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('You must provide a username and password to _setUser().');
        }

        // Load all settings from the storage and mark as current user.
        $this->settings->setActiveUser($username);

        // Generate the user's device instance, which will be created from the
        // user's last-used device IF they've got a valid, good one stored.
        // But if they've got a BAD/none, this will create a brand-new device.
        $savedDeviceString = $this->settings->get('devicestring');
        $this->device = new Devices\Device(
            Constants::IG_VERSION,
            Constants::VERSION_CODE,
            Constants::LOCALE,
            $savedDeviceString
        );

        // Get active device string so that we can compare it to any saved one.
        $deviceString = $this->device->getDeviceString();

        // Generate a brand-new device fingerprint if the device wasn't reused
        // from settings, OR if any of the stored fingerprints are missing.
        // NOTE: The regeneration when our device model changes is to avoid
        // dangerously reusing the "previous phone's" unique hardware IDs.
        // WARNING TO CONTRIBUTORS: Only add new parameter-checks here if they
        // are CRITICALLY important to the particular device. We don't want to
        // frivolously force the users to generate new device IDs constantly.
        $resetCookieJar = false;
        if (
            $deviceString !== $savedDeviceString // Brand new device, or missing
            || empty($this->settings->get('uuid')) // one of the critically...
            || empty($this->settings->get('phone_id')) // ...important device...
            || empty($this->settings->get('device_id'))
        ) { // ...parameters.
            // Erase all previously stored device-specific settings and cookies.
            $this->settings->clearSettings();

            // Save the chosen device string to settings.
            $this->settings->set('devicestring', $deviceString);

            // Generate hardware fingerprints for the new device.
            $this->settings->set('device_id', Signatures::generateDeviceId());
            $this->settings->set('phone_id', Signatures::generateUUID(true));
            $this->settings->set('uuid', Signatures::generateUUID(true));

            // Erase any stored account ID, to ensure that we detect ourselves
            // as logged-out. This will force a new relogin from the new device.
            $this->settings->set('logged_in_user', (new User())->serialize());

            // We'll also need to throw out all previous cookies.
            $resetCookieJar = true;
        }

        // Generate other missing values. These are for less critical parameters
        // that don't need to trigger a complete device reset like above. For
        // example, this is good for new parameters that Instagram introduces
        // over time, since those can be added one-by-one over time here without
        // needing to wipe/reset the whole device.
        if (empty($this->settings->get('advertising_id'))) {
            $this->settings->set('advertising_id', Signatures::generateUUID(true));
        }
        if (empty($this->settings->get('session_id'))) {
            $this->settings->set('session_id', Signatures::generateUUID(true));
        }

        // Store various important parameters for easy access.
        $this->username = $username;
        $this->password = $password;
        $this->uuid = $this->settings->get('uuid');
        $this->advertising_id = $this->settings->get('advertising_id');
        $this->device_id = $this->settings->get('device_id');
        $this->phone_id = $this->settings->get('phone_id');
        $this->session_id = $this->settings->get('session_id');
        $this->experiments = $this->settings->getExperiments();

        $this->logged_in_user = new User();
        $this->logged_in_user->unserialize($this->settings->get('logged_in_user'));

        // Load the previous session details if we're possibly logged in.
        if (!$resetCookieJar && $this->settings->isMaybeLoggedIn()) {
            $this->account_id = $this->settings->get('account_id');
        } else {
            $this->account_id = null;
        }

        // Configures Client for current user AND updates isMaybeLoggedIn state
        // if it fails to load the expected cookies from the user's jar.
        // Must be done last here, so that isMaybeLoggedIn is properly updated!
        // NOTE: If we generated a new device we start a new cookie jar.
        $this->client->updateFromCurrentSettings($resetCookieJar);
    }

    /**
     * Set the active account for the class instance, without knowing password.
     *
     * This internal function is used by all unauthenticated pre-login functions
     * whenever they need to perform unauthenticated requests, such as looking
     * up a user's account recovery options.
     *
     * `WARNING:` A user database entry will be created for every username you
     * set as the active user, exactly like the normal `_setUser()` function.
     * This is necessary so that we generate a user-device and data storage for
     * each given username, which gives us necessary data such as a "device ID"
     * for the new user's virtual device, to use in various API-call parameters.
     *
     * `WARNING:` This function CANNOT be used for performing logins, since
     * Instagram will validate the password and will reject the missing
     * password. It is ONLY meant to be used for *RECOVERY* PRE-LOGIN calls that
     * need device parameters when the user DOESN'T KNOW their password yet.
     *
     * @param string $username Your Instagram username.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _setUserWithoutPassword(
        $username)
    {
        if (empty($username) || !is_string($username)) {
            throw new \InvalidArgumentException('You must provide a username.');
        }

        // Switch the currently active user/pass if the username is different.
        // NOTE: Creates a user database (device) for the user if they're new!
        // NOTE: Because we don't know their password, we'll mark the user as
        // having "NOPASSWORD" as pwd. The user will fix that when/if they call
        // `login()` with the ACTUAL password, which will tell us what it is.
        // We CANNOT use an empty string since `_setUser()` will not allow that!
        // NOTE: If the user tries to look up themselves WHILE they are logged
        // in, we'll correctly NOT call `_setUser()` since they're already set.
        if ($this->username !== $username) {
            $this->_setUser($username, 'NOPASSWORD');
        }
    }

    /**
     * Updates the internal state after a successful login.
     *
     * @param Response\LoginResponse $response The login response.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _updateLoginState(
        Response\LoginResponse $response)
    {
        // This check is just protection against accidental bugs. It makes sure
        // that we always call this function with a *successful* login response!
        if (!$response instanceof Response\LoginResponse || !$response->isOk()) {
            throw new \InvalidArgumentException('Invalid login response provided to _updateLoginState().');
        }

        $this->logged_in_user = $response->getLoggedInUser();
        $this->account_id     = $response->getLoggedInUser()->getPk();

        $this->settings->set('logged_in_user', $response->getLoggedInUser()->serialize());
        $this->settings->set('account_id', $this->account_id);
        $this->settings->set('last_login', time());
    }

    public function updateStateFromResponse(Response $response) {
        $headersMap = [
            'x-ig-set-www-claim' => 'www_claim',
            'ig-set-authorization' => 'authorization',
            'ig-set-password-encryption-key-id' => 'public_key_id',
            'ig-set-password-encryption-pub-key' => 'public_key'
        ];

        foreach ($headersMap as $headerName => $storageKey) {
            $v = $response->getHttpResponse()->getHeaderLine($headerName);
            if (
                empty($v) ||
                ($storageKey == 'authorization' && str_ends_with($v, ':'))
            )
                continue;

            $this->settings->set($storageKey, $v);
        }
    }

    /**
     * Sends pre-login flow. This is required to emulate real device behavior.
     *
     * @throws \InstagramAPI\Exception\InstagramException
     */
    protected function _sendPreLoginFlow()
    {
        $this->internal->readMsisdnHeader('default');
        $this->internal->bootstrapMsisdnHeader();
        // Reset zero rating rewrite rules.
        $this->client->zeroRating()->reset();
        // Calling this non-token API will put a csrftoken in our cookie
        // jar. We must do this before any functions that require a token.
        $this->internal->fetchZeroRatingToken();
        $this->account->setContactPointPrefill('prefill');
        //$this->internal->syncDeviceFeatures(true);
        $this->internal->preLoginLauncherSync();
        $this->internal->logAttribution();
        $this->account->getPrefillCandidates();
    }

    /**
     * Sends login flow. This is required to emulate real device behavior.
     *
     * @param bool $justLoggedIn       Whether we have just performed a full
     *                                 relogin (rather than doing a resume).
     * @param int  $appRefreshInterval See `login()` for description of this
     *                                 parameter.
     *
     * @throws \InvalidArgumentException
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LoginResponse|null A login response if a
     *                                                   full (re-)login is
     *                                                   needed during the login
     *                                                   flow attempt, otherwise
     *                                                   `NULL`.
     */
    protected function _sendPostLoginFlow()
    {
        // Reset zero rating rewrite rules.
        $this->client->zeroRating()->reset();
        $this->internal->fetchZeroRatingToken();
        // Perform the "user has just done a full login" API flow.
        //$this->account->getAccountFamily();
        $this->internal->postLoginLauncherSync(); // postLoginSync
        //$this->internal->syncUserFeatures(); // qe.syncExperiments
        $this->internal->logAttribution();
        Utils::catch(function () { $this->internal->logResurrectAttribution(); });
        $this->internal->getLoomFetchConfig(); // loom.fetchConfig()
        $this->account->getLinkageStatus();
        $this->timeline->getTimelineFeed();
        $this->story->getReelsTrayFeed('cold_start');
        $this->story->getReelsMediaFeed($this->account_id);
        //$this->discover->getSuggestedSearches('users');
        //$this->discover->getSuggestedSearches('blended');
        $this->discover->getRecentSearches();
        $this->direct->getRankedRecipients('reshare', true);
        $this->direct->getRankedRecipients('raven', true);
        $this->direct->getPresences();
        $this->direct->getInbox(null, 20, 10);
        $this->media->getBlockedMedia();
        $this->internal->getQPFetch();
        $this->internal->getQPCooldowns();
        $this->internal->getArlinkDownloadInfo();
        $this->discover->getExploreFeed(null, null, true); // discover.topicalExplore
        $this->people->getMarkSuSeen();
        $this->internal->getFacebookOTA();

        // We've now performed a login or resumed a session. Forcibly write our
        // cookies to the storage, to ensure that the storage doesn't miss them
        // in case something bad happens to PHP after this moment.
        $this->client->saveCookieJar();

        return null;
    }

    /**
     * Log out of Instagram.
     *
     * WARNING: Most people should NEVER call `logout()`! Our library emulates
     * the Instagram app for Android, where you are supposed to stay logged in
     * forever. By calling this function, you will tell Instagram that you are
     * logging out of the APP. But you SHOULDN'T do that! In almost 100% of all
     * cases you want to *stay logged in* so that `login()` resumes your session!
     *
     * @throws \InstagramAPI\Exception\InstagramException
     *
     * @return \InstagramAPI\Response\LogoutResponse
     *
     * @see Instagram::login()
     */
    public function logout()
    {
        $response = $this->account->logout();

        // We've now logged out. Forcibly write our cookies to the storage, to
        // ensure that the storage doesn't miss them in case something bad
        // happens to PHP after this moment.
        $this->client->saveCookieJar();

        return $response;
    }

    /**
     * Checks if a parameter is enabled in the given experiment.
     *
     * @param string $experiment
     * @param string $param
     * @param bool   $default
     *
     * @return bool
     */
    public function isExperimentEnabled(
        $experiment,
        $param,
        $default = false)
    {
        return isset($this->experiments[$experiment][$param])
            ? in_array($this->experiments[$experiment][$param], ['enabled', 'true', '1'])
            : $default;
    }

    /**
     * Get a parameter value for the given experiment.
     *
     * @param string $experiment
     * @param string $param
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getExperimentParam(
        $experiment,
        $param,
        $default = null)
    {
        return isset($this->experiments[$experiment][$param])
            ? $this->experiments[$experiment][$param]
            : $default;
    }

    /**
     * Create a custom API request.
     *
     * Used internally, but can also be used by end-users if they want
     * to create completely custom API queries without modifying this library.
     *
     * @param string $url
     *
     * @return \InstagramAPI\Request
     */
    public function request(
        $url)
    {
        return new Request($this, $url);
    }


    /**
     * Triggers an event.
     * @param $eventName string Event name
     * @param ...$handlerArguments
     * @return void
     */
    public function triggerEvent($eventName, ...$handlerArguments) {
        // Reject anything that isn't in our list of VALID callbacks.
        if (!in_array($eventName, self::SUPPORTED_EVENTS)) {
            throw new InstagramException(sprintf(
                'The string "%s" is not a valid event name.',
                $eventName
            ));
        }

        // Trigger the callback with a reference to our StorageHandler instance.
        foreach (($this->_events[$eventName] ?? []) as $eventHandler)
            call_user_func_array($eventHandler, $handlerArguments);
    }


    /**
     * Add an event handler
     * @param string $eventName
     * @param callable $handler
     * @return self
     */
    public function addEvent(string $eventName, callable $handler): self {
        if (!isset($this->_events[$eventName]))
            $this->_events[$eventName] = [];

        $this->_events[$eventName][] = $handler;

        return $this;
    }
}
