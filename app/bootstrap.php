<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPtricks\Orm\Database;
use Noodlehaus\Config;
use Illuminate\Database\Capsule\Manager as Capsule;



// define base route & path
define('INC_ROOT',dirname(__DIR__));
define('BASEPATH',dirname(__DIR__));

// Define time 
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
define( 'MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS    );
define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );


// Load the libraries
require INC_ROOT.'/vendor/autoload.php';

$config = ['settings' => [ 'determineRouteBeforeAppMiddleware' => true , 'displayErrorDetails' => true,  'log.enable' => true ,     'addContentLengthHeader' => false,
], ];

// initialize the slim
$app = new \Slim\App($config);

// Call files
require INC_ROOT.'/app/helpers.php';

// start slim container
$container = $app->getContainer();

// Starter
$starter = new \App\Starter($container);

require INC_ROOT.'/app/functions.php';
require_once BASEPATH.'/app/helpers.php';

// call routes
require INC_ROOT.'/app/Routes/admin.php';



