<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
     *
     * URL to your CodeIgniter root. Typically this will be your base URL,
     * WITH a trailing slash:
     *
     *    http://example.com/
     *
     * If this is not set then CodeIgniter will try guess the protocol, domain
     * and path to your installation. However, you should always configure this
     * explicitly and never rely on auto-guessing, especially in production
     * environments.
     */
    public $baseURL = 'http://localhost/quiz_app/';

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
     *
     * Typically this will be your index.php file, unless you've renamed it to
     * something else. If you are using mod_rewrite to remove the page set this
     * variable so that it is blank.
     */
    public $indexPage = 'index.php';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * This item determines which server global should be used to retrieve the
     * URI string.  The default setting of 'REQUEST_URI' works for most servers.
     * If your links do not seem to work, try one of the other delicious flavors:
     *
     * 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
     * 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
     * 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
     */
    public $uriProtocol = 'REQUEST_URI';

    /**
     * --------------------------------------------------------------------------
     * Default Locale
     * --------------------------------------------------------------------------
     *
     * The Locale roughly represents the language and location that your visitor
     * is viewing the site from. It affects the language strings and other
     * strings (like currency markers, numbers, etc), that your program
     * should run under for this request.
     */
    public $defaultLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Negotiate Locale
     * --------------------------------------------------------------------------
     *
     * If true, the current Request object will automatically determine the
     * language to use based on the value of the Accept-Language header.
     *
     * If false, no automatic detection will be performed.
     */
    public $negotiateLocale = false;

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     *
     * If $negotiateLocale is true, this array lists the locales supported
     * by the application in descending order of priority. If no match is
     * found, the first locale will be used.
     */
    public $supportedLocales = ['en'];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     *
     * The default timezone that will be used in your application to display
     * dates with the date helper, and can be retrieved through app_timezone()
     */
    public $appTimezone = 'UTC';

    /**
     * --------------------------------------------------------------------------
     * Default Character Set
     * --------------------------------------------------------------------------
     *
     * This determines which character set is used by default in various methods
     * that require a character set to be provided.
     */
    public $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * If true, this will force every request made to this application to be
     * made via a secure connection (HTTPS). If the incoming request is not
     * secure, the user will be redirected to a secure version of the page
     * and the HTTP Strict Transport Security header will be set.
     */
    public $forceGlobalSecureRequests = false;

    /**
     * --------------------------------------------------------------------------
     * Session Variables
     * --------------------------------------------------------------------------
     *
     * 'sessionDriver'
     *
     * The storage driver to use: files, database, redis, memcached
     *
     * 'sessionCookieName'
     *
     * The session cookie name, must contain only [0-9a-z_-] characters
     *
     * 'sessionExpiration'
     *
     * The number of SECONDS you want the session to last.
     * Setting to 0 (zero) means expire when the browser is closed.
     *
     * 'sessionSavePath'
     *
     * The location to save sessions to, driver dependent.
     *
     * For the 'files' driver, it's a path to a writable directory.
     * WARNING: Only absolute paths are supported!
     *
     * For the 'database' driver, it's a table name.
     * Please read up the manual for the format with other session drivers.
     *
     * IMPORTANT: You are REQUIRED to set a valid save path!
     *
     * 'sessionMatchIP'
     *
     * Whether to match the user's IP address when reading the session data.
     *
     * WARNING: If you're using the database driver, don't forget to update
     *          your session table's PRIMARY KEY when changing this setting.
     *
     * 'sessionTimeToUpdate'
     *
     * How many seconds between CI regenerating the session ID.
     *
     * 'sessionRegenerateDestroy'
     *
     * Whether to destroy session data associated with the old session ID
     * when auto-regenerating the session ID. When set to FALSE, the data
     * will be later deleted by the garbage collector.
     *
     * 'cookiePrefix'
     *
     * Set to a string to add that to the front of all cookies created.
     *
     * 'cookieDomain'
     *
     * Set to a string to indicate the domain for cookies created.
     *
     * 'cookiePath'
     *
     * Path for cookies created. Default is /
     *
     * 'cookieSecure'
     *
     * Cookie will only be set if a secure HTTPS connection exists.
     *
     * 'cookieHTTPOnly'
     *
     * Cookie will only be accessible via HTTP(S) (no javascript)
     *
     * 'cookieSameSite'
     *
     * Configure cookie SameSite setting. Valid values are:
     * - None
     * - Lax
     * - Strict
     * - ''
     *
     * Defaults to Lax for compatibility with modern browsers. Setting to
     * None is required when using cross-domain cookies.
     */
    public $sessionDriver            = 'files';
    public $sessionCookieName        = 'ci_session';
    public $sessionExpiration        = 7200;
    public $sessionSavePath          = WRITEPATH . 'session';
    public $sessionMatchIP           = false;
    public $sessionTimeToUpdate      = 300;
    public $sessionRegenerateDestroy = false;

    public $cookiePrefix   = '';
    public $cookieDomain   = '';
    public $cookiePath     = '/';
    public $cookieSecure   = false;
    public $cookieHTTPOnly = false;
    public $cookieSameSite = 'Lax';

    /**
     * --------------------------------------------------------------------------
     * CSRF Protection
     * --------------------------------------------------------------------------
     *
     * CSRF Protection is enabled by default for security purposes.
     * You may want to disable this if your controller architectures
     * don't use a token with each request.
     */
    public $CSRFProtection = true;

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Name
     * --------------------------------------------------------------------------
     *
     * The token name that is used for CSRF protection.
     */
    public $CSRFTokenName = 'csrf_test_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Header Name
     * --------------------------------------------------------------------------
     *
     * The header name that is used for CSRF protection.
     */
    public $CSRFHeaderName = 'X-CSRF-TOKEN';

    /**
     * --------------------------------------------------------------------------
     * CSRF Cookie Name
     * --------------------------------------------------------------------------
     *
     * The cookie name that is used for CSRF protection.
     */
    public $CSRFCookieName = 'csrf_cookie_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Expire
     * --------------------------------------------------------------------------
     *
     * The number in seconds the token should expire.
     */
    public $CSRFExpire = 7200;

    /**
     * --------------------------------------------------------------------------
     * CSRF Regenerate
     * --------------------------------------------------------------------------
     *
     * Regenerate token on every submission
     */
    public $CSRFRegenerate = true;

    /**
     * --------------------------------------------------------------------------
     * CSRF Redirect
     * --------------------------------------------------------------------------
     *
     * Redirect to previous page when the token does not match
     */
    public $CSRFRedirect = true;

    /**
     * --------------------------------------------------------------------------
     * Error Logging Threshold
     * --------------------------------------------------------------------------
     *
     * You can enable error logging by setting a threshold over zero. The
     * threshold determines what gets logged. Threshold options are:
     *
     * 0 = Disables logging, Error logging TURNED OFF
     * 1 = Emergency Messages - System is unusable
     * 2 = Alert Messages - Action Must Be Taken Immediately
     * 3 = Critical Messages - Application component unavailable, unexpected exception.
     * 4 = Runtime Errors - Don't need immediate action, but should be monitored.
     * 5 = Notices - Informational, recommended for debugging only.
     * 6 = Info - Informational messages for monitoring.
     * 7 = Debug - Debugging information, more verbose.
     * 8 = Trace - Very detailed executions steps, stack traces.
     * 9 = All - All of the above
     */
    public $threshold = 4;

    /**
     * --------------------------------------------------------------------------
     * Composer auto-loading
     * --------------------------------------------------------------------------
     *
     * If you use Composer, uncomment the line below to use its autoloader
     * for this website. This is useful for packages that use namespaces.
     *
     * Note: If you use custom namespaces in your classes, you'll need to expand
     * this to include them as well.
     */
    public $composerAutoload = ROOTPATH . 'vendor/autoload.php';
}
