<?php 

// Display all errors in this example
error_reporting( E_ALL );
ini_set('display_errors', 1);

// Set include path ( for __autoload )
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(realpath(__FILE__)).'/',
    get_include_path(),
)));

/**
 * 
 * @param string $class_name
 */
function __autoload($class_name) {
    $class_name = str_replace('_', '/', $class_name). '.php';
    require_once $class_name ;
}

// Lets start with a class that extends RCS_Core_Object
/**
 * @signal signalWithNoArguments
 * @signal signalWithOneArgument
 * ^-- This is required. You will declare all of your signals this way
 */
class Test_Signal_Class extends RCS_Core_Object
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

class Test_Slot_Class extends RCS_Core_Object
{
    /**
     *
     * @slot
     * ^-- This is required to declare the function as a slot
     */
    public function testSlotForSignalWithNoArguments ()
    {
        print "Got to " . __METHOD__ . "<br />";
    }
    
    /**
     *
     * @slot
     * ^-- This is required to declare the function as a slot
     */
    public function testSlotForsignalWithOneArgument ( Test_Model_Class $testModelClass )
    {
        // var_dump(debug_backtrace());
        print "Got to " . __METHOD__ . "<br />";
        var_dump($testModelClass);
    }
}

class Test_Model_Class {}

$testSignalClass = new Test_Signal_Class();
$testSlotClass = new Test_Slot_Class();

RCS_Core_Object::connect($testSignalClass, "signalWithNoArguments", $testSlotClass, "testSlotForSignalWithNoArguments");
RCS_Core_Object::connect($testSignalClass, "signalWithOneArgument", $testSlotClass, "testSlotForsignalWithOneArgument");

RCS_Core_Object::connectByName("Test_Signal_Class", "signalWithOneArgument", $testSlotClass, "testSlotForsignalWithOneArgument");

$testSignalClass->testFunctionEmittingSignalWithNoArguments();
$testSignalClass->testFunctionEmittingSignalWithOneArgument();

print "<hr />";
var_dump($testSignalClass);
var_dump($testSlotClass);
die;
