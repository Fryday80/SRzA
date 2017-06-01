<?php
/**
 * Created by PhpStorm.
 * User: Fry
 * Date: 20.11.2016
 * Time: 02:00
 */

namespace Application\View\Helper;

use Auth\Service\AccessService;
use Zend\View\Exception\RuntimeException;
use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Helper\Url;
use Doctrine\Common\Proxy\Exception;

use Traversable;
use Zend\Mvc\ModuleRouteListener;

class MyUrl extends Url
{
    /* @var AccessService */
    private $accessService;

    function __constructor($accessService) {
        $this->accessService = $accessService;
    }
    public function __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        if (null === $this->router) {
            throw new RuntimeException('No RouteStackInterface instance provided');
        }

        if (3 == func_num_args() && is_bool($options)) {
            $reuseMatchedParams = $options;
            $options = array();
        }

        if ($name === null) {
            if ($this->routeMatch === null) {
                throw new RuntimeException('No RouteMatch instance provided');
            }

            $name = $this->routeMatch->getMatchedRouteName();

            if ($name === null) {
                throw new RuntimeException('RouteMatch does not contain a matched route name');
            }
        }

        if (!is_array($params)) {
            if (!$params instanceof Traversable) {
                throw new InvalidArgumentException(
                    'Params is expected to be an array or a Traversable object'
                );
            }
            $params = iterator_to_array($params);
        }

        if ($reuseMatchedParams && $this->routeMatch !== null) {
            $routeMatchParams = $this->routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $options['name'] = $name;

        $controller = $this->routeMatch->getParam("controller");
        $action = $this->routeMatch->getParam("action");
        if (!$this->accessService->allowed($controller, $action)) {
            return "Not Allowed";
        }

        return $this->router->assemble($params, $options);
    }


}