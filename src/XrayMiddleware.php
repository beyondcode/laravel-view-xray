<?php

namespace BeyondCode\ViewXray;

use Closure;

class XrayMiddleware
{

    /** @var Xray */
    private $xray;

    public function __construct(Xray $xray)
    {
        $this->xray = $xray;
    }

   /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->xray->isEnabled()) {
            return $next($request);
        }

        $this->xray->boot();

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($response->isRedirection()) {
            return $response;
        } elseif (
            ($response->headers->has('Content-Type') &&
                strpos($response->headers->get('Content-Type'), 'html') === false)
            || $request->getRequestFormat() !== 'html'
            || $response->getContent() === false
        ) {
            return $response;
        } elseif (is_null($response->exception) && !is_null($this->xray->getBaseView())) {
            // Modify the response to add the Debugbar
            $this->injectXrayBar($response);
        }
        return $response;
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getRouteInformation($route)
    {
        if (!is_a($route, 'Illuminate\Routing\Route')) {
            return [];
        }
        $uri = head($route->methods()) . ' ' . $route->uri();
        $action = $route->getAction();

        $result = [
           'uri' => $uri ?: '-',
        ];

        $result = array_merge($result, $action);


        if (isset($action['controller']) && strpos($action['controller'], '@') !== false) {
            list($controller, $method) = explode('@', $action['controller']);
            if(class_exists($controller) && method_exists($controller, $method)) {
                $reflector = new \ReflectionMethod($controller, $method);
            }
            unset($result['uses']);
        } elseif (isset($action['uses']) && $action['uses'] instanceof \Closure) {
            $reflector = new \ReflectionFunction($action['uses']);
            $result['uses'] = $result['uses'];
        }

        if (isset($reflector)) {
            $filename = ltrim(str_replace(base_path(), '', $reflector->getFileName()), '/');
            $result['file'] = $filename . ':' . $reflector->getStartLine() . '-' . $reflector->getEndLine();
        }

        return $result;
    }

    protected function injectXrayBar($response)
    {
        $routeInformation = $this->getRouteInformation(app('router')->current());

        $content = $response->getContent();

        $xrayJs = file_get_contents(__DIR__.'/../resources/assets/xray.js');
        $xrayCss = file_get_contents(__DIR__.'/../resources/assets/xray.css');
        $xrayBar = view('xray::xray', [
            'routeInformation' => $routeInformation,
            'viewPath' => str_replace(base_path(), '', $this->xray->getBaseView()->getPath()),
            'viewName' => $this->xray->getBaseView()->name(),
        ]);

        $renderedContent = '<script>'.$xrayJs.'</script><style>'.$xrayCss.'</style>'.$xrayBar;

        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $content = substr($content, 0, $pos) . $renderedContent . substr($content, $pos);
        } else {
            $content = $content . $renderedContent;
        }
        // Update the new content and reset the content length
        $response->setContent($content);
        $response->headers->remove('Content-Length');
    }
}