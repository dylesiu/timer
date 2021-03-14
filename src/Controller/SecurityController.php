<?php

namespace App\Controller;

use App\Service\UserManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST", "OPTIONS"})
     */
    public function register(UserManagerService $userManagerService)
    {
        $password = $this->generateRandomString();
        $username = 'user_' . random_int(1000000,9999999);
        $userManagerService->createUser($username, $password);

        return $this->json([
            'username' => $username,
            'password' => $password,
        ]);
    }

    private  function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @Route("/login", name="login", methods={"POST", "OPTIONS", "GET"})
     */
    public function login(Request $request)
    {
        if ($request->isMethod('GET')) {
            return new RedirectResponse('/');
        }

        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(){}
}
