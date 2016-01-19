<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\Aba;

use Symfony\Component\Validator\Constraints as Assert;

class DescriptiveRecord
{
    /**
     * @var string $recordType
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 0)
     */
    private $recordType;

    /**
     * @var string $bsb
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{3}-[\d]{3}$/",
     *     message = "Detail descriptive bsb is invalid: {{ value }}. Required format is 000-000."
     * )
     */
    private $bsb;

    /**
     * @var string $accountNumber
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,9}$/",
     *     message = "Detail descriptive account number is invalid: {{ value }}. Must be up to 9 digits only."
     * )
     */
    private $accountNumber;

    /**
     * @var string $sequenceNumber
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,2}$/",
     *     message = "Detail descriptive sequence number is invalid: {{ value }}. Must be up to 2 digits only."
     * )
     */
    private $sequenceNumber;

    /**
     * @var string $bankName
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[A-Z]{3}$/",
     *     message = "Descriptive record bank name is invalid: {{ value }}. Must be capital letter abbreviation of length 3."
     * )
     */
    private $bankName;

    /**
     * @var string $bankName
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\w\s\_?\^\[\],.+-;:=#\/\*\(\)&%!\$@]{0,26}$/",
     *     message = "Descriptive record user name is invalid: {{ value }}. Must be letters only and up to 26 characters long."
     * )
     */
    private $userName;

    /**
     * @var string $bankName
     *
     * @Assert\Regex(
     *     pattern = "/^[\d]{6}$/",
     *     message = "Descriptive record direct entry user ID is invalid: {{ value }}. Must be 6 digits long."
     * )
     */
    private $directEntryUserId;

    /**
     * @var string $bankName
     *
     * @Assert\Regex(
     *     pattern = "/^[\w\s\_?\^\[\],.+-;:=#\/\*\(\)&%!\$@]{0,12}$/",
     *     message = "Descriptive record description is invalid: {{ value }}. Must be letters only and up to 12 characters long."
     * )
     */
    private $description;

    /**
     * @var \DateTime $processingDateTime
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $processingDateTime;

    public function __construct()
    {
        $this->setRecordType('0');
        $this->setSequenceNumber('01');
        $this->setProcessingDateTime(new \DateTime());
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
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @param string $sequenceNumber
     */
    public function setSequenceNumber($sequenceNumber)
    {
        $this->sequenceNumber = $sequenceNumber;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getDirectEntryUserId()
    {
        return $this->directEntryUserId;
    }

    /**
     * @param string $directEntryUserId
     */
    public function setDirectEntryUserId($directEntryUserId)
    {
        $this->directEntryUserId = $directEntryUserId;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getProcessingDateTime()
    {
        return $this->processingDateTime;
    }

    /**
     * @param \DateTime $processingDateTime
     */
    public function setProcessingDateTime($processingDateTime)
    {
        $this->processingDateTime = $processingDateTime;
    }
}