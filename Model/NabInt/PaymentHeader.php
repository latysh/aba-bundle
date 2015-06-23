<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\NabInt;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentHeader {

    /**
     * @var string $indicator
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 02)
     */
    private $indicator;

    /**
     * @var string $messageType
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 'IFT')
     */
    private $messageType;

    /**
     * @var string $numberPayments
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = '0001')
     */
    private $numberPayments;

    public function __construct() {
        $this->setIndicator('02');
        $this->setMessageType('IFT');
        $this->setNumberPayments('0001');
    }

    /**
     * @return string
     */
    public function getIndicator() {
        return $this->indicator;
    }

    /**
     * @param string $indicator
     */
    public function setIndicator($indicator) {
        $this->indicator = $indicator;
    }

    /**
     * @return string
     */
    public function getMessageType() {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     */
    public function setMessageType($messageType) {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getNumberPayments() {
        return $this->numberPayments;
    }

    /**
     * @param string $numberPayments
     */
    public function setNumberPayments($numberPayments) {
        $this->numberPayments = $numberPayments;
    }
}
