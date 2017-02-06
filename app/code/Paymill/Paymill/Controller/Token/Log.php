<?php
namespace Paymill\Paymill\Controller\Token;

class Log extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\LoggingHelper
     */
    protected $paymillLoggingHelperHelper;

    public function __construct (\Magento\Framework\App\Action\Context $context, 
            \Paymill\Paymill\Helper\PaymentHelper $paymillPaymentHelperHelper, 
            \Paymill\Paymill\Helper\LoggingHelper $paymillLoggingHelperHelper)
    {
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->paymillLoggingHelperHelper = $paymillLoggingHelperHelper;
        parent::__construct($context);
    }

    public function execute ()
    {
        $post = $this->getRequest()->getPost();
        if (array_key_exists('error', $post) &&
                 array_key_exists('apierror', $post['error'])) {
            $this->paymillLoggingHelperHelper->log(
                    "Token creation failed for the following reason: " .
                     $post['error']['apierror'], print_r($post['error'], true));
        } else {
            $this->paymillLoggingHelperHelper->log(
                    "Token creation failed for the following reason: Unkown reason.");
        }
    }
    
}
