<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-23
 * Time: 00:01
 */

namespace iRestMyCase\Models;


class User
{
    const PERMISSION_LEVEL_READ = 4;
    const PERMISSION_LEVEL_WRITE = 2;
    const PERMISSION_LEVEL_READWRITE = 6;

    protected $username;
    protected $password;
    protected $passwordHash;
    protected $permissionLevel;
}