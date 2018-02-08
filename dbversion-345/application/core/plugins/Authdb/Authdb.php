<?php
class Authdb extends AuthPluginBase
{
    protected $storage = 'DbStorage';
    protected $_onepass = null;

    static protected $description = 'Core: Database authentication + exports';
    static protected $name = 'LimeSurvey internal database';

    public function init()
    {
        /**
         * Here you should handle subscribing to the events your plugin will handle
         */
        $this->subscribe('createNewUser');
        $this->subscribe('beforeLogin');
        $this->subscribe('newLoginForm');
        $this->subscribe('afterLoginFormSubmit');
        $this->subscribe('remoteControlLogin');

        $this->subscribe('newUserSession');
        $this->subscribe('beforeDeactivate');
        // Now register for the core exports
        $this->subscribe('listExportPlugins');
        $this->subscribe('listExportOptions');
        $this->subscribe('newExport');
    }

    /**
     * Create a DB user
     *
     * @return void
     */
    public function createNewUser()
    {
        // Do nothing if the user to be added is not DB type
        if (flattenText(Yii::app()->request->getPost('user_type')) != 'DB') {
            return;
        }
        $oEvent = $this->getEvent();
        $new_user = flattenText(Yii::app()->request->getPost('new_user'), false, true);
        $new_email = flattenText(Yii::app()->request->getPost('new_email'), false, true);
        if (!validateEmailAddress($new_email)) {
            $oEvent->set('errorCode', self::ERROR_INVALID_EMAIL);
            $oEvent->set('errorMessageTitle', gT("Failed to add user"));
            $oEvent->set('errorMessageBody', gT("The email address is not valid."));
            return;
        }
        $new_full_name = flattenText(Yii::app()->request->getPost('new_full_name'), false, true);
        $new_pass = createPassword();
        $iNewUID = User::model()->insertUser($new_user, $new_pass, $new_full_name, Yii::app()->session['loginID'], $new_email);
        if (!$iNewUID) {
            $oEvent->set('errorCode', self::ERROR_ALREADY_EXISTING_USER);
            $oEvent->set('errorMessageTitle', '');
            $oEvent->set('errorMessageBody', gT("Failed to add user"));
            return;
        }

        Permission::model()->setGlobalPermission($iNewUID, 'auth_db');

        $oEvent->set('newUserID', $iNewUID);
        $oEvent->set('newPassword', $new_pass);
        $oEvent->set('newEmail', $new_email);
        $oEvent->set('newFullName', $new_full_name);
        $oEvent->set('errorCode', self::ERROR_NONE);
    }

    public function beforeDeactivate()
    {
        $this->getEvent()->set('success', false);

        // Optionally set a custom error message.
        $this->getEvent()->set('message', gT('Core plugin can not be disabled.'));
    }

    public function beforeLogin()
    {
        // We can skip the login form here and set username/password etc.
        $request = $this->api->getRequest();
        if (!is_null($request->getParam('onepass'))) {
            // We have a one time password, skip the login form
            $this->setOnePass($request->getParam('onepass'));
            $this->setUsername($request->getParam('user'));
            $this->setAuthPlugin(); // This plugin will handle authentication and skips the login form
        }
    }

    /**
     * Get the onetime password (if set)
     *
     * @return string|null
     */
    protected function getOnePass()
    {
        return $this->_onepass;
    }

    public function newLoginForm()
    {
        $sUserName = '';
        $sPassword = '';
        if (Yii::app()->getConfig("demoMode") === true && Yii::app()->getConfig("demoModePrefill") === true) {
            $sUserName = Yii::app()->getConfig("defaultuser");
            $sPassword = Yii::app()->getConfig("defaultpass");
        }

        $this->getEvent()->getContent($this)
                ->addContent(CHtml::tag('span', array(), "<label for='user'>".gT("Username")."</label>".CHtml::textField('user', $sUserName, array('size'=>40, 'maxlength'=>40, 'class'=>"form-control"))))
                ->addContent(CHtml::tag('span', array(), "<label for='password'>".gT("Password")."</label>".CHtml::passwordField('password', $sPassword, array('size'=>40, 'maxlength'=>40, 'class'=>"form-control"))));
    }

    public function newUserSession()
    {
        // Do nothing if this user is not Authdb type
        $identity = $this->getEvent()->get('identity');

        if ($identity->plugin != 'Authdb')
        {
            return;
        }

        // Here we do the actual authentication
        $username = $this->getUsername();
        $password = $this->getPassword();
        $onepass  = $this->getOnePass();

        $user = $this->api->getUserByName($username);

        if ($user == null){
          $user = $this->api->getUserByEmail($username);

          if (is_object($user)){
              $this->setUsername($user->users_name);
          }
        }

        if ($user !== null && $user->uid != 1 && !Permission::model()->hasGlobalPermission('auth_db','read',$user->uid))
        {
            $this->setAuthFailure(self::ERROR_AUTH_METHOD_INVALID, gT('Internal database authentication method is not allowed for this user'));
            return;
        }
        if ($user !== null and ($username==$user->users_name || $username==$user->email)) // Control of equality for uppercase/lowercase with mysql
        {
            if (gettype($user->password)=='resource')
            {
                $sStoredPassword=stream_get_contents($user->password,-1,0);  // Postgres delivers bytea fields as streams :-o
            }
            else
            {
                $sStoredPassword=$user->password;
            }
        }
        else
        {
            $this->setAuthFailure(self::ERROR_USERNAME_INVALID);
            return;
        }

        if ($onepass != '' && $this->api->getConfigKey('use_one_time_passwords') && md5($onepass) == $user->one_time_pw)
        {
            $user->one_time_pw='';
            $user->save();
            $this->setAuthSuccess($user);
            return;
        }

        if ($sStoredPassword !== hash('sha256', $password))
        {
            // Maybe it's a joomla hash
            if(!$this->joomla_verifyPassword($password, $sStoredPassword)){
                $this->setAuthFailure(self::ERROR_PASSWORD_INVALID);
                return;
            }
        }

        $this->setAuthSuccess($user);
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

    /**
     * Set the onetime password
     *
     * @param string $onepass
     * @return Authdb
     */
    protected function setOnePass($onepass)
    {
        $this->_onepass = $onepass;

        return $this;
    }


    // Now the export part:
    public function listExportOptions()
    {
        $event = $this->getEvent();
        $type = $event->get('type');

        switch ($type) {
            case 'csv':
                $event->set('label', gT("CSV"));
                $event->set('default', true);
                break;
            case 'xls':
                $label = gT("Microsoft Excel");
                if (!function_exists('iconv')) {
                    $label .= '<font class="warningtitle">'.gT("(Iconv Library not installed)").'</font>';
                }
                $event->set('label', $label);
                break;
            case 'doc':
                $event->set('label', gT("Microsoft Word"));
                $event->set('onclick', 'document.getElementById("answers-long").checked=true;document.getElementById("answers-short").disabled=true;');
                break;
            case 'pdf':
                $event->set('label', gT("PDF"));
                break;
            case 'html':
                $event->set('label', gT("HTML"));
                break;
            case 'json':    // Not in the interface, only for RPC
            default:
                break;
        }
    }

    /**
     * Registers this export type
     */
    public function listExportPlugins()
    {
        $event = $this->getEvent();
        $exports = $event->get('exportplugins');

        // Yes we overwrite existing classes if available
        $className = get_class();
        $exports['csv'] = $className;
        $exports['xls'] = $className;
        $exports['pdf'] = $className;
        $exports['html'] = $className;
        $exports['json'] = $className;
        $exports['doc'] = $className;

        $event->set('exportplugins', $exports);
    }

    /**
     * Returns the required IWriter
     */
    public function newExport()
    {
        $event = $this->getEvent();
        $type = $event->get('type');

        switch ($type) {
            case "doc":
                $writer = new DocWriter();
                break;
            case "xls":
                $writer = new ExcelWriter();
                break;
            case "pdf":
                $writer = new PdfWriter();
                break;
            case "html":
                $writer = new HtmlWriter();
                break;
            case "json":
                $writer = new JsonWriter();
                break;
            case "csv":
            default:
                $writer = new CsvWriter();
                break;
        }

        $event->set('writer', $writer);
    }
}
