<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class FilterData
{

    #[Assert\Type("array")]
    public array $categories = [];
    
    #[Assert\Type("null", "integer")]
    public $max;
    
    #[Assert\Type("null", "integer")]
    public $min;

}
