<?php

namespace App\Controllers;


use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

//! Custom
require __DIR__ . '/../Libraries/MongoDBLibs/vendor/autoload.php';

use App\Libraries\JWT\JWTUtils;
//! ./Custom
/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{

    //! Custom
    protected $jwtUtils;
    // protected $mongo;
    // protected $db1;
    // public function __construct()
    // {
    //     $this->jwtUtils = new JWTUtils();
    //     $this->mongo = new \MongoDB\Client("mongodb://test:test@172.16.1.125:27017/?authSource=IIoT_B12_Piping");
    //     $this->db1 = $this->mongo->selectDatabase("IIoT_B12_Piping");
    // }
    //! ./Custom

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        //! Custom
        $this->jwtUtils = new JWTUtils();
        //! ./Custom
    }

    //! Custom
    public function MongoDBUTCDateTime(int $time)
    {
        return new \MongoDB\BSON\UTCDateTime($time);
    }

    public function MongoDBObjectId(string $objID)
    {
        return new \MongoDB\BSON\ObjectId($objID);
    }

    public function MongoDBRegex(string $reg)
    {
        return new \MongoDB\BSON\Regex($reg);
    }
    //! ./Custom
}
