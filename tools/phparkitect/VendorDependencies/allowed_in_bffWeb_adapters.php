<?php declare(strict_types=1);

return [
    'BffWeb\Core',

    'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
    'Symfony\Component\HttpFoundation\Request',
    'Symfony\Component\HttpFoundation\Response',
    /** ALL EXCEPTIONS */
    'Symfony\Component\HttpKernel\Exception\*',
    'Symfony\Component\HttpKernel\Attribute\AsController',

    'Symfony\Component\RateLimiter\RateLimiterFactory',

    'Symfony\Component\OptionsResolver\OptionsResolver',

    'Symfony\Component\Mailer\MailerInterface',
    'Symfony\Component\Mime\Email',

    'Symfony\Component\Form\FormBuilderInterface',
    'Symfony\Component\Form\AbstractType',
    /** ALL FORM TYPE */
    'Symfony\Component\Form\Extension\Core\Type\*',

    /** ALL CONSTRAINTS */
    'Symfony\Component\Validator\Constraints\*'
];