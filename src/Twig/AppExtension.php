<?php


namespace App\Twig;


use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {

        $this->locale = $locale;
    }
    public function getFilters()
    {
        //"price" is the name of this filter, it is being used for the price.
        //Callable part is pointing the class/function respectively.
        return [
            new TwigFilter("price", [$this, "priceFilter"])
        ];
    }

    public function getGlobals()
    {
        //Getting locale from services.yaml
        return [
            "locale" => $this->locale
        ];
    }
    //The part where the job gets done obviously.
    public function priceFilter($number)
    {
        return "$".number_format($number, 2, ".", ",");
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest(
                "like",
                function ($obj) { return $obj instanceof LikeNotification;})
        ];
    }
}