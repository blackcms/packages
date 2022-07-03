<?php

Route::group(
    [
        "namespace" => "BlackCMS\Addon\Http\Controllers",
        "middleware" => ["web", "core"],
    ],
    function () {
        Route::group(
            ["prefix" => BaseHelper::getAdminPrefix(), "middleware" => "auth"],
            function () {
                Route::group(["prefix" => "addons"], function () {
                    Route::get("", [
                        "as" => "addons.index",
                        "uses" => "AddonController@index",
                    ]);

                    Route::put("status", [
                        "as" => "addons.change.status",
                        "uses" => "AddonController@update",
                        "middleware" => "preventDemo",
                        "permission" => "addons.index",
                    ]);

                    Route::delete("{addon}", [
                        "as" => "addons.remove",
                        "uses" => "AddonController@destroy",
                        "middleware" => "preventDemo",
                        "permission" => "addons.index",
                    ]);
                });
            }
        );
    }
);
