<?php

namespace app\transformer;

use League\Fractal\TransformerAbstract;

class UserItemTransformer extends TransformerAbstract
{
    public function transform($model)
    {
        return [
            'id' => $model['id'],
            'address' => $model['address'],
        ];
    }
}