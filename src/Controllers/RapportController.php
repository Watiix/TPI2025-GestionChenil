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

    /**
     * showRapport
     *
     * Affiche la page du rapport. Charge les utilisateurs avec leurs animaux (potentiellement pour le rapport).
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */

    public function showRapport(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $utilisateurs = Utilisateur::getAllWithAnimaux();

        return $this->view->render($response, 'rapport.php');
    }

    /**
     * generate
     *
     * Génère un rapport PDF avec les utilisateurs, animaux et réservations, puis le sauvegarde dans le dossier tmp.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface
    {
        if($_SESSION['user']['Statut'] !== 3){
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $utilisateurs = Utilisateur::getAllWithAnimaux();
        $nbUsers = count($utilisateurs);
        $nbAnimaux = array_reduce($utilisateurs, fn($carry, $u) => $carry + count($u['animaux']), 0);
        $nbReservations = count(Reservation::getAllReservation());
        $userId = $_SESSION['user']['IdUtilisateur'];
        $contenu = "Rapport PDF chenil";

        // Génère le HTML depuis la vue
        ob_start();
        require __DIR__ . '/../../views/pdf-content.php';
        $html = ob_get_clean();
    
        try {
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

            Utilisateur::InsertRapport($contenu, $userId);
            $_SESSION['form_succes'] = "Rapport générer avec succès.";
            return $response->withHeader('Location', '/')->withStatus(302);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}