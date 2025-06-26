<?php
declare (strict_types = 1);

namespace app\transformer;

use app\vo\Book;
use support\Transformer;

class HelloTransformer extends Transformer
{
    public function transform(Book $book)
	{
	    return [
	        'my_title'=>$book->title??'',
			'status'=>$book->status??0
	    ];
	}
}
