<?php declare(strict_types=1);

return [
    'BffWeb\Core',

    'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
    'Symfony\Component\HttpFoundation\Request',
    'Symfony\Component\HttpFoundation\Response',
    'Symfony\Component\HttpKernel\Attribute\AsController',

    'Symfony\Component\OptionsResolver\OptionsResolver',

    'Symfony\Component\Mailer\MailerInterface',
    'Symfony\Component\Mime\Email',

    'Symfony\Component\Form\FormBuilderInterface',
    'Symfony\Component\Form\AbstractType',
    'Symfony\Component\Form\Extension\Core\Type\*',

    'Symfony\Component\Validator\Constraints\*'
];