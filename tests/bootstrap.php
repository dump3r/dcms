<?php

    // create DCMS_SECURE
    define('DCMS_SECURE', true);
    
    // set DCMS_CALL
    define('DCMS_CALL', 'tests');
    
    // load the system constants
    require_once dirname(__FILE__).'/../constants.php';
    
    // load the system core
    require_once DCMS_FRAMEWORK_DIR.'/core/Core.php';
    
    // load the core mock
    \dcms\Core::load_file('CoreMock', 'tests/mocks/framework/core', true, true);