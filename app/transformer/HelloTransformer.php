<?php
declare (strict_types = 1);

namespace app\transformer;

use support\Transformer;

class HelloTransformer extends Transformer
{
    public function transform($item)
	{
	    return [
	        'username'=>$item['name']??'',
			'age'=>$item['age']??0
	    ];
	}
}
