Aba file generation for Symfony2!
===================

Overview
-------------

This bundle was inspired from https://github.com/simonblee/aba-file-generator
Generates an aba file for bulk banking transactions with Australian banks and NAB international payment file

> **Note:**

> - This library is very new and all test cases are not accounted for. It is recommended that you run a few manual tests and validate the file with your banking institute.
> - As always, if you notice any errors please submit an issue or even better, a pull request.

License
-------------
[MIT License](https://en.wikipedia.org/wiki/MIT_License)

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require latysh/aba-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Latysh\AbaBundle\LatyshAbaBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Usage
-------------------------
```php
     /**
     * @Route("/aba_test", name="aba_test")
     */
    public function abaAction() {

        $params = array(
            'bsb' => '123-123',
            'accountNumber' => '12345678',
            'bankName' => 'CBA',
            'userName' => 'Some name',
            'remitterName' => 'From some guy',
            'directEntryUserId' => '999999',
            'description' => 'Payroll'
        );

        $t1 = new Aba\DetailRecord();
        $t1->setAccountName('John Smith');
        $t1->setAccountNumber('098765');
        $t1->setBsb('234-456');
        $t1->setAmount(345);
        $t1->setRemitter('Pepperstone');
        $t1->setTransactionCode(Aba\TransactionCode::EXTERNALLY_INITIATED_DEBIT);
        $t1->setReference('A direct debit');

        $t2 = new Aba\DetailRecord();
        $t2->setAccountName('Mary Jane');
        $t2->setAccountNumber('67832');
        $t2->setBsb('123-456');
        $t2->setAmount(8765);
        $t2->setRemitter('Hitachi');
        $t2->setTransactionCode(Aba\TransactionCode::EXTERNALLY_INITIATED_CREDIT);
        $t2->setReference('For dinner');

        $t3 = new Aba\DetailRecord();
        $t3->setAccountName('Borris Becker');
        $t3->setAccountNumber('84736');
        $t3->setBsb('098-765');
        $t3->setAmount(7546);
        $t3->setRemitter('Panasonic');
        $t3->setTransactionCode(Aba\TransactionCode::PAYROLL_PAYMENT);
        $t3->setReference('Your salary');

        $t4 = new Aba\DetailRecord();
        $t4->setAccountName('Some Dude');
        $t4->setAccountNumber('123456789');
        $t4->setBsb('888-888');
        $t4->setAmount(123456);
        $t4->setRemitter('Sony');
        $t4->setTransactionCode(Aba\TransactionCode::PAYROLL_PAYMENT);
        $t4->setReference('Your salary');

        $fd = fopen('php://temp/maxmemory:4048576', 'w');
        if ($fd === false) {
            die('Failed to open temporary file');
        }

        $abaString = $this->get('latysh_aba_file_generator')->generate($params, [$t1, $t2, $t3, $t4]); // $transaction could also be an array here
        fwrite($fd, $abaString);

        rewind($fd);
        $content = stream_get_contents($fd);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', "attachment;filename=test.aba");
        $response->setContent($content);

        return $response;
    }
```

For NAB International payment file

```php
     /**
     * @Route("/nab_aba_test", name="nab_aba_test")
     */
    public function intAbaAction() {

        $p1 = new NabInt\PaymentRecord();
        $p1->setPayCurrencyCode('AUD');
        $p1->setValueDate(new \DateTime());
        $p1->setReference('123456 WD');
        $p1->setBeneficiaryName('Altynbek Usenov');
        $p1->setBeneficiaryAddress1('Lenina 100 unit 109');
        $p1->setBeneficiaryAddress2('Naryn, Kyrgyzstan');
        $p1->setBeneficiaryAddress3('KG');
        $p1->setBeneficiaryAccountNumber('123456789');
        $p1->setBeneficiaryBankName('DEMIRBANK');
        $p1->setOverseasBankCharges('B');
        $p1->setRemitterName('Latysh and Co');
        $p1->setRefinanceDate(new \DateTime());
        $p1->setAdditionalBeneficiaryInstructions1('Please send money asap');
        $p1->setBeneficiaryBicAddress('DEMIKG22');

        $pd1 = new NabInt\PaymentDetailRecord();
        $pd1->setPaymentMethod('NFA');
        $pd1->setPaymentLegCurrencyCode('AUD');
        $pd1->setPaymentLegAmount(100.11);
        $pd1->setDebitAccountBsb('111222');
        $pd1->setDebitAccountNumber('123456789');
        $pd1->setDebitCurrencyCode('AUD');
        $pd1->setDebitAmount(100.11);
        $pd1->setRefinanceIndicator('0');

        $pd2 = new NabInt\PaymentDetailRecord();
        $pd2->setPaymentMethod('NFA');
        $pd2->setPaymentLegCurrencyCode('AUD');
        $pd2->setPaymentLegAmount(200.22);
        $pd2->setDebitAccountBsb('333444');
        $pd2->setDebitAccountNumber('123456789');
        $pd2->setDebitCurrencyCode('AUD');
        $pd2->setDebitAmount(200.22);
        $pd2->setRefinanceIndicator('0');

        $p1->setPaymentDetailRecords([$pd1, $pd2]);

        $p2 = new NabInt\PaymentRecord();
        $p2->setPayCurrencyCode('BHD');
        $p2->setValueDate(new \DateTime());
        $p2->setReference('654321 WD');
        $p2->setBeneficiaryName('Marlon Peiris');
        $p2->setBeneficiaryAddress1('Random address 222');
        $p2->setBeneficiaryAddress2('Buhra, Sri Lanka');
        $p2->setBeneficiaryAddress3('SR');
        $p2->setBeneficiaryAccountNumber('987654321');
        $p2->setBeneficiaryBankName('SRIBANK');
        $p2->setOverseasBankCharges('B');
        $p2->setRemitterName('Butterfly hunter');
        $p2->setRefinanceDate(new \DateTime());
        $p2->setAdditionalBeneficiaryInstructions1('Please send money to best designer');
        $p2->setBeneficiaryBicAddress('SRISSR55');

        $pd3 = new NabInt\PaymentDetailRecord();
        $pd3->setPaymentMethod('NFA');
        $pd3->setPaymentLegCurrencyCode('BHD');
        $pd3->setPaymentLegAmount(50.65);
        $pd3->setDebitAccountBsb('444555');
        $pd3->setDebitAccountNumber('987654321');
        $pd3->setDebitCurrencyCode('BHD');
        $pd3->setDebitAmount(50.65);
        $pd3->setRefinanceIndicator('0');

        $p2->setPaymentDetailRecords($pd3);

        $fd = fopen('php://temp/maxmemory:4048576', 'w');
        if ($fd === false) {
            die('Failed to open temporary file');
        }

        $abaString = $this->get('latysh_nab_aba_file_generator')->generate([$p1, $p2]); // $transaction could also be an array here
        fwrite($fd, $abaString);

        rewind($fd);
        $content = stream_get_contents($fd);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', "attachment;filename=nabtest.aba");
        $response->setContent($content);

        return $response;
    }

```
or you can use file_put_contents instead of streaming file contents

```php
file_put_contents('/my/aba/file.aba', $abaString);
```
Enjoy! :smile:

References
============

* http://www.anz.com/Documents/AU/corporate/clientfileformats.pdf
* http://www.cemtexaba.com/aba-format/cemtex-aba-file-format-details.html
* https://github.com/mjec/aba/blob/master/sample-with-comments.aba
* http://www.nab.com.au/content/dam/nab/business/online-banking/nab-connect/file-formats/NAB%20Connect%20Consolidated%20File%20Format%20Specification_V0.05.pdf