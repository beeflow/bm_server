<?php

declare(strict_types=1);

namespace BMServerBundle\Server\Controller;

use BMServerBundle\Server\Entity\Item;
use BMServerBundle\Server\Libs\ApiResponse\ApiResponseFactory;
use BMServerBundle\Server\Repository\ItemRepository;
use BMServerBundle\Server\ValueObjects\ComparisonSign;
use BMServerBundle\Server\ValueObjects\PositiveOrZeroNumber;
use BMServerBundle\Server\ValueObjects\ProductName;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

class ItemController
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ApiResponseFactory
     */
    private $apiResponseFactory;

    public function __construct(
        ItemRepository $itemRepository,
        EntityManagerInterface $objectManager,
        ApiResponseFactory $apiResponseFactory
    ) {
        $this->itemRepository = $itemRepository;
        $this->objectManager = $objectManager;
        $this->apiResponseFactory = $apiResponseFactory;
    }

    /**
     * @Route("/add", name="item_add", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $errors = [];

        $itemData = $request->request->all();

        try {
            $amount = new PositiveOrZeroNumber($itemData['amount'] ?? -1);
        } catch (UnexpectedValueException $ex) {
            $errors[] = ['name' => 'amount', 'reason' => $ex->getMessage()];
        }

        try {
            $name = new ProductName($itemData['name'] ?? '');
        } catch (UnexpectedValueException $ex) {
            $errors[] = ['name' => 'name', 'reason' => $ex->getMessage()];
        }

        if (!empty($errors)) {
            $adiProblem = $this->apiResponseFactory->createApiProblem();
            foreach ($errors as $error) {
                $adiProblem->addInvalidParam($error);
            }
            $adiProblem->setDetail('One of required parameter is incorrect. Please check Incorrect params list.');

            return $adiProblem->getResponse();
        }

        $item = new Item($name->get(), $amount->getInt());
        $this->objectManager->persist($item);
        $this->objectManager->flush();

        return $this->apiResponseFactory->createApiResponse()->setStatus(Response::HTTP_CREATED)->getResponse();
    }

    /**
     * @Route("/available", name="find_available", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function available(Request $request): JsonResponse
    {
        $result = $this->itemRepository->findAvailable();

        return $this->apiResponseFactory
            ->createApiResponse()
            ->setData($result)->getResponse();
    }

    /**
     * @Route("/unavailable", name="find_unavailable", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function unavailable(): JsonResponse
    {
        $result = $this->itemRepository->findUnAvailable();

        return $this->apiResponseFactory
            ->createApiResponse()
            ->setData($result)->getResponse();
    }

    /**
     * @Route("/find", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $itemData = $request->request->all();
        $errors = [];

        # I did not have to implement more that searching by amount :P
        try {
            $amount = new PositiveOrZeroNumber($itemData['amount']['value'] ?? -1);
        } catch (UnexpectedValueException $ex) {
            $errors[] = ['name' => 'amount', 'reason' => $ex->getMessage()];
        }

        try {
            $comparisonSign = new ComparisonSign($itemData['amount']['comparison_sign']);
        } catch (UnexpectedValueException $ex) {
            $errors[] = ['name' => 'comparison_sign', 'reason' => $ex->getMessage()];
        }

        if (!empty($errors)) {
            $adiProblem = $this->apiResponseFactory->createApiProblem();
            foreach ($errors as $error) {
                $adiProblem->addInvalidParam($error);
            }
            $adiProblem->setDetail('One of required parameter is incorrect. Please check Incorrect params list.');

            return $adiProblem->getResponse();
        }

        $items = $this->itemRepository->findByAmount($comparisonSign->get(), $amount->getInt());

        return $this->apiResponseFactory
            ->createApiResponse()
            ->setData($items)->getResponse();
    }

    /**
     * @Route("/{id}", methods={"PATCH"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $itemData = $request->request->all();

        try {
            $amount = (new PositiveOrZeroNumber($itemData['amount'] ?? -1))->getInt();
        } catch (UnexpectedValueException $ex) {
            $amount = null;
        }

        try {
            $name = (new ProductName($itemData['name'] ?? ''))->get();
        } catch (UnexpectedValueException $ex) {
            $name = null;
        }

        if (null === $name && null === $amount) {
            $apiProblem = $this->apiResponseFactory->createApiProblem();
            $apiProblem->setDetail('To update data you need to set at leas one param.');

            return $apiProblem->getResponse();
        }

        /** @var Item $item */
        $item = $this->itemRepository->find($id);

        if (null === $item) {
            $apiProblem = $this->apiResponseFactory
                ->createApiProblem()
                ->setStatus(Response::HTTP_NOT_FOUND)
                ->setTitle('Product not found.')
                ->setDetail('The product you are looking for does not exist.');

            return $apiProblem->getResponse();
        }

        $newItem = new Item(
            $name ?? $item->getName(),
            $amount ?? $item->getAmount(),
            $item->getId()
        );
        $this->objectManager->merge($newItem);
        $this->objectManager->flush();

        return $this->apiResponseFactory
            ->createApiResponse()->setStatus(Response::HTTP_NO_CONTENT)->getResponse();
    }

    /**
     * @Route("/{id}", name="get_item", methods={"GET"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $item = $this->itemRepository->find($id);

        if (!$item instanceof Item) {
            $apiProblem = $this->apiResponseFactory
                ->createApiProblem()
                ->setStatus(Response::HTTP_NOT_FOUND)
                ->setTitle('Product not found.')
                ->setDetail('The product you are looking for does not exist.');

            return $apiProblem->getResponse();
        }

        return $this->apiResponseFactory->createApiResponse()->setData([
            'name' => $item->getName(),
            'amount' => $item->getAmount(),
            'id' => $item->getId()
        ])->getResponse();
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        /** @var Item $item */
        $item = $this->itemRepository->find($id);

        if (null === $item) {
            $adiProblem = $this->apiResponseFactory
                ->createApiProblem()
                ->setStatus(Response::HTTP_NOT_FOUND)
                ->setTitle('Product not found.')
                ->setDetail('The product you are looking for does not exist.');

            return $adiProblem->getResponse();
        }

        $this->objectManager->remove($item);
        $this->objectManager->flush();

        return $this->apiResponseFactory->createApiResponse()->getResponse();
    }
}
