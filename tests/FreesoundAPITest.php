<?php

use Pel\Helper\FreesoundAPI;

class FreesoundAPITest extends PHPUnit_Framework_TestCase
{
    protected static function newFreesoundAPI(
        $api_key = 'api_key',
        $curl_options = array()
    ) {
        return new FreesoundAPI(
            $api_key,
            $curl_options
        );
    }

    protected static function callMethod($name, array $args, $obj = null)
    {
        if ($obj === null) {
            $obj = static::newFreesoundAPI();
        }

        $class = new ReflectionClass('Pel\Helper\FreesoundAPI');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /*************************************************************
     * parseParams tests
     *************************************************************/

    public function testParseParamsNonRecursive()
    {
        $params = array(
            'param1' => 60,
            'param2' => 'string'
        );
        $qs = static::callMethod('parseParams', array($params));
        $qs_expected = "param1=60&param2=string";
        $this->assertTrue($qs == $qs_expected);
    }

    public function testParseParamsRecursive()
    {
        $params = array(
            'param1' => 60,
            'param2' => 'string',
            'paramRecursive1' => array(
                'param3' => 50,
                'paramRecursive2' => array(
                    'param4' => 'string',
                    'param5' => 40
                )
            )
        );
        $qs = static::callMethod('parseParams', array($params));
        $qs_expected = "param1=60&param2=string&param3=50&param4=string&param5=40";
        $this->assertTrue($qs == $qs_expected);
    }
}
