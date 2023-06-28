<?php

namespace PHPAuth\Core;

class Result implements \ArrayAccess, \Serializable
{
    /**
     * @var bool
     */
    public $is_success = true;

    /**
     * @var bool
     */
    public $is_error = false;

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var string
     */
    public $code = '';

    public function __construct(bool $is_success = true, string $message = '', string $code = '')
    {
        $this->is_success = $is_success;
        $this->is_error = !$is_success;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Getter.
     * Handles access to non-existing property
     *
     * @param string $key
     * @return null
     */
    public function __get(string $key)
    {
        return $this->offsetExists($key) ? $this->{$key} : null;
    }

    /**
     * Setter
     * Handles access to non-existing property
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function __set(string $key, $value = null)
    {
        $this->{$key} = $value;
    }

    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset)
    {
        if (property_exists($this, $offset)) {
            return $this->{$offset};
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Stringable interface available since PHP 8.0
     * @return string
     */
    public function __toString(): string
    {
        return $this->message;
    }

    public function serialize()
    {
        return json_encode([
            'is_success'    =>  $this->is_success,
            'is_error'      =>  $this->is_error,
            'message'       =>  $this->message,
            'code'          =>  $this->code
        ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE /*| JSON_THROW_ON_ERROR*/);
    }

    public function unserialize($data)
    {
        $json = json_decode($data, true);
        $this->is_success   = array_key_exists('is_success', $json) ? $json['is_success'] : true;
        $this->is_error     = array_key_exists('is_error', $json) ? $json['is_error'] : false;
        $this->message      = array_key_exists('message', $json) ? $json['message'] : '';
        $this->code         = array_key_exists('code', $json) ? $json['code'] : '';
        unset($json);
    }
}
