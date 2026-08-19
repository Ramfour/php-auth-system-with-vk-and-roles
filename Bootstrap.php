<?php
/**
 * Application bootstrap
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

// Make classes available
use App\Auth;
use App\CsrfToken;
use App\Database;
use App\AuthLogger;
use App\Helper;
