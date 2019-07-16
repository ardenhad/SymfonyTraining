<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        //"price" is the name of this filter, it is being used for the price.
        //Callable part is pointing the class/function respectively.
        return [
            new TwigFilter("price", [$this, "priceFilter"])
        ];
    }
    //The part where the job gets done obviously.
    public function priceFilter($number)
    {
        return "$".number_format($number, 2, ".", ",");
    }


}