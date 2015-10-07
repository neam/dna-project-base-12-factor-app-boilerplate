<?php
$actions = array();
foreach (RolesAndOperations::systemRoles() as $title => $label) {
    $actions[] = array('title' => $title);
}
foreach (RolesAndOperations::groupRoles() as $title => $label) {
    $actions[] = array('title' => $title);
}
return $actions;