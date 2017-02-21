<?php
namespace Paymill\Paymill\Controller\Token;

class Total extends \Magento\Framework\App\Action\Action
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
    }
    
}
