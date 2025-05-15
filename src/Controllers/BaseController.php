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

    // Méthode pour y accéder facilement depuis les enfants
    public function renderWithoutLayout($response, $template, $data = [])
    {
        return $this->noLayout->render($response, $template, $data);
    }
}   