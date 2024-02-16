<?php declare(strict_types=1);

/*
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2023 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace BffWeb\AdapterForWeb\Contact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly RateLimiterFactory $contactFormLimiter,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        path: '/{_locale}/contact',
        name: 'web_contact',
        requirements: [
            '_locale' => 'en|es|it',
        ],
        defaults: [
            '_locale' => 'en',
        ],
        methods: ['GET', 'POST'],
    )]
    public function index(Request $request): Response
    {
        $formDto = new ContactFormDto();
        $form = $this->createForm(ContactFormType::class, $formDto);
        $form->handleRequest($request);

        try {
            $this->verifyThrottling($request);
        } catch (TooManyRequestsHttpException) {
            $errorMessage = $this->translator->trans('too_many_requests_please_try_again_in_number_minute.', [
                'number' => 2,
            ]);
            $form->addError(new FormError($errorMessage));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactFormDto $contact */
            $contact = $form->getData();

            $email = $this->prepareEmail($contact);

            $this->mailer->send($email);

            $flashMessage = $this->translator->trans('action.contact.form.success');
            $this->addFlash('success', $flashMessage);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@web/contact/index.html.twig', [
            'formDto' => $formDto,
            'form' => $form,
        ]);
    }

    private function verifyThrottling(Request $request): void
    {
        // create a limiter based on a unique identifier of the client
        // (e.g. the client's IP address, a username/email, an API key, etc.)
        $limiter = $this->contactFormLimiter->create($request->getClientIp());

        // the argument of consume() is the number of tokens to consume
        // and returns an object of type Limit
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        // you can also use the ensureAccepted() method - which throws a
        // RateLimitExceededException if the limit has been reached
        //$limiter->consume(1)->ensureAccepted();

        // to reset the counter
        // $limiter->reset();
    }

    private function prepareEmail(ContactFormDto $contact): Email
    {
        return (new Email())
            ->from('sys@stage.accounts.oe-modules.com')
            ->to('teclaizerai@gmail.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Richiesta Informazione: ' . $contact->subject)
            ->text($contact->message)
            ->html('<p>' . $contact->message . '</p><p>From: ' . $contact->name . ' --' . $contact->email . '</p>');
    }
}
