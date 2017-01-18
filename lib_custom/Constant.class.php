<?php
class Constant {
    //session
    const DB_ENV_KEY = 'db_env_key';
    const APPLICATION_INI_CONFIG = 'application_ini_config';
    //env
    const ENV_LOCAL = 'LOCAL';
    const ENV_REMOTE = 'REMOTE';
    const ENV_LOCAL_OLD_PC = 'LOCAL_OLD_PC';
    
    //db ini keys
    const DB_KEY_ENV_LOCAL = 'db_local';
    const DB_KEY_ENV_REMOTE = 'db_remote';
    const DB_KEY_ENV_LOCAL_OLD_PC = 'db_remote_old_pc'; 
    
    //db
    const TABLE_PROVIDER = 'users_tuto_provider';
    const TABLE_QUESTION = 'qcms_question';
    const TABLE_RESPONSE = 'qcms_response_correction';
}
/**
$db_key_ini
$application_ini[$db_key_ini]
*/

