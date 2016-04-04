<?php

namespace HGC;

class Timer
{
    static private $_startTime;
    static private $_accumulatedTime;

    static public function start(){
        self::$_startTime = microtime(true);
    }

    static public function stop(){
        self::$_accumulatedTime += self::_getElapsedTime();
        self::$_startTime = null;
    }

    static private function _getElapsedTime(){
        if(is_null(self::$_startTime)){
            $elapsedTime = 0;
        }else{
            $currentTime = microtime(true);
            $elapsedTime = $currentTime - self::$_startTime;
        }

        return $elapsedTime;
    }

    static public function getAccumulatedTime()
    {
        return self::$_accumulatedTime + self::_getElapsedTime();
    }
}