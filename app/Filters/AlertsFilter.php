<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Michalsn\CodeIgniterHtmx\HTTP\IncomingRequest;
use Michalsn\CodeIgniterHtmx\HTTP\RedirectResponse;

class AlertsFilter implements FilterInterface
{
    /**
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    /**
     * @param array|null $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if ($response instanceof RedirectResponse) {
            alerts()->session();

            return null;
        }

        if (! str_contains($response->getHeaderLine('Content-Type'), 'html')) {
            return null;
        }

        /** @var IncomingRequest $request */
        if ($request->is('htmx')) {
            $body = $response->getBody();

            return $response->setBody($body . alerts()->inline());
        }
        alerts()->session();

        return null;
    }
}
