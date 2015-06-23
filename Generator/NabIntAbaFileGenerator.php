<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Generator;

use Latysh\AbaBundle\Model\NabInt\HeaderRecord;
use Latysh\AbaBundle\Model\NabInt\PaymentHeader;
use Latysh\AbaBundle\Model\NabInt\PaymentRecord;
use Latysh\AbaBundle\Model\NabInt\PaymentDetailRecord;
use Latysh\AbaBundle\Model\NabInt\PaymentTrailerRecord;
use Latysh\AbaBundle\Model\NabInt\PaymentTrailer;
use Latysh\AbaBundle\Model\NabInt\FileTrailerRecord;
use Symfony\Component\Validator\Exception\ValidatorException;

class NabIntAbaFileGenerator {

    private $validator;

    private $abaString;

    public function __construct($validator) {
        $this->validator = $validator;
        $this->abaString = '';
    }

    /**
     * @param array|PaymentRecord $paymentRecords
     *
     * @return string
     */
    public function generate($paymentRecords) {
        $headerRecord = new HeaderRecord();
        $headerRecord->setNumberMessages(count($paymentRecords));
        $paymentHeader = new PaymentHeader();

        // Header Record
        $this->addHeaderRecord($headerRecord);

        // Payment header
        $this->addPaymentHeader($paymentHeader);

        if (!is_array($paymentRecords)) {
            $paymentRecords = array($paymentRecords);
        }

        foreach ($paymentRecords as $paymentRecord) {
            $paymentRecord->setNumberOfDetailRecords(count($paymentRecord->getPaymentDetailRecords()));
            //calculate sum of payment amount
            foreach ($paymentRecord->getPaymentDetailRecords() as $paymentDetailRecord) {
                $paymentRecord->setPaymentAmount($paymentRecord->getPaymentAmount() + $paymentDetailRecord->getPaymentLegAmount());
            }

            // Payment record validation
            $errors = $this->validator->validate($paymentRecord);

            if (count($errors) > 0) {
                throw new ValidatorException('Payment record error: ' . (string)$errors);
            } else {
                // Payment record
                $this->addPaymentRecord($paymentRecord);

                foreach ($paymentRecord->getPaymentDetailRecords() as $paymentDetailRecord) {
                    // Payment detail record validation
                    $errors = $this->validator->validate($paymentDetailRecord);

                    if (count($errors) > 0) {
                        throw new ValidatorException('Payment detail record error: ' . (string)$errors);
                    } else {
                        // Payment detail record
                        $this->addPaymentDetailRecord($paymentDetailRecord);
                    }
                }
            }
            $paymentTrailerRecord = new PaymentTrailerRecord();
            // Payment trailer record
            $this->addPaymentTrailerRecord($paymentTrailerRecord);

            $paymentTrailer = new PaymentTrailer();
            // Payment trailer
            $this->addPaymentTrailer($paymentTrailer);
        }

        $fileTrailerRecord = new FileTrailerRecord();

        // File trailer record
        $this->addFileTrailerRecord($fileTrailerRecord);

        return $this->abaString;
    }

    /**
     * Create the header record line of the file.
     * @param HeaderRecord $headerRecord
     *
     */
    private function addHeaderRecord(HeaderRecord $headerRecord) {
        // Indicator
        $line = $headerRecord->getIndicator();

        // File name
        $line .= str_pad($headerRecord->getFileName(), 20, ' ', STR_PAD_RIGHT);

        // Number of messages
        $line .= str_pad($headerRecord->getNumberMessages(), 3, '0', STR_PAD_LEFT);

        $this->addLine($line);
    }

    /**
     * Create the payment header line of the file.
     * @param PaymentHeader $paymentHeader
     *
     */
    private function addPaymentHeader(PaymentHeader $paymentHeader) {
        // Indicator
        $line = $paymentHeader->getIndicator();

        // Message type
        $line .= $paymentHeader->getMessageType();

        // Number of payments
        $line .= str_pad($paymentHeader->getNumberPayments(), 4, '0', STR_PAD_LEFT);

        $this->addLine($line);
    }

    /**
     * Create the payment record line of the file.
     * @param PaymentRecord $paymentRecord
     *
     */
    private function addPaymentRecord(PaymentRecord $paymentRecord) {
        // Indicator
        $line = $paymentRecord->getIndicator();

        // Pay currency code
        $line .= $paymentRecord->getPayCurrencyCode();

        // Payment amount
        $line .= str_pad($this->getAmountFormatted($paymentRecord->getPayCurrencyCode(), $paymentRecord->getPaymentAmount()), 15, '0', STR_PAD_LEFT);

        // Value date
        $line .= $paymentRecord->getValueDate()->format('dmY');

        // Reference
        $line .= str_pad($paymentRecord->getReference(), 16, ' ', STR_PAD_RIGHT);

        // Beneficiary name
        $line .= str_pad($paymentRecord->getBeneficiaryName(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary address 1
        $line .= str_pad($paymentRecord->getBeneficiaryAddress1(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary address 2
        $line .= str_pad($paymentRecord->getBeneficiaryAddress2(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary address 3
        $line .= str_pad($paymentRecord->getBeneficiaryAddress3(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary account number
        $line .= str_pad($paymentRecord->getBeneficiaryAccountNumber(), 34, ' ', STR_PAD_RIGHT);

        // Beneficiary bank name
        $line .= str_pad($paymentRecord->getBeneficiaryBankName(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary beneficiary bank address 1
        $line .= str_pad($paymentRecord->getBeneficiaryBankAddress1(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary beneficiary bank address 2
        $line .= str_pad($paymentRecord->getBeneficiaryBankAddress2(), 35, ' ', STR_PAD_RIGHT);

        // Beneficiary beneficiary bank address 3
        $line .= str_pad($paymentRecord->getBeneficiaryBankAddress3(), 35, ' ', STR_PAD_RIGHT);

        // Purpose of remittance
        $line .= str_pad($paymentRecord->getPurposeOfRemittance(), 3, ' ', STR_PAD_RIGHT);

        // Overseas Bank Charges
        $line .= $paymentRecord->getOverseasBankCharges();

        // Remitter name
        $line .= str_pad($paymentRecord->getRemitterName(), 35, ' ', STR_PAD_RIGHT);

        // Refinance days
        $line .= str_pad($paymentRecord->getRefinanceDays(), 3, ' ', STR_PAD_RIGHT);

        // Refinance date
        $line .= $paymentRecord->getRefinanceDate()->format('dmY');

        // Additional Instructions to Beneficiary Line 1
        $line .= str_pad($paymentRecord->getAdditionalBeneficiaryInstructions1(), 35, ' ', STR_PAD_RIGHT);

        // Additional Instructions to Beneficiary Line 2
        $line .= str_pad($paymentRecord->getAdditionalBeneficiaryInstructions2(), 35, ' ', STR_PAD_RIGHT);

        // Additional Instructions to Beneficiary Line 3
        $line .= str_pad($paymentRecord->getAdditionalBeneficiaryInstructions3(), 35, ' ', STR_PAD_RIGHT);

        // Additional Instructions to Beneficiary Line 4
        $line .= str_pad($paymentRecord->getAdditionalBeneficiaryInstructions4(), 35, ' ', STR_PAD_RIGHT);

        // Additional instructions to NAB
        $line .= str_pad($paymentRecord->getAdditionalInstructionsToNab(), 275, ' ', STR_PAD_RIGHT);

        // Beneficiary bank country code
        $line .= $paymentRecord->getBeneficiaryBankCountryCode();

        // Beneficiary BIC Address (ie bank SWIFT Code)
        $line .= str_pad($paymentRecord->getBeneficiaryBicAddress(), 11, ' ', STR_PAD_RIGHT);

        // Routing type
        $line .= $paymentRecord->getRoutingType();

        // Routing code
        $line .= str_pad($paymentRecord->getRoutingCode(), 20, ' ', STR_PAD_RIGHT);

        // Originating Applicant Details 1
        $line .= str_pad($paymentRecord->getOriginatingApplicantDetails1(), 35, ' ', STR_PAD_RIGHT);

        // Originating Applicant Details 2
        $line .= str_pad($paymentRecord->getOriginatingApplicantDetails2(), 35, ' ', STR_PAD_RIGHT);

        // Originating Applicant Details 3
        $line .= str_pad($paymentRecord->getOriginatingApplicantDetails3(), 35, ' ', STR_PAD_RIGHT);

        // Originating Applicant Details 4
        $line .= str_pad($paymentRecord->getOriginatingApplicantDetails4(), 35, ' ', STR_PAD_RIGHT);

        // Number of Detail Records
        $line .= str_pad($paymentRecord->getNumberOfDetailRecords(), 3, '0', STR_PAD_LEFT);

        $this->addLine($line);
    }

    /**
     * Create the payment detail record line of the file.
     * @param PaymentDetailRecord $paymentDetailRecord
     *
     */
    private function addPaymentDetailRecord(PaymentDetailRecord $paymentDetailRecord) {
        // Payment Detail Record
        $line = $paymentDetailRecord->getIndicator();

        // Payment Method
        $line .= $paymentDetailRecord->getPaymentMethod();

        // Payment Leg Currency Code
        $line .= $paymentDetailRecord->getPaymentLegCurrencyCode();

        // Payment Leg Currency Amount
        $line .= str_pad($this->getAmountFormatted($paymentDetailRecord->getPaymentLegCurrencyCode(), $paymentDetailRecord->getPaymentLegAmount()), 15, '0', STR_PAD_LEFT);

        // FX Rate
        $line .= str_pad($paymentDetailRecord->getFxRate(), 11, '0', STR_PAD_LEFT);

        // Debit account BSB
        $line .= $paymentDetailRecord->getDebitAccountBsb();

        // Debit Account Number
        $line .= str_pad($paymentDetailRecord->getDebitAccountNumber(), 35, ' ', STR_PAD_RIGHT);

        // Debit Currency Code
        $line .= $paymentDetailRecord->getDebitCurrencyCode();

        // Debit Amount
        $line .= str_pad($this->getAmountFormatted($paymentDetailRecord->getDebitCurrencyCode(), $paymentDetailRecord->getDebitAmount()), 15, '0', STR_PAD_LEFT);

        // Refinance Indicator
        $line .= $paymentDetailRecord->getRefinanceIndicator();

        // Text to NAB for PAY account
        $line .= str_pad($paymentDetailRecord->getTextToNabForPayAccount(), 60, ' ', STR_PAD_RIGHT);

        // FEC number
        $line .= str_pad($paymentDetailRecord->getFecNumber(), 6, ' ', STR_PAD_RIGHT);

        // EFX number
        $line .= str_pad($paymentDetailRecord->getEfxNumber(), 15, ' ', STR_PAD_RIGHT);

        $this->addLine($line);
    }

    /**
     * Create the payment trailer record line of the file.
     * @param PaymentTrailerRecord $paymentTrailerRecord
     *
     */
    private function addPaymentTrailerRecord(PaymentTrailerRecord $paymentTrailerRecord) {
        // Indicator
        $line = $paymentTrailerRecord->getIndicator();

        $this->addLine($line);
    }

    /**
     * Create the payment trailer line of the file.
     * @param PaymentTrailer $paymentTrailer
     *
     */
    private function addPaymentTrailer(PaymentTrailer $paymentTrailer) {
        // Indicator
        $line = $paymentTrailer->getIndicator();

        $this->addLine($line);
    }

    /**
     * Create the file trailer record line of the file.
     * @param FileTrailerRecord $fileTrailerRecord
     *
     */
    private function addFileTrailerRecord(FileTrailerRecord $fileTrailerRecord) {
        // Indicator
        $line = $fileTrailerRecord->getIndicator();

        //File name
        $line .= str_pad($fileTrailerRecord->getFilename(), 20, ' ', STR_PAD_RIGHT);

        //Date created
        $line .= $fileTrailerRecord->getCreatedDate()->format('dmY');

        $this->addLine($line);
    }

    private function addLine($line, $crlf = true) {
        $this->abaString .= $line . ($crlf ? "\r\n" : "");
    }

    /**
     * Number scale (decimal point) value depending on currency. Refer to documentation.
     * @param string $currency
     * @param float $amount
     *
     * @return string $format
     */
    private function getAmountFormatted($currency, $amount) {
        if (in_array($currency, ['BHD', 'JOD', 'KWD', 'OMR'])) {
            $format = number_format($amount, 3, '.', '');
        } else if (in_array($currency, ['IDR', 'JPY', 'KWD', 'OMR'])) {
            $format = number_format($amount, 0, '.', '');
        } else {
            if (is_numeric($amount) && floor($amount) != $amount) { // if number if not decimal we put it as it is
                $format = number_format($amount, 2, '.', '');
            } else {
                $format = $amount;
            }
        }

        return $format;
    }
}
