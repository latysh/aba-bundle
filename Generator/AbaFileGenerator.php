<?php
/**
 * Author: Latysh (Altynbek Usenov)
 */
namespace Latysh\AbaBundle\Generator;

use Latysh\AbaBundle\Model\Aba\DescriptiveRecord;
use Latysh\AbaBundle\Model\Aba\BatchControlRecord;
use Latysh\AbaBundle\Model\Aba\DetailRecord;
use Latysh\AbaBundle\Model\Aba\TransactionInterface;
use Latysh\AbaBundle\Model\Aba\TransactionCode;
use Symfony\Component\Validator\Exception\ValidatorException;

class AbaFileGenerator {

    private $validator;

    private $abaString;

    public function __construct($validator) {
        $this->validator = $validator;
        $this->abaString = '';
    }

    /**
     * @param array $params
     * @param array|TransactionInterface $detailRecords
     *
     * @return string
     */
    public function generate($params = [], $detailRecords) {
        $descriptiveRecord = new DescriptiveRecord();
        $descriptiveRecord->setBsb($params['bsb']);
        $descriptiveRecord->setAccountNumber($params['accountNumber']);
        $descriptiveRecord->setBankName($params['bankName']);
        $descriptiveRecord->setUserName($params['userName']);
        $descriptiveRecord->setDirectEntryUserId($params['directEntryUserId']);
        $descriptiveRecord->setDescription($params['description']);

        $batchControlRecord = new BatchControlRecord();

        //Descriptive record validation
        $errors = $this->validator->validate($descriptiveRecord);

        if (count($errors) > 0) {
            throw new ValidatorException('Descriptive record error: ' . (string)$errors);
        } else {
            $this->addDescriptiveRecord($descriptiveRecord);
        }

        if (!is_array($detailRecords)) {
            $detailRecords = array($detailRecords);
        }

        foreach ($detailRecords as $detailRecord) {
            //Detail record validation
            $errors = $this->validator->validate($detailRecord);

            if (count($errors) > 0) {
                throw new ValidatorException('Detail record error: ' . (string)$errors);
            } else {
                $this->addDetailRecord($detailRecord, $descriptiveRecord);
                if ($detailRecord->getTransactionCode() === TransactionCode::EXTERNALLY_INITIATED_DEBIT) {
                    $batchControlRecord->setDebitTotal($batchControlRecord->getDebitTotal() + $detailRecord->getAmount());
                } else {
                    $batchControlRecord->setCreditTotal($batchControlRecord->getCreditTotal() + $detailRecord->getAmount());
                }
            }
        }

        $batchControlRecord->setNumberRecords(count($detailRecords));
        $this->addBatchControlRecord($batchControlRecord);

        return $this->abaString;
    }

    /**
     * Create the descriptive record line of the file.
     * @param DescriptiveRecord $descriptiveRecord
     *
     *
     */
    private function addDescriptiveRecord(DescriptiveRecord $descriptiveRecord) {
        // Record Type
        $line = $descriptiveRecord->getRecordType();

        // BSB
        $line .= $descriptiveRecord->getBsb();

        // Account Number
        $line .= str_pad($descriptiveRecord->getAccountNumber(), 9, ' ', STR_PAD_LEFT);

        // Reserved - must be a single blank space
        $line .= ' ';

        // Sequence Number
        $line .= $descriptiveRecord->getSequenceNumber();

        // Bank Name
        $line .= $descriptiveRecord->getBankName();

        // Reserved - must be seven blank spaces
        $line .= str_repeat(' ', 7);

        // User Name
        $line .= str_pad($descriptiveRecord->getUserName(), 26, ' ', STR_PAD_RIGHT);

        // User ID
        $line .= $descriptiveRecord->getDirectEntryUserId();

        // File Description
        $line .= str_pad($descriptiveRecord->getDescription(), 12, ' ', STR_PAD_RIGHT);

        // Processing Date
        $line .= $descriptiveRecord->getProcessingDateTime()->format('dmy');

        // Processing Time
        $line .= $descriptiveRecord->getProcessingDateTime()->format('Hs');

        // Reserved - 36 blank spaces
        $line .= str_repeat(' ', 36);

        $this->addLine($line);
    }

    /**
     * Add a detail record for each transaction.
     *
     * @param array|DetailRecord $detailRecord
     * @param DescriptiveRecord $descriptiveRecord
     */
    private function addDetailRecord(DetailRecord $detailRecord, DescriptiveRecord $descriptiveRecord) {
        // Record Type
        $line = $detailRecord->getRecordType();

        // BSB
        $line .= $detailRecord->getBsb();

        // Account Number
        $line .= str_pad($detailRecord->getAccountNumber(), 9, ' ', STR_PAD_LEFT);

        // Indicator
        $line .= $detailRecord->getIndicator() ?: ' ';

        // Transaction Code
        $line .= $detailRecord->getTransactionCode();

        // Transaction Amount
        $line .= str_pad($detailRecord->getAmount(), 10, '0', STR_PAD_LEFT);

        // Account Name
        $line .= str_pad($detailRecord->getAccountName(), 32, ' ', STR_PAD_RIGHT);

        // Lodgement Reference
        $line .= str_pad($detailRecord->getReference(), 18, ' ', STR_PAD_RIGHT);

        // Trace BSB
        $line .= $descriptiveRecord->getBsb();

        // Trace Account Number
        $line .= str_pad($descriptiveRecord->getAccountNumber(), 9, ' ', STR_PAD_LEFT);

        // Remitter Name
        $line .= str_pad($detailRecord->getRemitter(), 16, ' ', STR_PAD_RIGHT);

        // Withholding amount
        $line .= str_pad($detailRecord->getTaxWithholding(), 8, '0', STR_PAD_LEFT);

        $this->addLine($line);
    }

    /**
     * Add batch control record for each transaction.
     *
     * @param BatchControlRecord $batchControlRecord
     */
    private function addBatchControlRecord(BatchControlRecord $batchControlRecord) {
        // Record Type
        $line = $batchControlRecord->getRecordType();

        // BSB
        $line .= $batchControlRecord->getBsb();

        // Reserved - must be twelve blank spaces
        $line .= str_repeat(' ', 12);

        // Batch Net Total
        $line .= str_pad(abs($batchControlRecord->getCreditTotal() - $batchControlRecord->getDebitTotal()), 10, '0', STR_PAD_LEFT);

        // Batch Credits Total
        $line .= str_pad($batchControlRecord->getCreditTotal(), 10, '0', STR_PAD_LEFT);

        // Batch Debits Total
        $line .= str_pad($batchControlRecord->getDebitTotal(), 10, '0', STR_PAD_LEFT);

        // Reserved - must be 24 blank spaces
        $line .= str_repeat(' ', 24);

        // Number of records
        $line .= str_pad($batchControlRecord->getNumberRecords(), 6, '0', STR_PAD_LEFT);

        // Reserved - must be 40 blank spaces
        $line .= str_repeat(' ', 40);

        $this->addLine($line, false);
    }

    private function addLine($line, $crlf = true) {
        $this->abaString .= $line . ($crlf ? "\r\n" : "");
    }
}
