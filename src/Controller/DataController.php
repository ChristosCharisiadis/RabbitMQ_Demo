<?php

namespace App\Controller;

use App\Repository\AttributeRepository;
use App\Repository\ClusterRepository;
use App\Repository\ConsumptionRepository;
use App\Repository\EndpointRepository;
use App\Repository\GatewayRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DataController
 * @package App\Controller
 * @Route("/data", name="data.")
 */
class DataController extends AbstractController
{
    /**
     * Fetches data from the remote API and returns in Json format.
     * @Route("/fetch", name="fetch")
     * @return JsonResponse
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function fetch()
    {
        // Fetch data from the API
        $client = HttpClient::create();
        $response = $client->request('GET', $this->getParameter('api_endpoint'));
        
        // Return the data in Json format
        return JsonResponse::fromJsonString($response->getContent());
    }

    /**
     * Retrieves all the gateways from the database.
     * @Route("/gateways", name="gateways")
     * @param GatewayRepository $gatewayRepository
     * @return Response
     */
    public function getAllGateways(GatewayRepository $gatewayRepository) {
        $gatewayArray = $gatewayRepository->findAll();
        return $this->render('data/gateway.html.twig', [
            'gatewayArray' => $gatewayArray
        ]);
    }

    /**
     * Retrieves all the profiles from the database.
     * @Route("/profiles", name="profiles")
     * @param ProfileRepository $profileRepository
     * @return Response
     */
    public function getAllProfiles(ProfileRepository $profileRepository) {
        $profileArray = $profileRepository->findAll();
        return $this->render('data/simple.html.twig', [
            'title' => 'Profiles',
            'array' => $profileArray
        ]);
    }

    /**
     * Retrieves all the endpoints from the database.
     * @Route("/endpoints", name="endpoints")
     * @param EndpointRepository $endpointRepository
     * @return Response
     */
    public function getAllEndpoints(EndpointRepository $endpointRepository) {
        $endpointArray = $endpointRepository->findAll();
        return $this->render('data/simple.html.twig', [
            'title' => 'Endpoints',
            'array' => $endpointArray
        ]);
    }

    /**
     * Retrieves all the clusters from the database.
     * @Route("/clusters", name="clusters")
     * @param ClusterRepository $clusterRepository
     * @return Response
     */
    public function getAllClusters(ClusterRepository $clusterRepository) {
        $clusterArray = $clusterRepository->findAll();
        return $this->render('data/simple.html.twig', [
            'title' => 'Clusters',
            'array' => $clusterArray
        ]);
    }

    /**
     * Retrieves all the attributes from the database.
     * @Route("/attributes", name="attributes")
     * @param AttributeRepository $attributeRepository
     * @return Response
     */
    public function getAllAttributes(AttributeRepository $attributeRepository) {
        $attributeArray = $attributeRepository->findAll();
        return $this->render('data/simple.html.twig', [
            'title' => 'Attributes',
            'array' => $attributeArray
        ]);
    }

    /**
     * Retrieves all the consumptions from the database.
     * @Route("/consumptions", name="consumptions")
     * @param ConsumptionRepository $consumptionRepository
     * @return Response
     */
    public function getAllConsumptions(ConsumptionRepository $consumptionRepository) {
        $consumptionArray = $consumptionRepository->findAll();
        return $this->render('data/consumption.html.twig', [
            'consumptionArray' => $consumptionArray
        ]);
    }
}
