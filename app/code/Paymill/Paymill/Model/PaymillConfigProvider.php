<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Paymill\Paymill\Model;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Paymill\Paymill\Model\Method\MethodModelCreditcard as Creditcard;
use Paymill\Paymill\Model\Method\MethodModelDirectdebit as Directdebit;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Paymill\Paymill\Helper\OptionHelper;
use Magento\Framework\App\RequestInterface;
use Paymill\Paymill\Helper\PaymentHelper as PaymentHelperTotal; 


class PaymillConfigProvider implements ConfigProviderInterface
{

    /** @var PaymentConfig */
    protected $config;

    /**
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     *
     * @var Repository
     */
    protected $assetRepo;

    /**
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     *
     * @var \Paymill\Paymill\Helper\PaymentHelper
     */
    protected $paymillPaymentHelperHelper;

    /**
     *
     * @var \Paymill\Paymill\Helper\OptionHelper
     */
    protected $paymillOptionHelperHelper;

    /**
     * This var is used for the Branddetection, if empty all will be shown, else
     * only the selected ones
     *
     * @var string
     */
    private $creditCardLogosBrand = array();

    /**
     * This var is used to show the logos in the checkout, if empty none will be
     * shown
     *
     * @var string
     */
    private $creditCardLogosDisplay = '';

    /**
     *
     * @var string[]
     */
    protected $methodCodes = [
            Creditcard::PAYMENT_METHOD_CREDITCARD_CODE,
            Directdebit::PAYMENT_METHOD_DIRECTDEBIT_CODE
    ];

    /**
     *
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    protected $methods = [];

    /**
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     *
     * @param PaymentHelper $paymentHelper            
     * @param Escaper $escaper            
     * @param PaymentConfig $paymentConfig            
     * @param UrlInterface $urlBuilder            
     */
    public function __construct (PaymentHelper $paymentHelper, Escaper $escaper, 
            PaymentConfig $paymentConfig, UrlInterface $urlBuilder, 
            Repository $assetRepo, OptionHelper $paymillOptionHelperHelper, 
            PaymentHelperTotal $paymillPaymentHelperHelper,
            RequestInterface $request)
    {
        $this->escaper = $escaper;
        $this->config = $paymentConfig;
        $this->urlBuilder = $urlBuilder;
        $this->assetRepo = $assetRepo;
        $this->paymillOptionHelperHelper = $paymillOptionHelperHelper;
        $this->paymillPaymentHelperHelper = $paymillPaymentHelperHelper;
        $this->request = $request;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
        $this->setPaymillCcLogos();
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getConfig ()
    {
        $config = [];
        foreach ($this->methodCodes as $code) {
            if ($this->methods[$code]->isAvailable()) {
                $config['payment']['getPublicKey'][$code] = $this->getPublicKey(
                        $code);
                $config['payment']['getTokenSelector'][$code] = $this->getTokenSelector(
                        $code);
                $config['payment']['isFastCheckout'][$code] = $this->isFastCheckout(
                        $code);
                $config['payment']['getCheckoutDesc'][$code] = $this->getCheckoutDesc(
                        $code);
                $config['payment']['isInDebugMode'][$code] = $this->isInDebugMode(
                        $code);
                $config['payment']['getTokenLog'][$code] = $this->getTokenLog();
                if ($code === Directdebit::PAYMENT_METHOD_DIRECTDEBIT_CODE) {
                    $config['payment']['getPaymentEntry'][$code]['holder'] = $this->getPaymentEntry(
                            $code, 'holder');
                    $config['payment']['getPaymentEntryElv'][$code] = $this->getPaymentEntryElv(
                            $code);
                }
                if ($code === Creditcard::PAYMENT_METHOD_CREDITCARD_CODE) {
                    $config['payment']['getPaymentEntry'][$code]['card_holder'] = $this->getPaymentEntry(
                            $code, 'card_holder');
                    $config['payment']['getPaymentEntry'][$code]['cc_number'] = $this->getPaymentEntry(
                            $code, 'cc_number');
                    $config['payment']['getPaymentEntry'][$code]['cvc'] = $this->getPaymentEntry(
                            $code, 'cvc');
                    $config['payment']['getPaymentEntry'][$code]['expire_month'] = $this->getPaymentEntry(
                            $code, 'expire_month');
                    $config['payment']['getPaymentEntry'][$code]['expire_year'] = $this->getPaymentEntry(
                            $code, 'expire_year');
                    $config['payment']['getSpecificCreditcard'][$code] = $this->getSpecificCreditcard();
                    $config['payment']['getCreditCardLogosBrand'][$code] = $this->getCreditCardLogosBrand();
                    $config['payment']['getPaymillCcMonths'][$code] = $this->getPaymillCcMonths(
                            $code);
                    $config['payment']['getPaymillCcYears'][$code] = $this->getPaymillCcYears(
                            $code);
                    $config['payment']['getTokenUrl'][$code] = $this->getTokenUrl();
                    $config['payment']['getPaymentTotal'][$code] = $this->getPaymentTotal();
                    $config['payment']['getCurrency'][$code] = $this->getCurrency(
                            $code);
                    $config['payment']['getCustomerEmail'][$code] = $this->getCustomerEmail(
                            $code);
                    $config['payment']['getPci'][$code] = $this->getPci($code);
                    $config['payment']['getCvvImageUrl'][$code] = $this->getCvvImageUrl();
                }
            }
        }
        return $config;
    }

    private function getPaymentEntry ($method, $nameField)
    {
        return $this->methods[$method]->getPaymentEntry($method, $nameField);
    }

    private function getPublicKey ($method)
    {
        return $this->methods[$method]->getPublicKey();
    }

    private function getPaymentEntryElv ($method)
    {
        return $this->methods[$method]->getPaymentEntryElv($method);
    }

    private function isInDebugMode ($method)
    {
        return $this->methods[$method]->isInDebugMode();
    }

    private function getCheckoutDesc ($method)
    {
        return $this->methods[$method]->getCheckoutDesc($method);
    }

    private function isFastCheckout ($method)
    {
        return $this->methods[$method]->isFastCheckout($method);
    }

    private function getTokenSelector ($method)
    {
        return $this->methods[$method]->getTokenSelector();
    }

    private function getSpecificCreditcard ()
    {
        return $this->creditCardLogosDisplay;
    }

    public function getCreditCardLogosBrand ()
    {
        return $this->creditCardLogosBrand;
    }

    private function getPci ($method)
    {
        return $this->methods[$method]->getPci();
    }

    private function getCurrency ($method)
    {
        return $this->methods[$method]->getCurrency();
    }

    private function getCustomerEmail ($method)
    {
        return $this->methods[$method]->getCustomerEmail();
    }

    private function getPaymentTotal()
    {
        return  $this->paymillPaymentHelperHelper->getAmount();
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    private function getPaymillCcMonths ($method)
    {
        return $this->config->getMonths();
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    private function getPaymillCcYears ($method)
    {
        return $this->config->getYears();
    }

    protected function getTokenUrl ()
    {
        return $this->urlBuilder->getUrl('paymill/token/total', 
                array(
                        '_secure' => true
                ));
    }

    protected function getTokenLog ()
    {
        return $this->urlBuilder->getUrl('paymill/token/log', 
                array(
                        '_secure' => true
                ));
    }

    /**
     * Create a file asset that's subject of fallback system
     *
     * @param string $fileId            
     * @param array $params            
     * @return \Magento\Framework\View\Asset\File
     */
    public function createAsset ($fileId, array $params = [])
    {
        $params = array_merge([
                '_secure' => $this->request->isSecure()
        ], $params);
        return $this->assetRepo->createAsset($fileId, $params);
    }

    private function setPaymillCcLogos ()
    {
        $cards = explode(',', 
                $this->paymillOptionHelperHelper->getSpecificCreditcard());
        $this->creditCardLogosDisplay = '';
        $this->creditCardLogosBrand = array();
        $temp = array();
        if ($this->paymillOptionHelperHelper->showSpecificCreditCard()) {
            foreach ($cards as $card) {
                $asset = $this->createAsset(
                        'Paymill_Paymill::images/icon_32x20_' . $card . '.png');
                $this->creditCardLogosDisplay .= sprintf(
                        '<img style="display: inline" src="%s" alt="%s"/>', 
                        $asset->getUrl(), $card);
                
                array_push($temp, $card);
            }
        }
        $this->creditCardLogosBrand = $temp;
    }

    /**
     * Retrieve CVV tooltip image url
     *
     * @return string
     */
    public function getCvvImageUrl ()
    {
        return $this->getViewFileUrl('Magento_Checkout::cvv.png');
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }
  

}
