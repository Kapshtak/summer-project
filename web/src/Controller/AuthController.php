<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
# Import 
use Symfony\Component\HttpFoundation\Request;


# api/src/Controller/AuthController.php

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends AbstractController
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * AuthController Constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Register new user
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request)
    {
        // $newUserData['email']    = $request->get('email');
        // $newUserData['password'] = $request->get('password');

        $newUserData = json_decode($request->getContent(), true);

        $user = $this->userRepository->createNewUser($newUserData);

        return new JsonResponse(["id" => $user->getId(), "email" => $user->getEmail(), "roles" => $user->getRoles()], Response::HTTP_CREATED);
    }

        /**
    * api route redirects
    * @return Response
    */
    public function api()
    {
        return new Response(sprintf("Logged in as %s", $this->getUser()));
    }
}
