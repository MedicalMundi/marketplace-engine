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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route(path: '/contact', name: 'web_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $formDto = new ContactFormDto();
        $form = $this->createForm(ContactFormType::class, $formDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactFormDto $contact */
            $contact = $form->getData();

            $email = $this->prepareEmail($contact);

            $mailer->send($email);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@web/contact/index.html.twig', [
            'formDto' => $formDto,
            'form' => $form,
        ]);
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
