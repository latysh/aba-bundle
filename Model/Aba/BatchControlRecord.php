<?php
/**
 * Author: Latysh (Altynbek Usenov).
 */

namespace Latysh\AbaBundle\Model\Aba;

use Symfony\Component\Validator\Constraints as Assert;

class BatchControlRecord
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 7)
     */
    private $recordType;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{3}-[\d]{3}$/",
     *     message = "Batch control record bsb is invalid: {{ value }}. Required format is 000-000."
     * )
     */
    private $bsb;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,10}$/",
     *     message = "Batch control record net total is invalid: {{ value }}. Must be up to 10 digits only."
     * )
     */
    private $netTotal;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,10}$/",
     *     message = "Batch control record credit total is invalid: {{ value }}. Must be up to 10 digits only."
     * )
     */
    private $creditTotal;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,10}$/",
     *     message = "Batch control record debit total is invalid: {{ value }}. Must be up to 10 digits only."
     * )
     */
    private $debitTotal;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,6}$/",
     *     message = "Batch control record number of records is invalid: {{ value }}. Must be up to 6 digits only."
     * )
     */
    private $numberRecords;

    public function __construct()
    {
        $this->setRecordType('7');
        $this->setBsb('999-999');
        $this->setNetTotal('0');
        $this->setCreditTotal('0');
        $this->setDebitTotal('0');
        $this->setNumberRecords('0');
    }

    /**
     * @return string
     */
    public function getRecordType()
    {
        return $this->recordType;
    }

    /**
     * @param string $recordType
     */
    public function setRecordType($recordType)
    {
        $this->recordType = $recordType;
    }

    /**
     * @return string
     */
    public function getBsb()
    {
        return $this->bsb;
    }

    /**
     * @param string $bsb
     */
    public function setBsb($bsb)
    {
        $this->bsb = $bsb;
    }

    /**
     * @return int
     */
    public function getNetTotal()
    {
        return $this->netTotal;
    }

    /**
     * @param int $netTotal
     */
    public function setNetTotal($netTotal)
    {
        $this->netTotal = $netTotal;
    }

    /**
     * @return int
     */
    public function getCreditTotal()
    {
        return $this->creditTotal;
    }

    /**
     * @param int $creditTotal
     */
    public function setCreditTotal($creditTotal)
    {
        $this->creditTotal = $creditTotal;
    }

    /**
     * @return int
     */
    public function getDebitTotal()
    {
        return $this->debitTotal;
    }

    /**
     * @param int $debitTotal
     */
    public function setDebitTotal($debitTotal)
    {
        $this->debitTotal = $debitTotal;
    }

    /**
     * @return mixed
     */
    public function getNumberRecords()
    {
        return $this->numberRecords;
    }

    /**
     * @param mixed $numberRecords
     */
    public function setNumberRecords($numberRecords)
    {
        $this->numberRecords = $numberRecords;
    }
}
