<?php
/*
* LimeSurvey
* Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
* All rights reserved.
* License: GNU/GPL License v2 or later, see LICENSE.php
* LimeSurvey is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*
*/

class UserIdentity extends CUserIdentity
{
    protected $id;
    protected $user;
    protected $sOneTimePassword;

    /**
    * Checks whether this user has correctly entered password or not
    *
    * @access public
    * @return bool
    */
    public function authenticate($sOneTimePassword='')
    {
        if (Yii::app()->getConfig("auth_webserver")==false || $this->username != "")
        {
            $user = User::model()->findByAttributes(array('users_name' => $this->username));

            if ($user !== null)
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
                $this->errorCode = self::ERROR_USERNAME_INVALID;
                return !$this->errorCode;
            }

            if ($sOneTimePassword!='' && Yii::app()->getConfig("use_one_time_passwords") && md5($sOneTimePassword)==$user->one_time_pw)
            {
                $user->one_time_pw='';
                $user->save();
                $this->id = $user->uid;
                $this->user = $user;
                $this->errorCode = self::ERROR_NONE;
            }
            elseif ($sStoredPassword !== hash('sha256', $this->password))
            {
                // Maybe it's a joomla hash
                if(!$this->joomla_verifyPassword($this->password, $sStoredPassword)){
                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                }else{
                    $this->id = $user->uid;
                    $this->user = $user;
                    $this->errorCode = self::ERROR_NONE;
                }
            }
            else
            {
                $this->id = $user->uid;
                $this->user = $user;
                $this->errorCode = self::ERROR_NONE;
            }
        }
        elseif(Yii::app()->getConfig("auth_webserver") === true && (isset($_SERVER['PHP_AUTH_USER'])||isset($_SERVER['LOGON_USER']) ||isset($_SERVER['REMOTE_USER']))) // normal login through webserver authentication
        {
            if (isset($_SERVER['PHP_AUTH_USER'])) {
                $sUser=$_SERVER['PHP_AUTH_USER'];
            }
            elseif (isset($_SERVER['REMOTE_USER'])) {
                $sUser=$_SERVER['REMOTE_USER'];
            } else {
                $sUser = $_SERVER['LOGON_USER'];
            }
            if (strpos($sUser,"\\")!==false) {
                $sUser = substr($sUser, strrpos($sUser, "\\")+1);
            }

            $aUserMappings=Yii::app()->getConfig("auth_webserver_user_map");
            if (isset($aUserMappings[$sUser]))
            {
               $sUser = $aUserMappings[$sUser];
            }
            $this->username = $sUser;

            $oUser=User::model()->findByAttributes(array('users_name'=>$sUser));
            if (is_null($oUser))
            {
                if (function_exists("hook_get_auth_webserver_profile"))
                {
                    // If defined this function returns an array
                    // describing the defaukt profile for this user
                    $aUserProfile = hook_get_auth_webserver_profile($sUser);
                }
                elseif (Yii::app()->getConfig("auth_webserver_autocreate_user"))
                {
                    $aUserProfile=Yii::app()->getConfig("auth_webserver_autocreate_profile");
                }
            } else {
                $this->id = $oUser->uid;
                $this->user = $oUser;
                $this->errorCode = self::ERROR_NONE;
            }



            if (Yii::app()->getConfig("auth_webserver_autocreate_user") && isset($aUserProfile) && is_null($oUser))
            { // user doesn't exist but auto-create user is set
                $oUser=new User;
                $oUser->users_name=$sUser;
                $oUser->password=hash('sha256', createPassword());
                $oUser->full_name=$aUserProfile['full_name'];
                $oUser->parent_id=1;
                $oUser->lang=$aUserProfile['lang'];
                $oUser->email=$aUserProfile['email'];
                $oUser->create_survey=$aUserProfile['create_survey'];
                $oUser->create_user=$aUserProfile['create_user'];
                $oUser->delete_user=$aUserProfile['delete_user'];
                $oUser->superadmin=$aUserProfile['superadmin'];
                $oUser->configurator=$aUserProfile['configurator'];
                $oUser->manage_template=$aUserProfile['manage_template'];
                $oUser->manage_label=$aUserProfile['manage_label'];

                if ($oUser->save())
                {
                    $aTemplates=explode(",",$aUserProfile['templatelist']);
                    foreach ($aTemplates as $sTemplateName)
                    {
                        $oPermission=new Permission;
                        $oPermission->uid = $oUser->uid;
                        $oPermission->entity = 'template';
                        $oPermission->permission = trim($sTemplateName);
                        $oPermission->read_p = 1;
                        $oPermission->save();
                    }

                    // read again user from newly created entry
                    $this->id = $oUser->uid;
                    $this->user = $oUser;
                    $this->errorCode = self::ERROR_NONE;
                }
                else
                {
                    $this->errorCode = self::ERROR_USERNAME_INVALID;
                }

            }
        }
        else
        {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }
        return !$this->errorCode;
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
    * @return CActiveRecord
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
}
