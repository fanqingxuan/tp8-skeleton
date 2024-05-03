<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @property int $activated
 * @property int $created
 * @property int $logged
 * @property int $uid
 * @property string $authCode
 * @property string $group
 * @property string $mail
 * @property string $name
 * @property string $password
 * @property string $screenName
 * @property string $url
 * @mixin \think\Model
 */
class User extends Model
{
    protected $name = 'tbl_users';
}
