<?php

/**
 * RCS Framework Object
 * 
 * emits() should never be done in Constructors. There is a good chance a connect() was not done yet.
 * 
 * Yes, this isn't Asynchronous, but it can help to maybe organize PHP code a bit on a large scale.
 * There is an obvious performance hit.
 *
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Core
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.1.2
 */
class RCS_Core_Object
{
    /**
     * Object GUID
     * @var string
     */
    private $_guid = null;
    
    /**
     * Framework Version
     * @var string
     */
    private $_version = '0.1.2';
    
    /**
     * Minimum PHP Version
     * @var string
     */
    private $_minPHPVersion = '5.2.0';
    
    /**
     * Storage of Signal/Slot connections
     * @var array
     */
    public static $signalSlotConnections = array();

    /**
     * 
     * @throws Exception
     */
    public function __construct()
    {
        if (version_compare(PHP_VERSION, $this->_minPHPVersion, '<')) {
            throw new RCS_Core_Exception('RCS Framework '.$this->_version.' requires at least PHP '.$this->_minPHPVersion);
        }
        
        $this->_generateGuid();
        
    }
    
    /**
     * Function to generate GUID
     * <br />This function generates the Child Classes GUID
     *
     * @access private
     */
    private function _generateGuid()
    {
        $this->_guid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
        
        return $this->_guid;
    }
    
    /**
     * Getter for GUID
     * 
     * @return string
     */
    public function getGuid()
    {
        return $this->_guid;
    }
    
    /**
     * Connect
     * 
     * @param RCS_Core_Object $sender
     * @param string $signal
     * @param RCS_Core_Object $receiver
     * @param string $slot
     */
    public static function connect( RCS_Core_Object $sender, $signal, RCS_Core_Object $receiver, $slot )
    {
        $isStatic = !(isset($this) && get_class($this) == __CLASS__);
        
        // Is this a static call ?
        if (!$isStatic) {
            throw new Exception(__METHOD__ .' is static ');
        }
        
        // Check if $signal is a string
        if ( !is_string($signal) ) {
            throw new InvalidArgumentException('$signal must be of type string');
        }
        
        // Check if $slot is a string
        if ( !is_string($slot) ) {
            throw new InvalidArgumentException('$slot must be of type string');
        }
        
        if( $sender->getGuid() === null ) {
            throw new Exception(__METHOD__.' .. sender does not have a guid');
        }
        
        if( $receiver->getGuid() === null ) {
            throw new Exception(__METHOD__.' .. receiver does not have a guid');
        }
        
        // Check if slot is a valid slot
        $slotReflection = new ReflectionClass ( get_class($receiver) );
        
        try {
            /* @var $method ReflectionMethod */
            $method = $slotReflection->getMethod($slot);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
        
        if (!strstr($method->getDocComment(), '@slot')) {
            throw new InvalidArgumentException($slotReflection->getName().'::'.$method->getName() . ' is not declared as a slot');
        }
        
        // Check if signal is a valid signal
        $signalReflection = new ReflectionClass ( get_class($sender) );
        
        if (!strstr($signalReflection->getDocComment(), $signal)) {
            throw new InvalidArgumentException($signalReflection->getName().' does not have '.$signal.' declared as a signal');
        }
        
        $eventObj = new RCS_Core_Event();
        $eventObj->setType(RCS_Core_Event::OBJECT_CONNECTION_TYPE);
        $eventObj->setSender($sender);
        $eventObj->setSignal($signal);
        $eventObj->setReceiver($receiver);
        $eventObj->setSlot($slot);        
        
        self::$signalSlotConnections[] = $eventObj;
    }
    
    /**
     * Connect By Name
     * 
     * @param string $sender
     * @param string $signal
     * @param RCS_Core_Object $receiver
     * @param string $slot
     */
    public static function connectByName( $sender, $signal, RCS_Core_Object $receiver, $slot )
    {
        $isStatic = !(isset($this) && get_class($this) == __CLASS__);
        
        // Is this a static call ?
        if (!$isStatic) {
            throw new Exception(__METHOD__ .' is static ');
        }
        
        // Check if $signal is a string
        if ( !is_string($signal) ) {
            throw new InvalidArgumentException('$signal must be of type string');
        }
        
        // Check if $slot is a string
        if ( !is_string($slot) ) {
            throw new InvalidArgumentException('$slot must be of type string');
        }
        
        // Check if $sender is a string
        if ( !is_string($sender) ) {
            throw new InvalidArgumentException('$sender must be of type string');
        }
        
        if( $receiver->getGuid() === null ) {
            throw new Exception(__METHOD__.' .. receiver does not have a guid');
        }
        
        // Check if slot is a valid slot
        $slotReflection = new ReflectionClass ( get_class($receiver) );
        
        try {
            /* @var $method ReflectionMethod */
            $method = $slotReflection->getMethod($slot);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
        
        if (!strstr($method->getDocComment(), '@slot')) {
            throw new InvalidArgumentException($slotReflection->getName().'::'.$method->getName() . ' is not declared as a slot');
        }
        
        // Check if signal is a valid signal
        $signalReflection = new ReflectionClass ( $sender );
        
        if (!strstr($signalReflection->getDocComment(), $signal)) {
            throw new InvalidArgumentException($signalReflection->getName().' does not have '.$signal.' declared as a signal');
        }
        
        $eventObj = new RCS_Core_Event();
        $eventObj->setType(RCS_Core_Event::NAME_CONNECTION_TYPE);
        $eventObj->setSender($sender);
        $eventObj->setSignal($signal);
        $eventObj->setReceiver($receiver);
        $eventObj->setSlot($slot);

        self::$signalSlotConnections[] = $eventObj;
    }

    /**
     * Emit a Signal 
     * 
     * @param string $signal
     * @return void
     */
    public function emit( $signal )
    {
        //Lets loop through the connected signals and see if we have a match.
        $signalSlots = array();
        $signalSlots = self::$signalSlotConnections;

        if( count($signalSlots) > 0 ) {
            /* @var $eventObj RCS_Core_Event */
            foreach( $signalSlots as $eventObj ) {
                if ($eventObj->getType() == RCS_Core_Event::NAME_CONNECTION_TYPE ) { 
                    if( ( get_class($this) == $eventObj->getSender() ) && ( $signal == $eventObj->getSignal() ) ) {
                        //We got a match
                        $function = $eventObj->getSlot();
                        $params = func_get_args(); 
                        array_shift($params); 
                        
                        call_user_func_array(array($eventObj->getReceiver(), $function), $params);
                                
                    }    
                } else if ($eventObj->getType() == RCS_Core_Event::OBJECT_CONNECTION_TYPE ) {
                    if( ( $this->getGuid() == $eventObj->getSender()->getGuid() ) && ( $signal == $eventObj->getSignal() ) ) {
                        //We got a match
                        $function = $eventObj->getSlot();
                        $params = func_get_args(); 
                        array_shift($params); 
                        
                        call_user_func_array(array($eventObj->getReceiver(), $function), $params);
                                
                    }    
                }                                
            }                      
        }        
    }
}
