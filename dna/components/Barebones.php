<?php
/**
 * Helper classes to perform certain operations in barebones php withour requiring Yii
 */

namespace barebones;

use FluentPDO;
use PDO;
use Exception;
use ItemTypes;

/**
 * Helper class to perform some operations with barebones php
 *
 * Class Barebones
 */
class Barebones
{

    static public $pdoInstance;

    static public function pdo()
    {
        if (empty(static::$pdoInstance)) {
            static::$pdoInstance = new PDO(
                'mysql:host=' . DATABASE_HOST . ';port=' . DATABASE_PORT . ';dbname=' . DATABASE_NAME,
                DATABASE_USER,
                DATABASE_PASSWORD,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        }
        return static::$pdoInstance;
    }

    static public $fpdo;

    static public function fpdo()
    {
        if (empty(static::$fpdo)) {
            static::$fpdo = new FluentPDO(Barebones::pdo());
            static::$fpdo->errorHandler = function (\BaseQuery $query, \PDO $result) {
                $msg = "\n";
                $msg .= "ERROR:\n";
                $msg .= print_r($query->getQuery(), true);
                $msg .= print_r($result->errorInfo(), true);
                $msg .= "\n";
                error_log($msg);
                if (YII_DEBUG && DEBUG_LOGS) {
                    echo $msg . "\n";
                }
            };
            if (YII_DEBUG && DEBUG_LOGS) {
                static::$fpdo->debug = function ($query) {
                    $msg = "\n";
                    $msg .= "QUERY:\n";
                    $msg .= print_r($query->getQuery(), true);
                    $msg .= "\n";
                    $msg .= "\n";
                    $msg .= "PARAMETERS:\n";
                    $msg .= print_r($query->getParameters(), true);
                    $msg .= "\n";
                    error_log($msg);
                    echo $msg . "\n";
                };
            }

        }
        return static::$fpdo;
    }

    const PUBLIC_GROUP_ID = 1;
    const PUBLIC_VISIBILITY = 'visible';

    public static function restrictQueryToPublishedItems(&$query)
    {

        if (!in_array($query->getFromTable(), ItemTypes::where('is_access_restricted'))) {
            return;
        }

        $query->leftJoin('`node_has_group` AS `nhg_public` ON (' . $query->getFromAlias() . '.`node_id` = `nhg_public`.`node_id`' .
            ' AND `nhg_public`.`group_id` = ' . intval(static::PUBLIC_GROUP_ID) .
            ' AND `nhg_public`.`visibility` = \'' . static::PUBLIC_VISIBILITY . '\')'
        )->where('nhg_public.id IS NOT NULL');

    }

    /**
     * Create a url to the image rendered with the given preset.
     * Performance version of the P3Media::createUrl() method.
     *
     * @param int $mediaId
     * @param string|null $preset
     * @param boolean $absolute
     * @return string
     */
    public static function createMediaUrl($mediaId, $preset = null)
    {
        // Access to raw app config included in www/index.php
        global $config;
        $presets = $config['modules']['p3media']['params']['presets'];
        if (isset($presets[$preset]['type'])) {
            $type = $presets[$preset]['type'];
        } else {
            $result = static::fpdo()->from('p3_media')->select('mime_type')->where('id=:id', array(':id' => $mediaId))->fetch();
            $mimeType = $result['mime_type'];
            $type = !empty($mimeType) ? substr(strrchr($mimeType, '/'), 1) : 'none';
        }
        return static::createAbsoluteUrl(
            '/p3media/file/image',
            array(
                'id' => $mediaId,
                'preset' => $preset,
                'title' => 'media',
                'extension' => '.' . $type
            ),
            '/files-api'
        );
    }

    public static function createAbsoluteUrl($route, $params = [], $basePath = "/api")
    {
        return Yii::app()->request->baseUrl
        . $basePath
        . $route
        . (!empty($params) ? '?' . http_build_query($params) : "")
        . "";
    }
}

class SentryErrorHandling
{

    public static function activate($sentryDsn)
    {

        // Init sentry
        $sentryClient = new \Raven_Client($sentryDsn);
        $errorHandler = new \Raven_ErrorHandler($sentryClient);

        // Register error handler callbacks
        set_error_handler(array($errorHandler, 'handleError'));
        set_exception_handler(array($errorHandler, 'handleException'));

        // Handle fatal errors
        if (false) register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null) {

                // The information that we show to the end-user
                $publicInfo = array(
                    'code' => 500,
                );

                // Set the error as public when in debug mode
                if (YII_DEBUG) {
                    $publicInfo["error"] = $error;
                }

                // Error has already been reported by sentry - redirect to error-page instead of letting the user stare
                // at a white screen of death
                $errorQs = http_build_query($publicInfo);

                $isFatal = ($error["type"] == E_ERROR);

                // todo: fix hard-coded path
                if (strpos(Yii::app()->request->requestUri, "api/v1/error") === false) {

                    $url = Yii::app()->request->baseUrl . "/api/v1/error?$errorQs";
                    if (!headers_sent($filename, $linenum)) {
                        header("Location: $url");
                        exit;
                    } else {
                        throw new Exception("Shutdown handler error redirect to $url failed since headers were sent in $filename on line $linenum. Error: " . print_r($error, true));
                        exit;
                    }

                } else {
                    // Error when loading site/error - we can't do much but throw an exception about the error
                    throw new Exception("Error when loading site/error: " . print_r($error, true));
                }
            }
        });
        // Necessary in order for locations to work
        ini_set('display_errors', false);


    }
}

/*
class Command
{
    public $fpdo;

    public function __construct()
    {
        $this->fpdo = new FluentPDO(Barebones::pdo());
    }

    public function select($arg)
    {
        throw new Exception('It is necessary to start query-building with from()');
    }
}
*/

class Db
{
    public $command;

    public function createCommand()
    {
        if (empty($this->command)) {
            $this->command = new FluentPDO(Barebones::pdo());
        }
        return $this->command;
    }

}

class App
{

    public $language = "en";

    public $db;

    public $request;

    public function __construct()
    {
        $baseUrl = ($_SERVER['HTTPS'] === 'off' ? 'http' : 'https')
            . "://"
            . $_SERVER['HTTP_HOST'];

        $this->request = (object) [
            "requestUri" => $_SERVER['REQUEST_URI'],
            "baseUrl" => $baseUrl,
        ];
    }

    public function getDb()
    {
        if (empty($this->db)) {
            $this->db = new Db();
        }
        return $this->db;
    }

}

class Yii
{

    static public function app()
    {
        static $app;
        if (empty($app)) {
            $app = new App();
        }
        return $app;
    }

    /**
     * Currently simply returns the original string
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $source
     * @param null $language
     * @return mixed
     */
    public static function t($category, $message, $params = array(), $source = null, $language = null)
    {
        return $message;
    }

}

class ActiveRecord
{
    public $attributes = [];
    public $__table;

    function __get($name)
    {
        if (isset($this->$name)) {
            return parent::__get($name);
        }
        $relation = "relation" . ucfirst($name);
        $orgName = $name;
        // TODO: Implement fetching of translations
        if (!array_key_exists($name, $this->attributes)) {
            $name = "_" . $orgName;
        }
        if (!array_key_exists($name, $this->attributes)) {
            $name = $orgName . "_" . Yii::app()->language;
        }
        if (method_exists($this, $relation)) {
            return $this->$relation();
        }
        if (!array_key_exists($name, $this->attributes)) {
            //var_dump($orgName, $relation, method_exists($this, $relation), $this->attributes);
            //try {throw new \Exception("backtrace");} catch (\Exception $e) {echo $e->getTraceAsString();}
            throw new Exception("Could not find attribute '$orgName' in " . get_class($this));
        }
        return $this->attributes[$name];
    }

    function __set($name, $value)
    {
        if (isset($this->$name)) {
            return parent::__set($name, $value);
        }
        $this->attributes[$name] = $value;
    }

    function findByPk($id)
    {
        $models = $this->findAll("id = ?", $id, 1);
        if (empty($models)) {
            return null;
        }
        return $models[0];
    }

    function findAll($where, $params = [], $limit = 100)
    {

        $command = Barebones::fpdo()
            ->from("`{$this->__table}`")
            ->where($where, $params)
            ->limit($limit);

        Barebones::restrictQueryToPublishedItems($command);

        $rows = $command->fetchAll();

        $models = [];
        foreach ($rows as $row) {
            $model = static::model();
            $model->attributes = $row;
            $models[] = $model;
        }

        return $models;
    }

    static public function model($class = null)
    {
        if (empty($class)) {
            $class = get_called_class();
        }
        return new $class;
    }

    public function tableName()
    {
        return $this->__table;
    }

}

class CHttpException extends Exception
{
    /**
     * @var integer HTTP status code, such as 403, 404, 500, etc.
     */
    public $statusCode;

    /**
     * Constructor.
     * @param integer $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param integer $code error code
     */
    public function __construct($status, $message = null, $code = 0)
    {
        $this->statusCode = $status;
        parent::__construct($message, $code);
    }
}

class CApplicationComponent
{

}