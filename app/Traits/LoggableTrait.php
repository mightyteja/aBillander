<?php 

namespace App\Traits;

trait LoggableTrait
{
    // Logger to send messages
    protected $logger;
    protected $log_has_errors = false;

    /**
    * Return 
    */


    public static function loggerName() 
    {
        return __CLASS__;
    }

    public static function loggerSignature() 
    {
        return __CLASS__;
    }

    /**
    * Set up Logger 
    */
    public static function loggerSetup( $name = '', $signature = '' ) 
    {
        if ( !$name)      $name      = self::loggerName();
        if ( !$signature) $signature = self::loggerSignature();

        return \App\ActivityLogger::setup( $name, $signature );
    }

    /**
    * Get Logger
    */
    public static function logger() 
    {
        return self::loggerSetup();
    }


/* ********************************************************************************************* */   


    
    protected function logMessage($level = '', $message = '', $context = [])
    {
        $this->logger->log($level, $message, $context);

        if ( $level == 'ERROR' ) $this->log_has_errors = true;
    }
    
    public function logHasErrors()
    {
        return $this->log_has_errors; 
    }

}