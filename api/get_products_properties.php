<?php
require_once "classes/DB.php";
echo DB::getInstance()->getAllPropertiesGroupedByType();