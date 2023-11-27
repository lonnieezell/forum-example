<?php

namespace App\Controllers;

use App\Libraries\Policies\Policy;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Michalsn\CodeIgniterHtmx\HTTP\IncomingRequest;
use Michalsn\CodeIgniterHtmx\HTTP\Response;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * The name of the current theme.
     * Must be within /themes directory.
     */
    protected string $theme = 'default';

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Instance of the main Response object.
     *
     * @var Response
     */
    protected $response;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Policy instance for additional authorization.
     */
    protected Policy $policy;

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = [...$this->helpers, 'alerts'];

        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();

        $this->policy = service('policy');
    }

    /**
     * Render a view file.
     *
     * Must be used in order to utilize the theme system.
     */
    protected function render(string $view, array $data = []): string
    {
        $themePath = ROOTPATH . "/themes/{$this->theme}/";
        $renderer  = single_service('renderer', $themePath);

        return $renderer
            ->setData($data)
            ->render($view);
    }
}
