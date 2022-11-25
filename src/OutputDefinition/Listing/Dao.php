<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OutputDataConfigToolkitBundle\OutputDefinition\Listing;

use Doctrine\DBAL\Exception;
use OutputDataConfigToolkitBundle\OutputDefinition;

/**
 * Class Dao
 *
 * @package OutputDataConfigToolkitBundle\OutputDefinition\Listing
 *
 * @property OutputDefinition\Listing $model
 */
class Dao extends \Pimcore\Model\Listing\Dao\AbstractDao
{
    /**
     * @throws Exception
     */
    public function load(): array
    {
        $configs = [];

        $params = array_column($this->model->getConditionParams() ?: [], 'value');

        $unitIds = $this->db->fetchAllAssociative(
            'SELECT o_id, id, o_classId, channel FROM ' . OutputDefinition\Dao::TABLE_NAME .
            $this->getCondition() . $this->getOrder() . $this->getOffsetLimit(),
            $params
        );

        foreach ($unitIds as $row) {
            $configs[] = OutputDefinition::getByO_IdClassIdChannel($row['o_id'], $row['o_classId'], $row['channel']);
        }

        $this->model->setOutputDefinitions($configs);

        return $configs;
    }

    public function getTotalCount(): int
    {
        $params = array_column($this->model->getConditionParams() ?: [], 'value');

        return (int)$this->db->fetchAssociative('SELECT COUNT(*) as amount FROM `' . OutputDefinition\Dao::TABLE_NAME . '`' . $this->getCondition(), $params);
    }
}
