<?php 

// Display all errors in this example
error_reporting( E_ALL );
ini_set('display_errors', 1);

// Set include path ( for __autoload )
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(realpath(__FILE__)).'/',
    get_include_path(),
)));

require "RCS/Core/Object.php";
require "RCS/Core/Event.php";

// Lets start with a class that extends \RCS\Core\Object
/**
 * @signal signalWithNoArguments
 * @signal signalWithOneArgument
 * ^-- This is required. You will declare all of your signals this way
 */
class Test_Signal_Class extends \RCS\Core\Object
{
    /**
     * 
     */
    public function testFunctionEmittingSignalWithNoArguments ()
    {
        $this->emit( "signalWithNoArguments" );
    }
    
    /**
     * 
     */
    public function testFunctionEmittingSignalWithOneArgument ()
    {
        $this->emit( "signalWithOneArgument", new Test_Model_Class() );
    }
    
}

class Test_Slot_Class extends \RCS\Core\Object
{
    /**
     *
     * @slot
     * ^-- This is required to declare the function as a slot
     */
    public function testSlotForSignalWithNoArguments ()
    {
        print "Got to " . __METHOD__ . "<br />\n";
    }
    
    /**
     *
     * @slot
     * ^-- This is required to declare the function as a slot
     */
    public function testSlotForSignalWithOneArgument ( Test_Model_Class $testModelClass )
    {
        // var_dump(debug_backtrace());
        print "Got to " . __METHOD__ . "<br />\n";
        print_r($testModelClass);
    }
}

class Test_Model_Class {}

$testSignalClass = new Test_Signal_Class();
$testSlotClass = new Test_Slot_Class();

// This is the ideal way to implement the library
\RCS\Core\Object::connect($testSignalClass, "signalWithNoArguments", $testSlotClass, "testSlotForSignalWithNoArguments");
\RCS\Core\Object::connect($testSignalClass, "signalWithOneArgument", $testSlotClass, "testSlotForSignalWithOneArgument");

// This can be used in cases where the original code implementation was done very poorly, 
// or possibly encoded into a package such as Zend Guard or a PHP extension where you can't access the source directly
\RCS\Core\Object::connectByName("Test_Signal_Class", "signalWithOneArgument", $testSlotClass, "testSlotForSignalWithOneArgument");

// This has just one connection
$testSignalClass->testFunctionEmittingSignalWithNoArguments();

// This has two connections
$testSignalClass->testFunctionEmittingSignalWithOneArgument();

