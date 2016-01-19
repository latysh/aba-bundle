<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\NabInt;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentTrailerRecord
{
    /**
     * @var string $indicator
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 79)
     */
    private $indicator;


    public function __construct()
    {
        $this->setIndicator('79');
    }

    /**
     * @return string
     */
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     */
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;
    }
}
