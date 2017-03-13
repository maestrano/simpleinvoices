<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e23f4576a97373eba11afdf1428554e
{
    public static $classMap = array (
        'Maestrano' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Maestrano.php',
        'Maestrano_Account_Bill' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Account/Bill.php',
        'Maestrano_Account_Group' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Account/Group.php',
        'Maestrano_Account_RecurringBill' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Account/RecurringBill.php',
        'Maestrano_Account_Reseller' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Account/Reseller.php',
        'Maestrano_Account_User' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Account/User.php',
        'Maestrano_Api_AuthenticationError' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/AuthenticationError.php',
        'Maestrano_Api_ConnectionError' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/ConnectionError.php',
        'Maestrano_Api_Error' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/Error.php',
        'Maestrano_Api_InvalidRequestError' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/InvalidRequestError.php',
        'Maestrano_Api_Object' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/Object.php',
        'Maestrano_Api_Requestor' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/Requestor.php',
        'Maestrano_Api_Resource' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/Resource.php',
        'Maestrano_Api_Util' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/Util.php',
        'Maestrano_AttachedObject' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Api/AttachedObject.php',
        'Maestrano_Connec_Client' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Connec/Client.php',
        'Maestrano_Helper_DateTime' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Helper/DateTime.php',
        'Maestrano_Net_HttpClient' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Net/HttpClient.php',
        'Maestrano_Saml_Request' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Saml/Request.php',
        'Maestrano_Saml_Response' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Saml/Response.php',
        'Maestrano_Saml_Settings' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Saml/Settings.php',
        'Maestrano_Saml_XmlSec' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Saml/XmlSec.php',
        'Maestrano_Sso_Group' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Sso/Group.php',
        'Maestrano_Sso_Service' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Sso/Service.php',
        'Maestrano_Sso_Session' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Sso/Session.php',
        'Maestrano_Sso_User' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Sso/User.php',
        'Maestrano_Util_PresetObject' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Util/PresetObject.php',
        'Maestrano_Util_PresetProxy' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Util/PresetProxy.php',
        'Maestrano_Util_Set' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Util/Set.php',
        'XMLSecEnc' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Xmlseclibs/xmlseclibs.php',
        'XMLSecurityDSig' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Xmlseclibs/xmlseclibs.php',
        'XMLSecurityKey' => __DIR__ . '/..' . '/maestrano/maestrano-php/lib/Maestrano/Xmlseclibs/xmlseclibs.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit7e23f4576a97373eba11afdf1428554e::$classMap;

        }, null, ClassLoader::class);
    }
}
