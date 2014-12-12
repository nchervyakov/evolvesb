<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.11.2014
 * Time: 13:38
 */


namespace App\Type;


class ArrayObject extends \ArrayObject
{
    public function contains($element)
    {
        foreach ($this as $el) {
            if ($el === $element) {
                return true;
            }
        }

        return false;
    }

    public function containsKey($key)
    {
        return array_key_exists($key, $this);
    }

    public function removeElement($element)
    {
        foreach ($this as $key => $el) {
            if ($el === $element) {
                unset($this[$key]);
                return true;
            }
        }

        return false;
    }

    public function clear()
    {
        parent::exchangeArray([]);
    }

    public function firstElement()
    {
        if ($this->count()) {
            return $this->getIterator()->current();
        }

        return null;
    }
}