<?php

return [
    [
        "name" => "Addons",
        "flag" => "addons.index",
        "parent_flag" => "core.system",
    ],
    [
        "name" => "Activate/Deactivate",
        "flag" => "addons.edit",
        "parent_flag" => "addons.index",
    ],
    [
        "name" => "Remove",
        "flag" => "addons.remove",
        "parent_flag" => "addons.index",
    ],
];
