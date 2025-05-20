<?php
namespace Lucancstr\GestionChenil\Controllers;

use Lucancstr\GestionChenil\Models\Animal;
use Lucancstr\GestionChenil\Models\Utilisateur;
use Lucancstr\GestionChenil\Models\Reservation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Mpdf\Mpdf;


class RapportController extends BaseController {

    public function showRapport(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $utilisateurs = Utilisateur::getAllWithAnimaux();


        return $this->view->render($response, 'rapport.php');
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        $utilisateurs = Utilisateur::getAllWithAnimaux(); // Structure : user + animaux[]
        $nbUsers = count($utilisateurs);
        $nbAnimaux = array_reduce($utilisateurs, fn($carry, $u) => $carry + count($u['animaux']), 0);
        $nbReservations = count(Reservation::getAllReservation());
        // Génère le HTML depuis la vue
        ob_start();
        require __DIR__ . '/../../views/pdf-content.php';
        $html = ob_get_clean();
    
        // Crée le PDF et le sauve dans le dossier tmp
        $mpdf = new Mpdf([
            'default_font' => 'DejaVuSans',
            'tempDir' => __DIR__ . '/../../tmp'
        ]);
    
        $mpdf->WriteHTML($html);
    
        // Définir le chemin de sortie
        $pdfPath = __DIR__ . '/../../tmp/rapport.pdf';
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE); // <-- Sauvegarde dans le fichier
    
        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'pdf' => '/tmp/rapport.pdf'
        ]));


        $_SESSION['form_succes'] = "Rapport générer avec succès.";
        return $response->withHeader('Location', '/')->withStatus(302);
        
    }
}