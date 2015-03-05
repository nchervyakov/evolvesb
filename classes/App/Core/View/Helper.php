<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.08.2014
 * Time: 16:43
 */


namespace App\Core\View;
use App\Core\BaseController;
use App\Model\Order;
use App\Model\PaymentOperation;
use App\Page;
use App\Pixie;
use PHPixie\Paginate\Pager as Pager;
use VulnModule\VulnInjection;


/**
 * Class Helper
 * @property-read Pixie pixie
 * @package App\Core\View
 */
class Helper extends \PHPixie\View\Helper
{
    protected $orderStatusLabelMapping = [
        Order::STATUS_NEW => 'label-default',
        Order::STATUS_COMPLETED => 'label-success',
        Order::STATUS_WAITING_PAYMENT => 'label-warning',
        Order::STATUS_PROCESSING => 'label-primary',
        Order::STATUS_SHIPPING => 'label-primary',
        Order::STATUS_CANCELLED => 'label-danger',
        Order::STATUS_REFUNDED => 'label-danger'
    ];

    protected $paymentOpTypes = [
        PaymentOperation::TR_TYPE_AUTHORIZED_PAYMENT => "Авторизованный платёж",
        PaymentOperation::TR_TYPE_IMMEDIATE_PAYMENT => "Мгновенный платёж",
        PaymentOperation::TR_TYPE_PAYMENT_COMPLETION => "Завершение платёжа",
        PaymentOperation::TR_TYPE_AUTH_CANCELLATION => "Отмена платёжа",
        PaymentOperation::TR_TYPE_REFUND => "Возврат денег",
    ];

    protected $paymentOpTypesComplete = [
        PaymentOperation::TR_TYPE_AUTHORIZED_PAYMENT => "Блок",
        PaymentOperation::TR_TYPE_IMMEDIATE_PAYMENT => "Оплата",
        PaymentOperation::TR_TYPE_PAYMENT_COMPLETION => "Подтверждение",
        PaymentOperation::TR_TYPE_AUTH_CANCELLATION => "Отмена авторизации",
        PaymentOperation::TR_TYPE_REFUND => "Отмена",
    ];

    /**
     * @var BaseController|Page
     */
    protected $controller;

    protected $aliases = array(
        '_' => 'output',
        '_esc' => 'escape',
        '_token' => 'token',
        '_dump' => 'dump',
        '_order_status' => 'order_status',
        '_format_order_status' => 'formatOrderStatus',
        '_pager' => 'pager',
        '_addToCartLink' => 'addToCartLink',
        '_trim' => 'trim',
        '_local_date' => 'localDate',
        '_sidebar_menu' => 'sidebarMenu',
        '_format_price' => 'formatPrice',
        '_hl_search' => 'highliteSearchResults',
        '_format_payment_op' => 'formatPaymentOperation',
        '_format_payment_op_complete' => 'formatPaymentOperationComplete',
        '_format_payment_result' => 'formatPaymentResult',
    );

    /**
     * @var string Current currency of products
     */
    protected $currency;

    /**
     * @inheritdoc
     * @param Pixie $pixie
     */
    public function __construct($pixie)
    {
        parent::__construct($pixie);
    }

    /**
     * @inheritdoc
     */
    public function escape($str, $fieldName = null)
    {
        $service = $this->pixie->getVulnService();

        if (!$fieldName || !$service) {
            return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
        }

        $vulns = $service->getConfig()->getVulnerabilities();

        $xss = $vulns['xss'];
        $fields = $service->getConfig()->getFields();
        $field = $fields[$fieldName];

        if ((!isset($xss['enabled']) || $xss['enabled'] == true) && is_array($field) && in_array('xss', $field)) {
            return $str;
        }

        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @inheritdoc
     */
    public function output($str, $fieldName = null)
    {
        echo $this->escape($str, $fieldName);
    }

    /**
     * Render hidden CSRF field.
     * @param $tokenId
     * @param bool $refresh
     */
    public function token($tokenId, $refresh = true)
    {
        $service = $this->pixie->getVulnService();

        if (!$service || $service->csrfIsEnabled()) {
            echo '';
            return;
        }
        echo $service->renderTokenField(Page::TOKEN_PREFIX . $tokenId, $refresh);
    }

    /**
     * Dump all passed vars to output.
     */
    public function dump()
    {
        echo call_user_func_array('App\\Debug::dump', func_get_args());
    }

    /**
     * Generates Bootstrap label for order status.
     * @param $status
     * @return string
     */
    public function order_status($status)
    {
        $canonicalStatus = strtolower(trim($status));
        $label = isset($this->orderStatusLabelMapping[$canonicalStatus])
            ? $this->orderStatusLabelMapping[$canonicalStatus] : 'label-default';
        return '<span class="label ' . $label . '">' . htmlspecialchars($this->formatOrderStatus($status), ENT_COMPAT, 'UTF-8') . '</span>';
    }

    /**
     * Renders Bootstrap pager based on PHPixies Paginate module.
     * @param $pager Pager
     * @param string $linkTemplate
     */
    public function pager($pager, $linkTemplate = '/?page=#page#')
    {
        if (!($pager instanceof Pager)) {
            return;
        }
        if ($linkTemplate) {
            $pager->set_url_pattern($linkTemplate);
        }
        if ($pager->num_pages > 1) { ?>
            <ul class="pagination pull-right clearfix">
                <li class="previous <?php if ($pager->page == 1): ?>disabled<?php endif; ?>"><a
                        href="<?php echo $pager->url($pager->page > 1 ? $pager->page - 1 : 1); ?>">&laquo;</a></li>
                <?php for ($page = 1; $page <= $pager->num_pages; $page++): ?>
                    <li <?php if ($page == $pager->page): ?>class="active"<?php endif; ?>><a href="<?php echo $pager->url($page); ?>"><?php echo $page; ?></a></li>
                <?php endfor; ?>
                <li class="next <?php if ($pager->page == $pager->num_pages): ?>disabled<?php endif; ?>"><a
                        href="<?php echo $pager->url($pager->page < $pager->num_pages ? $pager->page + 1 : $pager->num_pages); ?>">&raquo;</a></li>
            </ul> <?php
        }
    }

    public function addToCartLink($productId)
    {
        static $productsInCart;
        if (!$productsInCart) {
            $productsInCart = $this->controller->getProductsInCartIds();
        }
        if (!in_array($productId, $productsInCart)) { ?>
            <a href="/cart/view" class="btn btn-primary pull-left ladda-button buy-link js-add-to-cart-shortcut"
               data-product-id="<?php echo $productId; ?>"
               data-style="contract" title="Add to Cart"><span class="ladda-label"><span
                            class="glyphicon glyphicon-shopping-cart"></span></span></a><?php
        } else {
            ?><a href="/cart/view" class="btn btn-success btn-sm pull-left ladda-button buy-link added-to-cart" data-product-id="<?php echo $productId; ?>"
                    data-style="contract" title="Go to Cart">Added to Cart</a> <?php
        }
    }

	public function trim($string, $trimLength = 40) {
		echo $this->_smartTrim($string, $trimLength);
	}
	
	private function _smartTrim($text, $max_len, $trim_middle = false, $trim_chars = '...')
	{
		$text = trim($text);

		if (strlen($text) < $max_len) {

			return $text;

		} elseif ($trim_middle) {

			$hasSpace = strpos($text, ' ');
			if (!$hasSpace) {
				/**
				 * The entire string is one word. Just take a piece of the
				 * beginning and a piece of the end.
				 */
				$first_half = substr($text, 0, $max_len / 2);
				$last_half = substr($text, -($max_len - strlen($first_half)));
			} else {
				/**
				 * Get last half first as it makes it more likely for the first
				 * half to be of greater length. This is done because usually the
				 * first half of a string is more recognizable. The last half can
				 * be at most half of the maximum length and is potentially
				 * shorter (only the last word).
				 */
				$last_half = substr($text, -($max_len / 2));
				$last_half = trim($last_half);
				$last_space = strrpos($last_half, ' ');
				if (!($last_space === false)) {
					$last_half = substr($last_half, $last_space + 1);
				}
				$first_half = substr($text, 0, $max_len - strlen($last_half));
				$first_half = trim($first_half);
				if (substr($text, $max_len - strlen($last_half), 1) == ' ') {
					/**
					 * The first half of the string was chopped at a space.
					 */
					$first_space = $max_len - strlen($last_half);
				} else {
					$first_space = strrpos($first_half, ' ');
				}
				if (!($first_space === false)) {
					$first_half = substr($text, 0, $first_space);
				}
			}

			return $first_half.$trim_chars.$last_half;

		} else {

			$trimmed_text = substr($text, 0, $max_len);
			$trimmed_text = trim($trimmed_text);
			if (substr($text, $max_len, 1) == ' ') {
				/**
				 * The string was chopped at a space.
				 */
				$last_space = $max_len;
			} else {
				/**
				 * In PHP5, we can use 'offset' here -Mike
				 */
				$last_space = strrpos($trimmed_text, ' ');
			}
			if (!($last_space === false)) {
				$trimmed_text = substr($trimmed_text, 0, $last_space);
			}
			return $this->_removeTrailingPunctuation($trimmed_text).$trim_chars;

		}

	}

	/**
	 * Strip trailing punctuation from a line of text.
	 *
	 * @param  string $text The text to have trailing punctuation removed from.
	 *
	 * @return string       The line of text with trailing punctuation removed.
	 */
	private function _removeTrailingPunctuation($text)
	{
		return preg_replace("'[^a-zA-Z_0-9]+$'s", '', $text);
	}

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param $date
     * @param string $lang
     * @return bool|mixed|string
     */
    public function localDate($date, $lang = 'en')
    {
        $ru_month = array( 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря' );
        $en_month = array( 'January', 'February', 'March', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );

        $date = is_numeric($date) ? $date : strtotime($date);

        if ($lang == 'ru') {
            return str_replace($en_month, $ru_month, date('j F, Y', $date));
        } else {
            return date('F j, Y', $date);
        }
    }

    public function sidebarMenu($sidebarLinks)
    {
        $this->renderSidebarMenuLevel($sidebarLinks);
    }

    protected function renderSidebarMenuLevel($sidebarLinks)
    {
        $baseLen = strlen('/admin');
        $hasActive = false;

        foreach ($sidebarLinks as $sbLink => $sbLinkData) {
            $isLinkActive = strlen($sbLink) <= $baseLen && $_SERVER['REQUEST_URI'] == $sbLink
                || ((strlen($sbLink) > $baseLen) && (strpos($_SERVER['REQUEST_URI'], $sbLink) === 0)
                    && (strlen($sbLink) >= strlen($_SERVER['REQUEST_URI'])) );
            if ($isLinkActive) {
                $hasActive = true;
            }

            if (array_key_exists('items', $sbLinkData)) {
                ob_start();
                $hasActive = $this->renderSidebarMenuLevel($sbLinkData['items']);
                $itemsHtml = ob_get_clean();
                ?>
                <li class="<?php echo $hasActive ? 'active' : ''; ?>">
                    <a href="#" class="<?php echo $hasActive ? 'active' : ''; ?>"><i class="<?php $this->output($sbLinkData['link_class']); ?>"></i>
                        <?php $this->output($sbLinkData['label']); ?><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <?php echo $itemsHtml; ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li><?php

            } else { ?>
                <li>
                <a href="<?php echo $sbLink; ?>" class="<?php echo $isLinkActive ? 'active' : ''; ?>"
                    ><i class="<?php $this->output($sbLinkData['link_class']); ?>"></i><?php $this->output($sbLinkData['label']); ?></a>
                </li><?php
            }
        }

        return $hasActive;
    }

    /**
     * @param $price
     * @param null $template
     * @param string $currency
     * @return string
     */
    public function formatPrice($price, $template = null, $currency = null)
    {
        if (!$currency) {
            if ($this->currency) {
                $currency = $this->currency;

            } else {
                $paymentConfig = $this->pixie->config->get('payment');
                $currency = $paymentConfig['currency'];
                $this->currency = $currency;
            }
        }

        $priceCanonical = number_format((float)$price, 2, '.', ',');
        $replaces = [
            '%PRICE%' => $priceCanonical,
            '%CURRENCY%' => $currency
        ];

        if ($currency == 'RUR') {
            $replaces['%SYMBOL%'] = 'руб.';
            $replaces['%SYMBOL_PRICE%'] = trim($replaces['%PRICE%'] . ' ' . $replaces['%SYMBOL%']);

        } else if ($currency == 'USD') {
            $replaces['%SYMBOL%'] = '$';
            $replaces['%SYMBOL_PRICE%'] = trim($replaces['%SYMBOL%'] . $replaces['%PRICE%']);

        } else {
            $replaces['%SYMBOL%'] = '';
            $replaces['%SYMBOL_PRICE%'] = $replaces['%PRICE%'];
        }
        $template = $template ?: '%SYMBOL_PRICE%';
        return trim(str_replace(array_keys($replaces), array_values($replaces), $template));
    }

    public function highliteSearchResults($text, array $words)
    {
        $patterns = [];
        foreach ($words as $word) {
            $patterns[] = preg_quote($word);
        }
        $patterns = implode('|', $patterns);
        $text = preg_replace('/('.$patterns.')/ims', '<strong class="highlight">$1</strong>', $text);
        return $text;
    }

    /**
     * @param string $status Original order status
     * @return string Formatted status
     */
    public function formatOrderStatus($status)
    {
        $names = [
            Order::STATUS_NEW => 'новый',
            Order::STATUS_WAITING_PAYMENT => 'ожидание оплаты',
            Order::STATUS_SHIPPING => 'доставляется',
            Order::STATUS_COMPLETED => 'завершён',
            Order::STATUS_CANCELLED => 'отменён',
            Order::STATUS_PROCESSING => 'в обработке',
            Order::STATUS_REFUNDED => 'платёж возвращён'
        ];

        if (isset($names[$status])) {
            return $names[$status];
        } else {
            return 'Неизвестен';
        }
    }

    public function formatPaymentOperation($operationType)
    {
        return $this->paymentOpTypes[$operationType];
    }

    public function formatPaymentOperationComplete($operationType)
    {
        $operationType = (int) trim($operationType);
        return $operationType . ' ' . $this->paymentOpTypesComplete[$operationType];
    }

    public function formatPaymentResult($resultCode)
    {
        $errors = $this->pixie->config->get('payment_test.errors');
        return trim($resultCode) . '-' . $errors['rc_detail'][(int) trim($resultCode)];
    }
}