<?php
 

 
class FirstTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertTrue(){
                $this->assertTrue(1===1);
           }
    public function testAssertFalse(){
                $this->assertFalse(1===0);
           }
}