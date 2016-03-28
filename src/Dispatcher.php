<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-22
 * Time: 23:06
 */

namespace eiffelis;


class Dispatcher
{
    protected $config;

    public function __construct()
    {
        $this->config = parse_ini_file('config.ini');
    }

    /**
     * @param string $URI
     *
     * @return string
     */
    public function renderURI($URI){

    }
}
