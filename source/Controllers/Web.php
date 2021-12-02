<?php


namespace Source\Controllers;

use CoffeeCode\Router\Router;
use Source\Core\Controller;

/**
 * Class Web
 * @package Source\App
 */
class Web extends Controller
{
    private Router $router;

    /**
     * Web constructor.
     */
    public function __construct(Router $router)
    {
        parent::__construct(CONF_VIEW_PATH . CONF_VIEW_THEME);

        $this->view->data(['router' => $router]);
        $this->router = $router;
    }

    public function home()
    {
         $head = $this->seo->render(
            CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/'),
            theme('assets/images/nzooji.jpeg')
        );

        echo $this->view->render('home', [
            'head' => $head
        ]);
    }

    public function error(array $data): void
    {
        echo "erro";
    }
}
