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

namespace Splash\Connectors\ReCommerce\Objects\Order;

use Splash\Connectors\ReCommerce\DataTransformer\StatusTransformer;

/**
 * Splash Api Object List Function
 */
trait ObjectsListTrait
{
    /**
     * {@inheritdoc}
     */
    public function objectsList($filter = null, $params = null)
    {
        if (!empty($filter) && !in_array($filter, array_keys(StatusTransformer::SPLASH), true)) {
            return array('meta' => array('current' => 0, 'total' => 0));
        }

        return $this->getVisitor()->list($filter, $params)->getResults();
    }
}
