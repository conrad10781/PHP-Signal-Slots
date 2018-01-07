<?php

/**
 * RCS Framework Object
 * 
 * @category   RCS Framework 
 * @package    Modules
 * @subpackage Core
 * @copyright  Copyright (c) 2009 RCS
 * @author RCS
 * @version 0.2.4
 */
class RCS_Core_Event
{

    const OBJECT_CONNECTION_TYPE = 0;

    const NAME_CONNECTION_TYPE = 1;

    /**
     *
     * @var int
     */
    protected $_type;

    /**
     * Accepts RCS_Core_Object, and strings
     *
     * @var mixed
     */
    protected $_sender;

    /**
     *
     * @var string
     */
    protected $_signal;

    /**
     *
     * @var RCS_Core_Object
     */
    protected $_receiver;

    /**
     *
     * @var string
     */
    protected $_slot;

    /**
     * Set _type
     *
     * @param int $_type            
     * @return RCS_Core_Event
     */
    public function setType ($_type)
    {
        $this->_type = $_type;
        return $this;
    }

    /**
     * Get _type
     *
     * @return int
     */
    public function getType ()
    {
        return $this->_type;
    }

    /**
     * Set _sender
     *
     * @param mixed $_sender            
     * @return RCS_Core_Event
     */
    public function setSender ($_sender)
    {
        $this->_sender = $_sender;
        return $this;
    }

    /**
     * Get _sender
     *
     * @return mixed
     */
    public function getSender ()
    {
        return $this->_sender;
    }

    /**
     * Set _signal
     *
     * @param string $_signal            
     * @return RCS_Core_Event
     */
    public function setSignal ($_signal)
    {
        $this->_signal = $_signal;
        return $this;
    }

    /**
     * Get _signal
     *
     * @return string
     */
    public function getSignal ()
    {
        return $this->_signal;
    }

    /**
     * Set _receiver
     *
     * @param RCS_Core_Object $_receiver            
     * @return RCS_Core_Event
     */
    public function setReceiver (RCS_Core_Object $_receiver)
    {
        $this->_receiver = $_receiver;
        return $this;
    }

    /**
     * Get _receiver
     *
     * @return RCS_Core_Object
     */
    public function getReceiver ()
    {
        return $this->_receiver;
    }

    /**
     * Set _slot
     *
     * @param string $_slot            
     * @return RCS_Core_Event
     */
    public function setSlot ($_slot)
    {
        $this->_slot = $_slot;
        return $this;
    }

    /**
     * Get _slot
     *
     * @return string
     */
    public function getSlot ()
    {
        return $this->_slot;
    }

    /**
     *
     * @param array $params            
     */
    public function dispatch (array $params)
    {
        $function = $this->getSlot();
        
        call_user_func_array(
                array(
                        $this->getReceiver(),
                        $function
                ), $params);
    }
}

