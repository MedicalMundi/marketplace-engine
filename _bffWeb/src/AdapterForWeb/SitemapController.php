<?php declare(strict_types=1);

/**
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2024 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace BffWeb\AdapterForWeb;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.{_format}', name: 'app_sitemap', requirements: [
        '_format' => 'html|xml',
    ], format: 'xml')]
    public function index(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];

        // Static URL homepage
        $urls[] = [
            'loc' => $this->generateUrl('app_home'),
            'priority' => '1.00',
            'changefreq' => 'daily',
        ];

        // Static URLs contact
        $urls[] = [
            'loc' => $this->generateUrl('web_contact'),
            //'lastmod' => $post->getUpdatedAt()->format('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => '1.00',

        ];

        // Add your sitemap URLs dynamically (e.g., from database or routes)
        // Dynamic URLs from Post table

        $xml = $this->renderView('@web/sitemap/sitemap.xml.twig', [
            'urls' => $urls,
            'hostname' => $hostname,
        ]);

        return new Response($xml, Response::HTTP_OK, [
            'Content-Type' => 'text/xml',
        ]);
    }
}
