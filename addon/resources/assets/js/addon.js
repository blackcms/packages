class Addon {
    init() {
        $("#addon-list").on("click", ".btn-trigger-change-status", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass("button-loading");

            $.ajax({
                url: route("addons.change.status", {
                    name: _self.data("addon"),
                }),
                type: "POST",
                data: { _method: "PUT" },
                success: (data) => {
                    if (data.error) {
                        BlackCMS.showError(data.message);
                    } else {
                        BlackCMS.showSuccess(data.message);
                        $("#addon-list #app-" + _self.data("addon")).load(
                            window.location.href +
                                " #addon-list #app-" +
                                _self.data("addon") +
                                " > *"
                        );
                        window.location.reload();
                    }
                    _self.removeClass("button-loading");
                },
                error: (data) => {
                    BlackCMS.handleError(data);
                    _self.removeClass("button-loading");
                },
            });
        });

        $(document).on("click", ".btn-trigger-remove-addon", (event) => {
            event.preventDefault();
            $("#confirm-remove-addon-button").data(
                "addon",
                $(event.currentTarget).data("addon")
            );
            $("#remove-addon-modal").modal("show");
        });

        $(document).on("click", "#confirm-remove-addon-button", (event) => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            _self.addClass("button-loading");

            $.ajax({
                url: route("addons.remove", { addon: _self.data("addon") }),
                type: "POST",
                data: { _method: "DELETE" },
                success: (data) => {
                    if (data.error) {
                        BlackCMS.showError(data.message);
                    } else {
                        BlackCMS.showSuccess(data.message);
                        window.location.reload();
                    }
                    _self.removeClass("button-loading");
                    $("#remove-addon-modal").modal("hide");
                },
                error: (data) => {
                    BlackCMS.handleError(data);
                    _self.removeClass("button-loading");
                    $("#remove-addon-modal").modal("hide");
                },
            });
        });
    }
}

$(document).ready(() => {
    new Addon().init();
});
