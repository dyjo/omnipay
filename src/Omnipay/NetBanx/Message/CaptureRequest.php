<?php

/*
 * This file is part of the Omnipay package.
 *
 * (c) Adrian Macneil <adrian@adrianmacneil.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omnipay\NetBanx\Message;

/**
 * NetBanx Capture Request
 */
class CaptureRequest extends AbstractRequest
{
    /**
     * Method
     *
     * @var string
     */
    protected $txnMode = 'ccSettlement';

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('amount', 'transactionReference');

        $data = $this->getBaseData();
        $data['txnRequest'] = $this->getXmlString();

        return $data;
    }

    /**
     * Get XML string (without header)
     *
     * @return string
     */
    protected function getXmlString()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <ccPostAuthRequestV1
                    xmlns="http://www.optimalpayments.com/creditcard/xmlschema/v1"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://www.optimalpayments.com/creditcard/xmlschema/v1" />';

        $sxml = new \SimpleXMLElement($xml);

        $merchantAccount = $sxml->addChild('merchantAccount');

        $merchantAccount->addChild('accountNum', $this->getAccountNumber());
        $merchantAccount->addChild('storeID', $this->getStoreId());
        $merchantAccount->addChild('storePwd', $this->getStorePassword());

        $sxml->addChild('confirmationNumber', $this->getTransactionReference());
        $sxml->addChild('merchantRefNum', $this->getCustomerId());
        $sxml->addChild('amount', $this->getAmountDecimal());

        return $sxml->asXML();
    }
}
