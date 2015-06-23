<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Model\NabInt;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PaymentDetailRecord {

    /**
     * @var string $indicator
     *
     * @Assert\NotBlank()
     * @Assert\EqualTo(value = 55)
     */
    private $indicator;

    /**
     * @var string $paymentMethod
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^(AUD|BTC|FEC|NFA|REF|RTR)$/",
     *     message = "Payment detail record payment method is invalid: {{ value }}. Must be one of (AUD, BTC, FEC, NFA, REF, RTR)"
     * )
     */
    private $paymentMethod;

    /**
     * @var string $paymentLegCurrencyCode
     *
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    private $paymentLegCurrencyCode;

    /**
     * @var float $paymentLegAmount
     *
     * @Assert\Type(
     *     type="numeric",
     *     message="Payment detail record payment amount value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Range(
     *      min = 0.01,
     *      max = 999999999999999,
     *      minMessage = "Payment detail record payment amount is invalid: {{ value }}. Must be at least {{ limit }}",
     *      maxMessage = "Payment detail record payment amount is invalid: {{ value }}. Cannot be more than {{ limit }}"
     * )
     */
    private $paymentLegAmount;

    /**
     * @var string $fxRate
     *
     * @Assert\Type(
     *     type="numeric",
     *     message="Payment detail record fxRate value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Range(
     *      min = 0.000001,
     *      max = 9999999999,
     *      minMessage = "Payment detail record fxRate is invalid: {{ value }}. Must be at least {{ limit }}",
     *      maxMessage = "Payment detail record fxRate is invalid: {{ value }}. Cannot be more than {{ limit }}"
     * )
     */
    private $fxRate;

    /**
     * @var string $debitAccountBsb
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^[\d]{0,6}$/",
     *     message = "Payment detail record debit account BSB is invalid: {{ value }}. Must be up to 6 digits without dash."
     * )
     */
    private $debitAccountBsb;

    /**
     * @var string $debitAccountNumber
     *
     * @Assert\Regex(
     *     pattern = "/^[\w]{0,35}$/",
     *     message = "Payment detail record debit account number is invalid: {{ value }}. Must be up to 34 letters and numbers."
     * )
     */
    private $debitAccountNumber;

    /**
     * @var string $debitCurrencyCode
     *
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    private $debitCurrencyCode;

    /**
     * @var float $debitAmount
     *
     * @Assert\Type(
     *     type="numeric",
     *     message="Payment detail record debit amount value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Length(
     *      min = 0,
     *      max = 15,
     *      minMessage = "Payment detail record debit amount is invalid: {{ value }}. Must be at least {{ limit }}",
     *      maxMessage = "Payment detail record debit amount is invalid: {{ value }}. Cannot be more than {{ limit }}"
     * )
     */
    private $debitAmount;

    /**
     * @var string $refinanceIndicator
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern = "/^(0|1)$/",
     *     message = "Payment detail record payment method is invalid: {{ value }}. Must be one of either 0 or 1"
     * )
     */
    private $refinanceIndicator;

    /**
     * @var string $textToNabForPayAccount
     *
     * @Assert\Regex(
     *     pattern = "/^[\w\s\(\)\_?.,+-]{0,60}$/",
     *     message = "Payment detail record text to NAB for pay account is invalid: {{ value }}. Must be up to 60 characters, including special characters(+-/'?.,())."
     * )
     */
    private $textToNabForPayAccount;

    /**
     * @var string $fecNumber
     *
     * @Assert\Type(
     *     type="integer",
     *     message="Payment detail record FEC number value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Length(
     *      min = 0,
     *      max = 6,
     *      minMessage = "Payment detail record FEC number is invalid: {{ value }}. Must be at least {{ limit }}",
     *      maxMessage = "Payment detail record FEC number is invalid: {{ value }}. Cannot be more than {{ limit }}"
     * )
     */
    private $fecNumber;

    /**
     * @var string $efxNumber
     *
     * @Assert\Type(
     *     type="integer",
     *     message="Payment detail record EFX number value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\Length(
     *      min = 0,
     *      max = 15,
     *      minMessage = "Payment detail record EFX number is invalid: {{ value }}. Must be at least {{ limit }}",
     *      maxMessage = "Payment detail record EFX number is invalid: {{ value }}. Cannot be more than {{ limit }}"
     * )
     */
    private $efxNumber;

    public function __construct() {
        $this->setIndicator('55');
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
        //Payment method
        if (in_array($this->getPaymentMethod(), ['AUD', 'BTC', 'FEC', 'RTR', 'EFX']) && $this->getDebitCurrencyCode() != 'AUD') {
            $context->buildViolation('For payment methods (AUD, BTC, FEC, RTR, EFX) currency should be AUD')
                ->atPath('paymentMethod')
                ->addViolation();
        }

        //Debit account number
        if (($this->getDebitAccountNumber() == '' && $this->getRefinanceIndicator() == '0') || ($this->getDebitAccountNumber() != '' && $this->getRefinanceIndicator() == '1')) {
            $context->buildViolation('Debit account number mandatory if refinance indicator is 0, must be blank if refinance indicator is 1')
                ->atPath('debitAccountNumber')
                ->addViolation();
        }

        //Debit amount
        if (in_array($this->getPaymentMethod(), ['FEC', 'RTR', 'EFX']) && $this->getDebitAmount() == '') {
            $context->buildViolation('Debit amount must be kept blank for all except FEC, RTR, EFX')
                ->atPath('debitAmount')
                ->addViolation();
        }

        //FEC number
        if ($this->getPaymentMethod() == 'FEC' && $this->getFecNumber() == '') {
            $context->buildViolation('FEC number must not be blank for FEC payment method')
                ->atPath('fecNumber')
                ->addViolation();
        }

        //EFX number
        if ($this->getPaymentMethod() == 'EFX' && $this->getFecNumber() == '') {
            $context->buildViolation('EFX number must not be blank for EFX payment method')
                ->atPath('efxNumber')
                ->addViolation();
        }
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
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod) {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getPaymentLegCurrencyCode() {
        return $this->paymentLegCurrencyCode;
    }

    /**
     * @param string $paymentLegCurrencyCode
     */
    public function setPaymentLegCurrencyCode($paymentLegCurrencyCode) {
        $this->paymentLegCurrencyCode = $paymentLegCurrencyCode;
    }

    /**
     * @return float
     */
    public function getPaymentLegAmount() {
        return $this->paymentLegAmount;
    }

    /**
     * @param float $paymentLegAmount
     */
    public function setPaymentLegAmount($paymentLegAmount) {
        $this->paymentLegAmount = $paymentLegAmount;
    }

    /**
     * @return string
     */
    public function getFxRate() {
        return $this->fxRate;
    }

    /**
     * @param string $fxRate
     */
    public function setFxRate($fxRate) {
        $this->fxRate = $fxRate;
    }

    /**
     * @return string
     */
    public function getDebitAccountBsb() {
        return $this->debitAccountBsb;
    }

    /**
     * @param string $debitAccountBsb
     */
    public function setDebitAccountBsb($debitAccountBsb) {
        $this->debitAccountBsb = $debitAccountBsb;
    }

    /**
     * @return string
     */
    public function getDebitAccountNumber() {
        return $this->debitAccountNumber;
    }

    /**
     * @param string $debitAccountNumber
     */
    public function setDebitAccountNumber($debitAccountNumber) {
        $this->debitAccountNumber = $debitAccountNumber;
    }

    /**
     * @return string
     */
    public function getDebitCurrencyCode() {
        return $this->debitCurrencyCode;
    }

    /**
     * @param string $debitCurrencyCode
     */
    public function setDebitCurrencyCode($debitCurrencyCode) {
        $this->debitCurrencyCode = $debitCurrencyCode;
    }

    /**
     * @return float
     */
    public function getDebitAmount() {
        return $this->debitAmount;
    }

    /**
     * @param float $debitAmount
     */
    public function setDebitAmount($debitAmount) {
        $this->debitAmount = $debitAmount;
    }

    /**
     * @return string
     */
    public function getRefinanceIndicator() {
        return $this->refinanceIndicator;
    }

    /**
     * @param string $refinanceIndicator
     */
    public function setRefinanceIndicator($refinanceIndicator) {
        $this->refinanceIndicator = $refinanceIndicator;
    }

    /**
     * @return string
     */
    public function getTextToNabForPayAccount() {
        return $this->textToNabForPayAccount;
    }

    /**
     * @param string $textToNabForPayAccount
     */
    public function setTextToNabForPayAccount($textToNabForPayAccount) {
        $this->textToNabForPayAccount = $textToNabForPayAccount;
    }

    /**
     * @return string
     */
    public function getFecNumber() {
        return $this->fecNumber;
    }

    /**
     * @param string $fecNumber
     */
    public function setFecNumber($fecNumber) {
        $this->fecNumber = $fecNumber;
    }

    /**
     * @return string
     */
    public function getEfxNumber() {
        return $this->efxNumber;
    }

    /**
     * @param string $efxNumber
     */
    public function setEfxNumber($efxNumber) {
        $this->efxNumber = $efxNumber;
    }
}
