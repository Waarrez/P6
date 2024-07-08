<?php

namespace App\Controller;

use App\Services\MailerService;
use App\Entity\User;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $manager,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly MailerService $mailer
    )
    {}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
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

    /**
     * @throws Exception|TransportExceptionInterface
     */
    #[Route('/register', name: 'home.register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute('home.index');
        }

        $user = new User();
        $registerForm = $this->createForm(RegisterFormType::class, $user);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            try {
                $token = bin2hex(random_bytes(16));
                $user->setConfirmAccount($token);

                $hashedPassword = $this->hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);

                $this->mailer->sendEmail(
                    $user->getEmail(),
                    "Inscription sur snowtricks",
                    "Merci pour votre inscription ! Voici le lien pour confirmer votre compte",
                    $token
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre inscription a bien été enregistrée. Un email de confirmation va être envoyé.');
                return $this->redirectToRoute('home.index'); // Redirection après succès
            } catch (UniqueConstraintViolationException $e) {
                if (str_contains($e->getMessage(), 'email')) {
                    $this->addFlash('error', 'Cet email est déjà utilisé.');
                } elseif (str_contains($e->getMessage(), 'username')) {
                    $this->addFlash('error', 'Ce nom d\'utilisateur est déjà pris.');
                } else {
                    $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.');
                }
            } catch (Exception $e) {
                dump($e->getMessage());
                $this->addFlash('error', "Une erreur est survenue lors de votre inscription. Veuillez réessayer.");
            }
        } else {
            $this->addFormErrorsToFlash($registerForm);
        }

        return $this->render('account/register.html.twig', [
            'registerForm' => $registerForm->createView(),
        ]);
    }

    #[Route(path: '/confirmAccount/{token}', name: 'app_confirm_account')]
    public function confirmAccount(string $token): ?Response
    {
        if ($token === '' || $token === '0') {
            return $this->redirectToRoute('home.index');
        }

        $user = $this->userRepository->findOneBy(["confirmAccount" => $token]);

        if ($user === true) {
            $this->addFlash("error", "Le lien n'est plus disponible.");
            return $this->redirectToRoute('home.index');
        }

        $user->setConfirmAccount("");

        $this->manager->persist($user);
        $this->manager->flush();

        $this->addFlash('success', "Votre compte est confirmé ! Vous pouvez dès à présent vous connecter.");
        return $this->redirectToRoute('app_login');
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route("/resetPassword", name: "home.resetPassword")]
    public function sendResetPassword(Request $request, SessionInterface $session): Response
    {
        $email = $request->request->get('email', '');

        $state = false;

        $session->start();

        $session->set('code', '');

        if ($request->isMethod('POST')) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            $session->set('email', $email);

            if ($user !== null) {
                $state = true;
                $code = random_int(1000, 9999);

                if ($session->get('code') === "") {
                    $session->set('code', $code);
                    $this->mailer->sendResetPasswordMail(
                        $email,
                        "Changement de mot de passe",
                        "Merci de saisir le code",
                        $code
                    );

                    $this->addFlash("success", "Un code de confirmation vient d'être envoyé au mail suivant : ".$user->getEmail());
                    return $this->redirectToRoute('home.confirmCode');
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

       if ($session->get('code') !== "") {
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

        if ($session->get('code') === true) {
            if ($request->isMethod("POST") === true) {
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

    private function addFormErrorsToFlash(FormInterface $form): void
    {
        foreach ($form->getErrors(true) as $error) {
            /** @var FormError $error */
            $this->addFlash('error', $error->getMessage());
        }
    }
}
