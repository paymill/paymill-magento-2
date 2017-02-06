<?php

namespace Paymill\Paymill\Services;

/**
 * Paymill API wrapper for refunds resource
 */
class Refunds extends \Paymill\Paymill\Services\Base
{
    /**
     * {@inheritDoc}
     */
    protected $_serviceResource = 'refunds/';

    /**
     * General REST POST verb
     * create resource item
     *
     * @param array $itemData
     *
     * @return array created item
     */
    public function create($itemData = array())
    {
        $transactionId = $itemData['transactionId'];
        $params        = $itemData['params'];

        $result = $this->_httpClient->request(
            $this->_serviceResource . "$transactionId",
            $params,
            \Paymill\Paymill\Services\Apiclient\InterfaceCurl::HTTP_POST
        );
        return $result['data'];
    }

    /**
     * General REST DELETE verb
     * Delete or inactivate/cancel resource item
     *
     * @param string $clientId
     *
     * @return array item deleted
     */
    public function delete($identifier = null)
    {
        throw new \Paymill\Paymill\Services\Exception( __CLASS__ . " does not support " . __METHOD__, "404");
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $itemData = array())
    {
        throw new \Paymill\Paymill\Services\Exception( __CLASS__ . " does not support " . __METHOD__, "404" );
    }
}
