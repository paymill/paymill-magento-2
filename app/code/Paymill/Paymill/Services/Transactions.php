<?php

namespace Paymill\Paymill\Services;

/**
 * Paymill API wrapper for transactions resource
 */
class Transactions extends \Paymill\Paymill\Services\Base
{
    /**
     * {@inheritDoc}
     */
    protected $_serviceResource = 'transactions/';

    /**
     * General REST DELETE verb
     * Delete or inactivate/cancel resource item
     *
     * @param string $clientId
     *
     * @return array item deleted
     */
    public function delete($clientId = null)
    {
        throw new \Paymill\Paymill\Services\Exception( __CLASS__ . " does not support " . __METHOD__, "404");
    }
}
