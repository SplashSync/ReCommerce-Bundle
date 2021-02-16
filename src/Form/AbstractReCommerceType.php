<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Connectors\ReCommerce\Form;

use Splash\Connectors\Optilog\Models\RestHelper as API;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Base Form Type for ReCommerce Connectors Servers
 */
abstract class AbstractReCommerceType extends AbstractType
{
    /**
     * Add Ws Host Url Field to FormBuilder
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addWsHostField(FormBuilderInterface $builder, array $options)
    {
        $builder
            //==============================================================================
            // Optilog Api Host Url
            ->add('WsHost', UrlType::class, array(
                'label' => "var.apiurl.label",
                'help' => "var.apiurl.desc",
                'required' => true,
                'translation_domain' => "ReCommerceBundle",
            ))
        ;

        return $this;
    }

    /**
     * Add Api Key Field to FormBuilder
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addApiKeyField(FormBuilderInterface $builder, array $options)
    {
        $builder
            //==============================================================================
            // Optilog Api Key For Authentification
            ->add('ApiKey', TextType::class, array(
                'label' => "var.apikey.label",
                'help' => "var.apikey.desc",
                'required' => true,
                'translation_domain' => "ReCommerceBundle",
            ))
        ;

        return $this;
    }
}
