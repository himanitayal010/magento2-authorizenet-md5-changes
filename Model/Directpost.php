<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magneto\Authorizenet\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\Method\TransparentInterface;

/**
 * Authorize.net DirectPost payment method model.
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Directpost extends \Magento\Authorizenet\Model\Directpost
{
    const METHOD_CODE = 'authorizenet_directpost';

    /**
     * @var string
     */
    protected $_formBlockType = \Magento\Payment\Block\Transparent\Info::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \Magento\Payment\Block\Info::class;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isInitializeNeeded = true;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Authorizenet\Model\Directpost\Response
     */
    protected $response;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;

    /**
     * Order factory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $psrLogger;

    /**
     * @var \Magento\Sales\Api\PaymentFailuresInterface
     */
    private $paymentFailures;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * Validate response data. Needed in controllers.
     *
     * @return bool true in case of validation success.
     * @throws \Magento\Framework\Exception\LocalizedException In case of validation error
     */
    public function validateResponse()
    {
        $response = $this->getResponse();
        $hashConfigKey = !empty($response->getData('x_SHA2_Hash')) ? 'signature_key' : 'trans_md5';
        //md5 check
        if (!$response->isValidHash($this->getConfigData($hashConfigKey), $this->getConfigData('login'))
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The transaction was declined because the response hash validation failed.')
            );
        }
        return true;
    }
}
