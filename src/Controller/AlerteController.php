<?php

namespace App\Controller;

use App\Message\AlertMessage;
use App\Service\DestinatairesManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;

class AlerteController extends AbstractController
{
    private DestinatairesManager $destManager;
    private MessageBusInterface $bus;
    private LoggerInterface $logger;

    public function __construct(DestinatairesManager $destManager, MessageBusInterface $bus, LoggerInterface $logger) 
    {
        $this->destManager = $destManager;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    #[Route('/alerter', name: 'alerter', methods: ['POST'])]
    public function alerter(Request $request): JsonResponse
    { 
        $providedKey = $request->headers->get('X-API-KEY') ?? $request->get('api_key');
        $apiKey = $this->getParameter('app.api_key');

        if (!$providedKey || $providedKey !== $apiKey) {
            $response = new JsonResponse(['error' => 'Clé API invalide ou absente'], 401);
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return $response;
        }
        
        $insee = $request->get('insee');

        if (!$insee || !preg_match('/^\d{5}$/', $insee)) {
            $response = new JsonResponse(['error' => 'Code INSEE invalide'], 400);
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return $response;
        }
        
        // Récupère les destinataires correspondant à l'insee
        $destinataires = $this->destManager->getDestinatairesByInsee($insee);

        $this->logger->info('Destinataires récupérés pour l\'INSEE: ' . $insee, [
            'destinataires' => $destinataires
        ]);

        if (empty($destinataires)) {
            $response = new JsonResponse(['error' => 'Aucun destinataire trouvé'], 404);
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return $response;
        }

        foreach ($destinataires as $destinataire) {
            $telephone = $destinataire['telephone'];
            $prenom = $destinataire['prenom'];
            $nom = $destinataire['nom'];
            $message = "Alerte météo : Bonjour $prenom $nom, restez vigilant(e) !";

            $this->bus->dispatch(new AlertMessage($telephone, $message));
        }
        
        $response = new JsonResponse(['message' => 'Alertes météo en cours d’envoi'], 200);
        $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return $response;
    }
}
