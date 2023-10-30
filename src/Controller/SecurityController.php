<?php

namespace App\Controller;

use App\Classes\MailJet;
use App\Entity\User;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $manager,
        private readonly UserPasswordHasherInterface $hasher,
    )
    {}


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home.index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/confirmAccount/{token}', name: 'app_confirm_account')]
    public function confirmAccount(string $token) : ?\Symfony\Component\HttpFoundation\RedirectResponse
    {

        if($token) {

            $user = $this->userRepository->findOneBy(["confirmAccount" => $token]);

            $user->setConfirmAccount("");

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "Votre compte est confirmé ! Vous pouvez dès à présent vous connecter.");

            return $this->redirectToRoute('app_login');
        }

        return null;
    }

    /**
     * @throws Exception
     */
    #[Route('/inscription', name: 'home.register')]
    public function register(Request $request) : Response {

        if($this->getUser()) {
            return $this->redirectToRoute("home.index");
        }

        $user = new User();
        $mail = new MailJet();

        $registerForm = $this->createForm(RegisterFormType::class, $user);

        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted() && $registerForm->isValid()) {
            $token = bin2hex(random_bytes(16));

            $user->setConfirmAccount($token);
            $password = $user->getPassword();
            $hashPass = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashPass);

            $mail->send($user->getEmail(), $user->getUsername(), $token);

            $this->addFlash('success', 'Votre inscription à bien été enregistré, un mail de confirmation va être envoyé');
            $this->manager->persist($user);
            $this->manager->flush();
        }

        return $this->render('account/register.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/resetPassword", name: "home.resetPassword")]
    public function sendResetPassword(Request $request, SessionInterface $session): Response
    {
        $email = $request->request->get('email', '');

        $state = false;

        $session->start();

        $session->set('code', '');

        if($request->isMethod('POST')) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            $session->set('email', $email);

            if($user) {
                $state = true;
                $code = random_int(1000, 9999);

                $mail = new MailJet();

                if($session->get('code') === "") {
                    if($code) {
                        $mail->sendCode($user->getEmail(), $user->getUsername(), $code);

                        $session->set('code', $code);

                        return $this->redirectToRoute('home.confirmCode');
                    }
                } else {
                    return $this->redirectToRoute('home.index');
                }

            } else {
                $this->addFlash('warning', "Ce mail n'est associé à aucun compte");
            }
        }

        return $this->render("account/resetPassword.html.twig", [
            'state' => $state
        ]);
    }

    #[Route("/confirmCode", name: "home.confirmCode")]
    public function confirmCode(SessionInterface $session, Request $request) : Response {

       if($session->get('code') !== null) {
           if ($request->isMethod('POST')) {

               $code = $request->request->get('code');

               if (is_numeric($code) && $session->get('code') == $code) {

                   $session->set('code', true);

                   return $this->redirectToRoute('home.newPassword');
               } else {
                   $this->addFlash('danger', "Le code n'est pas valide !");
               }
           }
       } else {
           return $this->redirectToRoute("home.index");
       }

        return $this->render('account/confirmResetPassword.html.twig');
    }

    #[Route("/newPassword", name: "home.newPassword")]
    public function addNewPassword(Request $request, SessionInterface $session) : Response {

        if($session->get('code') === true) {
            if($request->isMethod("POST")) {
                $password = $request->request->get('password');

                $email = $session->get('email');

                $user = $this->userRepository->findOneBy(["email" => $email]);

                $hashPassword = $this->hasher->hashPassword($user, $password);

                $user->setPassword($hashPassword);
                $this->manager->persist($user);
                $this->manager->flush();

                $session->clear();

                $this->addFlash('success', "Votre mot de passe à été mis à jour !");
                return $this->redirectToRoute("app_login");
            }
        } else {
            return $this->redirectToRoute("home.index");
        }

        return $this->render('account/addNewPassword.html.twig');
    }
}
