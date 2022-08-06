<?php

namespace App\Sitemap;

use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Content\Repository\Content;
use Sulu\Component\Webspace\PortalInformation;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapUrl;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Bundle\WebsiteBundle\Sitemap\SitemapAlternateLink;
use Sulu\Component\Content\Repository\Mapping\MappingBuilder;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Bundle\WebsiteBundle\Sitemap\AbstractSitemapProvider;
use Sulu\Component\Content\Repository\ContentRepositoryInterface;

class SitemapProvider extends AbstractSitemapProvider
{

    public function __construct(        
        private ContentRepositoryInterface $contentRepository,
        private WebspaceManagerInterface $webspaceManager,
        private string $environment
        ){}

    public function build($page, $scheme, $host)
    {
        $portalInformations = $this->webspaceManager->findPortalInformationsByHostIncludingSubdomains(
            $host, $this->environment
        );

        $result = [];

        foreach ($portalInformations as $portalInformation) {
            $localization = $portalInformation->getLocalization();

            if (!$localization) {
                continue;
            }

            $pages = $this->contentRepository->findAllByPortal(
                $portalInformation->getLocalization()->getLocale(),
                $portalInformation->getPortalKey(),
                MappingBuilder::create()
                    ->addProperties(['changed', 'seo-hideInSitemap'])
                    ->setResolveUrl(true)
                    ->setHydrateGhost(false)
                    ->getMapping()
            );

            

            foreach ($pages as $contentPage) {
                if (!$contentPage->getUrl()
                    || true === $contentPage['seo-hideInSitemap']
                    || RedirectType::NONE !== $contentPage->getNodeType()
                ) {
                    continue;
                }

                // if ($this->accessControlManager) {
                //     $userPermissions = $this->accessControlManager->getUserPermissionByArray(
                //         $contentPage->getLocale(),
                //         PageAdmin::SECURITY_CONTEXT_PREFIX . $contentPage->getWebspaceKey(),
                //         $contentPage->getPermissions(),
                //         null
                //     );

                //     if (isset($userPermissions['view']) && !$userPermissions['view']) {
                //         continue;
                //     }
                // }

                $sitemapUrl = $this->generateSitemapUrl($contentPage, $portalInformation, $host, $scheme);


                if (!$sitemapUrl) {
                    continue;
                }

                $result[] = $sitemapUrl;
            }
        }

        return $result;
    }

    private function generateSitemapUrl(
        Content $contentPage,
        PortalInformation $portalInformation,
        string $host,
        string $scheme
    ) {
        $changed = $contentPage['changed'];
        if (\is_string($changed)) {
            $changed = new \DateTime($changed);
        }

        $url = $this->webspaceManager->findUrlByResourceLocator(
            $contentPage->getUrl(),
            $this->environment,
            $contentPage->getLocale(),
            $portalInformation->getWebspaceKey(),
            $host,
            $scheme
        );

        if (!$url) {
            return null;
        }

        $defaultLocale = $portalInformation
            ->getWebspace()
            ->getDefaultLocalization()
            ->getLocale(Localization::DASH);

        $sitemapUrl = new SitemapUrl(
            $url,
            $contentPage->getLocale(),
            $defaultLocale,
            $changed
        );

        foreach ($contentPage->getUrls() as $urlLocale => $href) {
            $url = $this->webspaceManager->findUrlByResourceLocator(
                $href,
                $this->environment,
                $urlLocale,
                $portalInformation->getWebspaceKey(),
                $host,
                $scheme
            );

            if (!$url) {
                continue;
            }

            $sitemapUrl->addAlternateLink(new SitemapAlternateLink($url, $urlLocale));
        }

        return $sitemapUrl;
    }

    public function getAlias(): string
    {
        return 'mypages';
    }
}