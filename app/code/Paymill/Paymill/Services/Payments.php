<?php

namespace Paymill\Paymill\Services;

/**
 * Paymill API wrapper for payments resource
 */
class Payments extends \Paymill\Paymill\Services\Base
{
    /**
     * {@inheritDoc}
     */
    protected $_serviceResource = 'payments/';

    /**
     * General REST PUT verb
     * Update resource item
     *
     * @param array $itemData
     *
     * @return array item updated or null
     */
    public function update(array $itemData = array())
    {
        throw new \Paymill\Paymill\Services\Exception( __CLASS__ . " does not support " . __METHOD__, "404");
    }
}
