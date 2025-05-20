<?php

namespace Lucancstr\GestionChenil\Controllers;

use Slim\Views\PhpRenderer;

abstract class BaseController
{
    protected PhpRenderer $view;
    protected PhpRenderer $noLayout;

    function __construct(){
        $this->view = new PhpRenderer(__DIR__ .'/../../views', [
            'title' => 'GestionChenil',
        ]);

        $this->view->setLayout("layout.php");
        $this->noLayout = new PhpRenderer(__DIR__ . '/../../views');
    }

    /**
     * renderWithoutLayout
     *
     * Rend une vue sans utiliser de layout, comme dans login/register.
     *
     * @param ResponseInterface $response
     * @param string $template Nom du fichier de vue à afficher
     * @param array $data Données à passer à la vue
     * @return ResponseInterface
     */
    public function renderWithoutLayout($response, $template, $data = [])
    {
        return $this->noLayout->render($response, $template, $data);
    }
}   