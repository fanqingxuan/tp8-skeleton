<?php

namespace app\transformer;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform($user)
    {
        return [
            'id' => $user['id'],
            'name' => $user['name'],
        ];
    }
}