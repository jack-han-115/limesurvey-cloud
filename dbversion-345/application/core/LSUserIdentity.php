<?php
use LimeSurvey\PluginManager\PluginEvent;
/*
* LimeSurvey
* Copyright (C) 2007-2013 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
*/

/**
 * For 2.06 most of the functionality in this class will be moved to the LSWebUser class.
 * To not delay release of 2.05 this class was kept the way it is now.
 *
 * @@TODO Move to LSWebUser and change documentation / workflow for authentication plugins
 */
class LSUserIdentity extends CUserIdentity
{

    const ERROR_IP_LOCKED_OUT = 98;
    const ERROR_UNKNOWN_HANDLER = 99;

    protected $config = array();

    /**
     * The userid
     *
     * @var int
     */
    public $id = null;

    /**
     * A User::model() object
     *
     * @var User
     */
    public $user;

    /**
     * This is the name of the plugin to handle authentication
     * default handler is used for remote control
     *
     * @var string
     */
    public $plugin = 'Authdb';

    public function authenticate()
    {
        // First initialize the result, we can later retieve it to get the exact error code/message
        $result = new LSAuthResult(self::ERROR_NONE);

        // Check if the ip is locked out
        if (FailedLoginAttempt::model()->isLockedOut()) {
            $message = sprintf(gT('You have exceeded the number of maximum login attempts. Please wait %d minutes before trying again.'), App()->getConfig('timeOutTime') / 60);
            $result->setError(self::ERROR_IP_LOCKED_OUT, $message);
        }

        // If still ok, continue
        if ($result->isValid()) {
            if (is_null($this->plugin)) {
                $result->setError(self::ERROR_UNKNOWN_HANDLER);
            } else {
                // Delegate actual authentication to plugin
                $authEvent = new PluginEvent('newUserSession', $this); // TODO: rename the plugin function authenticate()
                $authEvent->set('identity', $this);
                App()->getPluginManager()->dispatchEvent($authEvent);
                $pluginResult = $authEvent->get('result');
                if ($pluginResult instanceof LSAuthResult) {
                    $result = $pluginResult;
                } else {
                    $result->setError(self::ERROR_UNKNOWN_IDENTITY);
                }
            }
        }

        if ($result->isValid()) {
            // Perform postlogin
            regenerateCSRFToken();
            $this->postLogin();
            // Reset counter after successful login
            FailedLoginAttempt::model()->deleteAttempts();
        } else {
            // Log a failed attempt
            FailedLoginAttempt::model()->addAttempt();
            regenerateCSRFToken();
            App()->session->regenerateID(); // Handled on login by Yii
        }

        $this->errorCode = $result->getCode();
        $this->errorMessage = $result->getMessage();

        return $result->isValid();
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the current user's ID
     *
     * @access public
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the active user's record
     *
     * @access public
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
    * Code adpated from joomla 3.2.1 JUserHelper::verifyPassword()
     * Formats a password using the current encryption. If the user ID is given
     * and the hash does not fit the current hashing algorithm, it automatically
     * updates the hash.
     *
     * @param   string   $password  The plaintext password to check.
     * @param   string   $hash      The hash to verify against.
     * @param   integer  $user_id   ID of the user if the password hash should be updated
     *
     * @return  boolean  True if the password and hash match, false otherwise
     *
     * @since   Joomla 3.2.1
     */
    protected function joomla_verifyPassword($password, $hash)
    {

        if(!function_exists('hash_equals'))
        {
            function hash_equals($str1, $str2)
            {
                if(strlen($str1) != strlen($str2))
                {
                    return false;
                }
                else
                {
                    $res = $str1 ^ $str2;
                    $ret = 0;
                    for($i = strlen($res) - 1; $i >= 0; $i--)
                    {
                        $ret |= ord($res[$i]);
                    }
                    return !$ret;
                }
            }
        }

        //$rehash = false;
        $match = false;

        // If we are using phpass
        if (strpos($hash, '$P$') === 0){
            // Use PHPass's portable hashes with a cost of 10.
            $phpass = Yii::app()->phpass; //new PasswordHash(10, true);

            $match = $phpass->CheckPassword($password, $hash);

            //$rehash = true;
        }elseif ($hash[0] == '$'){
            // JCrypt::hasStrongPasswordSupport() includes a fallback for us in the worst case
            //JCrypt::hasStrongPasswordSupport();
            $match = password_verify($password, $hash);

            // Uncomment this line if we actually move to bcrypt.
            //$rehash = password_needs_rehash($hash, PASSWORD_DEFAULT);
        }elseif (substr($hash, 0, 8) == '{SHA256}'){
            // Check the password
            $parts     = explode(':', $hash);
            $crypt     = $parts[0];
            $salt      = @$parts[1];
            $testcrypt = static::getCryptedPassword($password, $salt, 'sha256', true);

            //$match = JCrypt::timingSafeCompare($hash, $testcrypt);
            $match = hash_equals((string) $hash, (string) $testcrypt);
        }else{
            // Check the password
            $parts = explode(':', $hash);
            $crypt = $parts[0];
            $salt  = @$parts[1];

            // Compile the hash to compare
            // If the salt is empty AND there is a ':' in the original hash, we must append ':' at the end
            $testcrypt = md5($password . $salt) . ($salt ? ':' . $salt : (strpos($hash, ':') !== false ? ':' : ''));

            $match = hash_equals((string) $hash, (string) $testcrypt);
        }

        return $match;

    }

    /**
     * From Joomla : Formats a password using the current encryption.
     *
     * @param   string   $plaintext     The plaintext password to encrypt.
     * @param   string   $salt          The salt to use to encrypt the password. []
     *                                  If not present, a new salt will be
     *                                  generated.
     * @param   string   $encryption    The kind of password encryption to use.
     *                                  Defaults to md5-hex.
     * @param   boolean  $show_encrypt  Some password systems prepend the kind of
     *                                  encryption to the crypted password ({SHA},
     *                                  etc). Defaults to false.
     *
     * @return  string  The encrypted password.
     *
     * @since   11.1
     * @deprecated  4.0
     */
    public static function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
    {
        // Get the salt to use.
        $salt = static::getSalt($encryption, $salt, $plaintext);

        // Encrypt the password.
        switch ($encryption)
        {
            case 'plain':
                return $plaintext;

            case 'sha':
                $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));

                return ($show_encrypt) ? '{SHA}' . $encrypted : $encrypted;

            case 'crypt':
            case 'crypt-des':
            case 'crypt-md5':
            case 'crypt-blowfish':
                return ($show_encrypt ? '{crypt}' : '') . crypt($plaintext, $salt);

            case 'md5-base64':
                $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));

                return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;

            case 'ssha':
                $encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext . $salt) . $salt);

                return ($show_encrypt) ? '{SSHA}' . $encrypted : $encrypted;

            case 'smd5':
                $encrypted = base64_encode(mhash(MHASH_MD5, $plaintext . $salt) . $salt);

                return ($show_encrypt) ? '{SMD5}' . $encrypted : $encrypted;

            case 'aprmd5':
                $length = strlen($plaintext);
                $context = $plaintext . '$apr1$' . $salt;
                $binary = static::_bin(md5($plaintext . $salt . $plaintext));

                for ($i = $length; $i > 0; $i -= 16)
                {
                    $context .= substr($binary, 0, ($i > 16 ? 16 : $i));
                }

                for ($i = $length; $i > 0; $i >>= 1)
                {
                    $context .= ($i & 1) ? chr(0) : $plaintext[0];
                }

                $binary = static::_bin(md5($context));

                for ($i = 0; $i < 1000; $i++)
                {
                    $new = ($i & 1) ? $plaintext : substr($binary, 0, 16);

                    if ($i % 3)
                    {
                        $new .= $salt;
                    }

                    if ($i % 7)
                    {
                        $new .= $plaintext;
                    }

                    $new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
                    $binary = static::_bin(md5($new));
                }

                $p = array();

                for ($i = 0; $i < 5; $i++)
                {
                    $k = $i + 6;
                    $j = $i + 12;

                    if ($j == 16)
                    {
                        $j = 5;
                    }

                    $p[] = static::_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
                }

                return '$apr1$' . $salt . '$' . implode('', $p) . static::_toAPRMD5(ord($binary[11]), 3);

            case 'sha256':
                $encrypted = ($salt) ? hash('sha256', $plaintext . $salt) . ':' . $salt : hash('sha256', $plaintext);

                return ($show_encrypt) ? '{SHA256}' . $encrypted : '{SHA256}' . $encrypted;

            case 'md5-hex':
            default:
                $encrypted = ($salt) ? md5($plaintext . $salt) : md5($plaintext);

                return ($show_encrypt) ? '{MD5}' . $encrypted : $encrypted;
        }
    }


    /**
     * Converts to allowed 64 characters for APRMD5 passwords.
     *
     * @param   string   $value  The value to convert.
     * @param   integer  $count  The number of characters to convert.
     *
     * @return  string  $value converted to the 64 MD5 characters.
     *
     * @since   11.1
     */
    protected static function _toAPRMD5($value, $count)
    {
        /* 64 characters that are valid for APRMD5 passwords. */
        $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $aprmd5 = '';
        $count = abs($count);

        while (--$count)
        {
            $aprmd5 .= $APRMD5[$value & 0x3f];
            $value >>= 6;
        }

        return $aprmd5;
    }

    /**
    * TODO: JCrypt genRandomBytes !!!!! allelouia, it returns only: return random_bytes($length); where lenght is 16
     * From Joomla: Returns a salt for the appropriate kind of password encryption.
     * Optionally takes a seed and a plaintext password, to extract the seed
     * of an existing password, or for encryption types that use the plaintext
     * in the generation of the salt.
     *
     * @param   string  $encryption  The kind of password encryption to use.
     *                               Defaults to md5-hex.
     * @param   string  $seed        The seed to get the salt from (probably a
     *                               previously generated password). Defaults to
     *                               generating a new seed.
     * @param   string  $plaintext   The plaintext password that we're generating
     *                               a salt for. Defaults to none.
     *
     * @return  string  The generated or extracted salt.
     *
     * @since   11.1
     * @deprecated  4.0
     */
    public static function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
    {
        // Encrypt the password.
        switch ($encryption)
        {
            case 'crypt':
            case 'crypt-des':
                if ($seed)
                {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
                }
                else
                {
                    return substr(md5(mt_rand()), 0, 2);
                }
                break;

            case 'sha256':
                if ($seed)
                {
                    return preg_replace('|^{sha256}|i', '', $seed);
                }
                else
                {
                    return static::genRandomPassword(16);
                }
                break;

            case 'crypt-md5':
                if ($seed)
                {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
                }
                else
                {
                    return '$1$' . substr(md5(random_bytes(16)), 0, 8) . '$';
                }
                break;

            case 'crypt-blowfish':
                if ($seed)
                {
                    return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
                }
                else
                {
                    return '$2$' . substr(md5(random_bytes(16)), 0, 12) . '$';
                }
                break;

            case 'ssha':
                if ($seed)
                {
                    return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
                }
                else
                {
                    return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(random_bytes(16))), 0, 8), 4);
                }
                break;

            case 'smd5':
                if ($seed)
                {
                    return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
                }
                else
                {
                    return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(random_bytes(16))), 0, 8), 4);
                }
                break;

            case 'aprmd5': /* 64 characters that are valid for APRMD5 passwords. */
                $APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

                if ($seed)
                {
                    return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
                }
                else
                {
                    $salt = '';

                    for ($i = 0; $i < 8; $i++)
                    {
                        $salt .= $APRMD5{rand(0, 63)};
                    }

                    return $salt;
                }
                break;

            default:
                $salt = '';

                if ($seed)
                {
                    $salt = $seed;
                }

                return $salt;
                break;
        }
    }
    
    
    protected function postLogin()
    {
        $user = $this->getUser();
        App()->user->login($this);

        // Check for default password
        if ($this->password === 'password') {
            $not = new UniqueNotification(array(
                'user_id' => App()->user->id,
                'importance' => Notification::HIGH_IMPORTANCE,
                'title' => 'Password warning',
                'message' => '<span class="fa fa-exclamation-circle text-warning"></span>&nbsp;'.
                    gT("Warning: You are still using the default password ('password'). Please change your password and re-login again.")
            ));
            $not->save();
        }

        if ((int) App()->request->getPost('width', '1220') < 1220) {
// Should be 1280 but allow 60 lenience pixels for browser frame and scrollbar
            Yii::app()->setFlashMessage(gT("Your browser screen size is too small to use the administration properly. The minimum size required is 1280*1024 px."), 'error');
        }

        // Do session setup
        Yii::app()->session['loginID'] = (int) $user->uid;
        Yii::app()->session['user'] = $user->users_name;
        Yii::app()->session['full_name'] = $user->full_name;
        Yii::app()->session['htmleditormode'] = $user->htmleditormode;
        Yii::app()->session['templateeditormode'] = $user->templateeditormode;
        Yii::app()->session['questionselectormode'] = $user->questionselectormode;
        Yii::app()->session['dateformat'] = $user->dateformat;
        Yii::app()->session['session_hash'] = hash('sha256', getGlobalSetting('SessionName').$user->users_name.$user->uid);

        // Perform language settings
        if (App()->request->getPost('loginlang', 'default') != 'default') {
            $user->lang = sanitize_languagecode(App()->request->getPost('loginlang'));
            $user->save();
            $sLanguage = $user->lang;
        } else if ($user->lang == 'auto' || $user->lang == '') {
            $sLanguage = getBrowserLanguage();
        } else {
            $sLanguage = $user->lang;
        }

        Yii::app()->session['adminlang'] = $sLanguage;
        App()->setLanguage($sLanguage);

        // Read all plugin config files if superadmin logged in
        if (Permission::model()->hasGlobalPermission('superadmin')) {
            $pm = Yii::app()->getPluginManager();
            $pm->readConfigFiles();
        }
    }

    public function setPlugin($name)
    {
        $this->plugin = $name;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    // LimeService Mod start =============
    /**
     * Show popup for user experience survey.
     * OpenProject #330.
     *
     * @return void
     */
    protected function showUserExperienceSurveyPopup()
    {
        // Init vars.
        $rpcUrl      = 'https://survey.limesurvey.org/admin/remotecontrol';
        $rpcUser     = 'tmp_api';
        $rpcPassword = 'O432KCB3jzcTq4wV4KtCau9XkhUv8/t6EWaVLGkajHI=';
        $surveyId    = '189495';

        Yii::app()->loadLibrary('jsonRPCClient');

        /** @var JsonRPCClient */
        $rpc = new JsonRPCClient($rpcUrl);

        /** @var string|array */
        $sessionKey = $rpc->get_session_key($rpcUser, $rpcPassword);

        if (is_array($sessionKey)
            && isset($sessionKey['status'])) {
            // Could not get session key for some reason.
            $this->mailError('Could not get session key to survey.limesurvey.org', $sessionKey);
            return;
        }

        $user = Yii::app()->user;

        /** @var int */
        $installationId = (int) getInstallationId();

        /** @var string */
        $uuid = $installationId . '_' . $user->id;

        /** @var array */
        $participant = $rpc->get_participant_properties($sessionKey, $surveyId, ['firstname' => $uuid]);

        if (is_array($participant)
            && isset($participant['status'])) {
            // Found no participant, add a new one.
            /** @var array */
            $participantData = [
                [
                    "firstname" => $uuid
                ]
            ];

            $result = $rpc->add_participants($sessionKey, $surveyId, $participantData, true);

            if (is_array($result)
                && isset($result['status'])) {
                // FAIL: Could not add participant.
                $this->mailError('Could not add participant', $result);
                return;
            }

            $participant = $rpc->get_participant_properties($sessionKey, $surveyId, ['firstname' => $uuid]);

            if (is_array($participant)
                && isset($participant['status'])) {
                // FAIL: Could not get participant.
                $this->mailError('Could not get participant', $participant);
                return;
            }
        }

        if ($participant['completed'] === 'Y'
            || $participant['emailstatus'] === 'OptOut') {
            // Remove notification, or do nothing.
        } else {
            $optoutUrl  = 'https://survey.limesurvey.org/index.php/optout/participants?surveyid='
                . $surveyId . '&token='
                . $participant['token'];
            $token = $participant['token'];
            $not = new UniqueNotification(
                [
                    'user_id'    => $user->id,
                    'importance' => Notification::HIGH_IMPORTANCE,
                    'markAsNew'  => true,
                    'title'      => 'Leave feedback on LimeSurvey',
                    'message'    => <<<HTML
<p>We're right now gathering feedback about our program. We want you to share your opinions and feedback about your usage. The survey will take around 10 minutes to complete.</p>
<a href="https://survey.limesurvey.org/189495?lang=en&token=$token" target="_blank" class="btn btn-default"><i class="fa fa-external-link"></i>&nbsp;Participate</a>&nbsp;
<button href="$optoutUrl" target="_blank" class="btn btn-default">Don't participate</button>&nbsp;
<button class="btn btn-default" data-dismiss="modal">Maybe later</button>
HTML
                ]
            );
            $not->save();
        }

        $rpc->release_session_key($sessionKey);

/*
        // TODO: Link should include token
        // TODO: RPC integration to check if survey is already finished by this user.
        // Load token for this user, use email as uuid.
        // If not exist, create it
        $optoutUrl  = 'https://survey.limesurvey.org/index.php/optout/participants?surveyid=189495&token=123';
        $not = new UniqueNotification(array(
            'user_id' => App()->user->id,
            'importance' => Notification::HIGH_IMPORTANCE,
            'markAsNew' => true,
            'title' => 'Leave feedback on LimeSurvey',
            'message' => <<<HTML
<p>We're right now gathering feedback about our program. We want you to share your opinions and feedback about your usage. The survey will take around 10 minutes to complete.</p>
<a href="https://survey.limesurvey.org/189495?lang=en" target="_blank" class="btn btn-default"><i class="fa fa-external-link"></i>&nbsp;Participate</a>&nbsp;
<a href="$optoutUrl" target="_blank" class="btn btn-default">Don't participate</a>&nbsp;
<button class="btn btn-default" data-dismiss="modal">Maybe later</button>
HTML
        ));
        $not->save();
 */
    }

    /**
     * @param string $header
     * @param mixed $var
     * @return void
     */
    protected function mailError($header, $var = 'no data')
    {
        mail(
            'alert@limesurvey.org',
            '[Usability survey] ' . $header,
            json_encode($var) . PHP_EOL
            . print_r($_SERVER, true)
        );
    }
    // LimeService Mod end =============
}
