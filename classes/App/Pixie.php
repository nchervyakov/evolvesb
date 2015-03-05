<?php

namespace App;

use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\EventDispatcher\EventDispatcher;
use App\EventDispatcher\Events;
use App\Events\Events as AppEvents;
use App\Events\GetResponseEvent;
use App\Exception\HttpException;
use App\Exception\NotFoundException;
use App\Helpers\Thumber;
use App\Installation\Installer;
use App\Payment\PaymentService;
use PaymentTest\PaymentTest;
use PaymentTest\TestFactory;
use PHPixie\Controller;
use PHPixie\Cookie;
use PHPixie\Exception\PageNotFound;
use PHPixie\ORM;
use VulnModule\Config\ModelInfoRepository;
use VulnModule\VulnInjection;

/**
 * Pixie dependency container
 *
 * @method Pixie bootstrap
 * @property-read \PHPixie\DB $db Database module
 * @property-read \PHPixie\ORM $orm ORM module
 * @property-read \PHPixie\Auth $auth Auth module
 * @property-read VulnInjection $vulninjection Vulninjection module
 * @property-read \PHPixie\Email $email Email module
 * @property-read Request $request Request instance
 * @property-read Response $response Request instance
 * @property-read Debug $debug Debug object
 * @property-read VulnInjection\Service $vulnService Debug object
 * @property-read ModelInfoRepository $modelInfoRepository
 * @property-read EventDispatcher $dispatcher
 * @property-read Cookie $cookie
 * @property-read \PHPixie\Paginate $paginate
 * @property-read Config $config
 * @property-read Installer $installer
 * @property-read Thumber $thumb
 * @property-read PaymentService $payments
 * @property-read PaymentTest $paymentTest
 * @property-read TestFactory $paymentTestFactory
 * @method Controller|Rest\Controller controller
 */
class Pixie extends \PHPixie\Pixie {

    /**
     * @var VulnInjection\Service
     */
    protected $vulnService;

    protected $modules = array(
        'config'  => '\App\Core\Config',
        'db' => '\PHPixie\DB',
        'orm' => '\PHPixie\ORM',
        'auth' => '\PHPixie\Auth',
        'vulninjection' => '\VulnModule\VulnInjection',
		'email' => '\PHPixie\Email',
		'paginate' => '\PHPixie\Paginate',
		'paginateDB' => '\App\Paginate\Paginate',
		'paymentTest' => '\PaymentTest\PaymentTest',
    );

    /**
     * Constructs Pixie instance.
     */
    public function __construct()
    {
        $this->instance_classes['debug'] = '\\App\\Debug';
        $this->instance_classes['request'] = '\\App\\Core\\Request';
        $this->instance_classes['response'] = '\\App\\Core\\Response';
        $this->instance_classes['modelInfoRepository'] = '\\VulnModule\\Config\\ModelInfoRepository';
        $this->instance_classes['dispatcher'] = '\\App\\EventDispatcher\\EventDispatcher';
        $this->instance_classes['installer'] = '\\App\\Installation\\Installer';
        $this->instance_classes['thumb'] = '\\App\\Helpers\\Thumber';
        $this->instance_classes['http'] = '\\App\\Network\\HTTPService';
        $this->instance_classes['payments'] = '\\App\\Payment\\PaymentService';
        $this->instance_classes['paymentTestFactory'] = '\\PaymentTest\\TestFactory';
    }

    /**
     * Add named instances by hand.
     * @param $name
     * @param $class
     */
    public function addInstanceClass($name, $class)
    {
        $this->instance_classes[$name] = $class;
    }

    /**
     * @inheritdoc
     */
    protected function after_bootstrap() {
		//Whatever code you want to run after bootstrap is done.
        $displayErrors = $this->getParameter('parameters.display_errors');
        $this->debug->display_errors = is_bool($displayErrors) ? $displayErrors : true;

        $this->dispatcher->addListener(Events::KERNEL_PRE_EXECUTE, '\\App\\Rest\\KernelEventListeners::restRouteHandler');
        $this->dispatcher->addListener(Events::KERNEL_PRE_EXECUTE, '\\App\\Admin\\EventListeners::hasAccessListener');

        $this->dispatcher->addListener(Events::KERNEL_PRE_HANDLE_EXCEPTION, '\\App\\Admin\\EventListeners::redirectUnauthorized');

        $this->dispatcher->addListener('PRE_REMOVE_ENTITY', '\\App\\Model\\Role::roleRemoveListener');

        $this->dispatcher->addListener(AppEvents::PAYMENT_OPERATION_COMPLETED, '\\App\\Events\\PaymentListeners::onOperationCompleted');
        $this->dispatcher->addListener(AppEvents::PAYMENT_OPERATION_SUCCEEDED, '\\App\\Events\\PaymentListeners::onOperationSucceeded');
        $this->dispatcher->addListener(AppEvents::PAYMENT_OPERATION_FAILED, '\\App\\Events\\PaymentListeners::onOperationFailed');
        $this->dispatcher->addListener(AppEvents::PAYMENT_PAYED, '\\App\\Events\\PaymentListeners::onPaymentPayed');
        $this->dispatcher->addListener(AppEvents::PAYMENT_PAYED, '\\App\\Events\\PaymentListeners::onPaymentRefunded');
        $this->dispatcher->addListener(AppEvents::ORDER_PAYED, '\\App\\Events\\PaymentListeners::onOrderPayed');
        $this->dispatcher->addListener(AppEvents::ORDER_REFUNDED, '\\App\\Events\\PaymentListeners::onOrderRefunded');
        $this->dispatcher->addListener(AppEvents::ORDER_STATUS_CHANGED, '\\App\\Events\\PaymentListeners::onOrderStatusChanged');
        $this->dispatcher->addListener(AppEvents::PRODUCT_STATUS_CHANGED, '\\App\\Events\\ProductListeners::onProductStatusChanged');
	}

    /**
     * @inheritdoc
     */
    public function handle_http_request()
    {
        $request = null;
        try {
            $request =  $this->http_request();
            $response = $request->execute();
            $response->send_headers()->send_body();

        } catch (PageNotFound $e) {
            $e = new NotFoundException('Not Found', 404, $e);
            $e->setParameter('request', $request);
            $this->handle_exception($e);

        } catch (HttpException $e) {
            $e->setParameter('request', $request);
            $this->handle_exception($e);

        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * @param \Exception $exception
     */
    public function handle_exception($exception)
    {
        if ($exception instanceof PageNotFound) {
            $exception = new NotFoundException('', 404, $exception);
        }

        if (!($exception instanceof HttpException)) {
            $this->debug->render_exception_page($exception);
        } else {
            $this->handle_error_request($exception);
        }
    }

    /**
     * Shows caught exception in a nice view.
     * @param \App\Exception\HttpException|\Exception $exception
     * @return null
     */
    public function handle_error_request(\Exception $exception) {
        try {
            $response = null;
            $request = null;
            if ($exception instanceof HttpException) {
                $request = $exception->getParameter('request');
            }

            $event = new GetResponseEvent($request, $request ? $request->getCookie() : []);
            $event->setException($exception);
            $this->dispatcher->dispatch(Events::KERNEL_PRE_HANDLE_EXCEPTION, $event);

            if ($event->getResponse()) {
                $response = $event->getResponse();
            }

            if (!$response) {
                $isAdmin = $exception instanceof HttpException
                    && $exception->getParameter('request')
                    && $exception->getParameter('request')->isAdminPath()
                    && $this->auth->has_role('admin');

                if ($isAdmin) {
                    $route_data = $this->router->match('/admin/error/' . $exception->getCode());
                } else {
                    $route_data = $this->router->match('/error/' . $exception->getCode());
                }
                $route_data['params'] = array_merge($route_data['params'], [
                    'exception' => $exception
                ]);
                $request = $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE);
                $response = $request->execute();
            }
            $response->send_headers()->send_body();

        } catch (\Exception $e) {
            $this->handle_exception($e);
        }
    }

    /**
     * Get param value without exception. Instead if value is missing, NULL is returned.
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($name, $default = null)
    {
        try {
            return $this->config->get($name);
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Creates custom implementation of Request.
     * @inheritdoc
     */
    public function request($route, $method = "GET", $post = array(), $get = array(), $param = array(), $server = array(), $cookie = array())
    {
        return new Request($this, $route, $method, $post, $get, $param, $server, $cookie);
    }

    /**
     * @inheritdoc
     * @return Response|\PHPixie\Response
     */
    public function response()
    {
        return new Response($this);
    }

    /**
     * @@inheritdoc
     * @return View|\PHPixie\View
     */
    public function view($name)
    {
        return new View($this, $this->view_helper(), $name);
    }

    /**
     * @return VulnInjection\Service
     */
    public function getVulnService()
    {
        return $this->vulnService;
    }

    /**
     * @param VulnInjection\Service $vulnService
     * @return $this
     */
    public function setVulnService($vulnService)
    {
        $this->vulnService = $vulnService;
        $this->addInstance('vulnService', $vulnService);
        return $this;
    }

    /**
     * @inheritdoc
     * @return View\Helper|\PHPixie\View\Helper
     */
    public function view_helper()
    {
        return new View\Helper($this);
    }

    /**
     * Adds new object as a dependency.
     * @param $name
     * @param $object
     */
    public function addInstance($name, $object)
    {
        $this->instances[$name] = $object;
    }

    public function isWindows()
    {
        if (stristr(php_uname('s'), 'Windows NT')) {
            return true;
        }

        return false;
    }
}
