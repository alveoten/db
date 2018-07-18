<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18/07/18
 * Time: 11.50
 */

namespace Tabusoft\DB;


interface DBEventsQueryInterface
{
    public function __invoke( DB $db, $sql, array $infos);
}