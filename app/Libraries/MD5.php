<?php


namespace App\Libraries;

use Illuminate\Contracts\Hashing\Hasher;

class MD5 implements Hasher
{
    /**
     * Hash the given value.
     *
     * @param string $value
     *
     * @return array  $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        return md5($value);
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param string $value
     * @param string $hashedValue
     * @param array $options
     *
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if(empty($hashedValue)){
            return true;
        }
        return $this->make($value) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param string $hashedValue
     * @param array $options
     *
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}