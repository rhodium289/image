<?php

use HGC\Timer;

class TimerTest extends PHPUnit_Framework_TestCase {
    public function testTimerCanMeasure()
    {
        $timer=new Timer();
        $timer->start();
        $timer->stop();
        $this->assertTrue($timer->getAccumulatedTime()>0);
    }
}