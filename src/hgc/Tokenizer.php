<?php
/**
 * Created by PhpStorm.
 * User: henry.grechcini
 * Date: 06/04/2016
 * Time: 15:07
 */

class Tokenizer {
    private $_keyValueArray;
    private $_pattern;
    private $_subject;

    private $_result;

    /**
     * @return mixed
     */
    public function getKeyValueArray()
    {
        return $this->_keyValueArray;
    }

    /**
     * @param mixed $keyValueArray
     * @return Tokenizer
     */
    public function setKeyValueArray($keyValueArray)
    {
        $this->_keyValueArray = $keyValueArray;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * @param mixed $pattern
     * @return Tokenizer
     */
    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @param mixed $subject
     * @return Tokenizer
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @param mixed $result
     * @return Tokenizer
     */
    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    private function _tokenReplace($argMatches) {
        if (!is_array($argMatches)) {
            throw new \Exception('No matches provided.');
        }

        if (count($argMatches)!=1) {
            throw new \Exception('Matched more than one value as a token key. There should be only one.');
        }

        $tokenName=$argMatches[0];

        if (!is_string($tokenName)) {
            throw new \Exception('The matched token should be a string.');
        }

        if (array_key_exists($argMatches[0], $this->_keyValueArray)) {
            $value=$this->_keyValueArray[$tokenName];
        } else {
            throw new \Exception('Token '.$tokenName.' not found.');
        }

        return $value;
    }

    public function process() {
        $this->setResult(preg_replace($this->getPattern(), array($this, '_tokenReplace'), $this->getSubject()));
	}
}