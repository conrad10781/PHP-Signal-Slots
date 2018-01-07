PHP-Signal-Slots
================

Based on Qt's implementation of signals/slots ( not asynchronous )

### Overview
As with Qt's signals/slots, this package was built to be very light, and work well with existing applications that didn't originally depend on it.

Adding the functionality of this package is simple:

1. Extend RCS_Core_Object in your existing class.
2. Add @signal declarations to your doc comments for your class. 
3. Add @slot declarations to your doc comments for your functions.


### Example
```php
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
RCS_Core_Object::connect($testSignalClass, "signalWithNoArguments", $testSlotClass, "testSlotForSignalWithNoArguments");
RCS_Core_Object::connect($testSignalClass, "signalWithOneArgument", $testSlotClass, "testSlotForSignalWithOneArgument");

// This can be used in cases where the original code implementation was done very poorly, 
// or possibly encoded into a package such as Zend Guard or a PHP extension where you can't access the source directly
RCS_Core_Object::connectByName("Test_Signal_Class", "signalWithOneArgument", $testSlotClass, "testSlotForSignalWithOneArgument");

// This has just one connection
$testSignalClass->testFunctionEmittingSignalWithNoArguments();

// This has two connections
$testSignalClass->testFunctionEmittingSignalWithOneArgument();

```

The above code will result in the following output:

```
Got to Test_Slot_Class::testSlotForSignalWithNoArguments<br />
Got to Test_Slot_Class::testSlotForSignalWithOneArgument<br />
Test_Model_Class Object
(
)
Got to Test_Slot_Class::testSlotForSignalWithOneArgument<br />
Test_Model_Class Object
(
)
```

