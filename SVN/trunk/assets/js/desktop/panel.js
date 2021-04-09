TW = TW || {};

TW.Panel = function (element) {
    this.desktop = null;

    this.user_menu = function () {
        /** Creamos el menú de usuarios */
        var user = TW.element("user");

        user.onclick = function () {
            user.classList.toggle("active");
        }
    }

    this.clock = function () {
        var clock = TW.element("clock");

        /** Ponemos en marcha el reloj */
        var weekdays = JSON.parse(clock.dataset.weekdays);
        var months = JSON.parse(clock.dataset.months);

        setInterval(function () {
            date = new Date();

            var day_week = weekdays[date.getDay()];
            var day = date.getDate();
            var month = months[date.getMonth() + 1];
            var hour = String(date.getHours()).padStart(2, "0");
            var minutes = String(date.getMinutes()).padStart(2, "0");

            clock.innerHTML = day_week + ' ' + day + ' ' + month + ' ' + hour + ':' + minutes;
        }, 500);
    }

    this.widget_edition_toggle = function () {
        /** Creamos el menú de edición de widgets */
        var edit_desktop = TW.element('editDesktop');
        if (edit_desktop) {
            edit_desktop.onclick = function () {
                TW.desktop.classList.toggle("editable");
                if (TW.desktop.classList.contains("editable")) {
                    edit_desktop.innerHTML = "Mode edition: ON";
                    this.desktop.set_edit_mode(true);
                } else {
                    edit_desktop.innerHTML = "Mode edition: OFF";
                    this.desktop.set_edit_mode(false);
                }
            }.bind(this);
        }
    }

    this.fullscreen_toggle = function () {
        var full_screen = TW.element('fullScreen');

        if (full_screen) {
            full_screen.onclick = function () {
                var element = document.documentElement;
                // Supports most browsers and their versions.
                var checkFUllScreen = document.fullscreen || document.webkitIsFullScreen || document.mozFullScreen || false
                var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen;
                var stopFullScreen = document.exitFullscreen || document.mozCancelFullScreen || document.webkitCancelFullScreen;

                if (requestMethod) { // Native full screen.
                    if (!checkFUllScreen) {
                        requestMethod.call(element);
                        TW.desktop.classList.add("fullscreen");
                        this.innerHTML = "Full Screen: ON";
                    } else {
                        TW.desktop.classList.remove("fullscreen");
                        stopFullScreen.call(document);
                        this.innerHTML = "Full Screen: OFF";
                    }
                }
            }
        }
    }

    this.display = function (desktop) {
        this.desktop = desktop;

        this.clock();
        this.user_menu();
        this.widget_edition_toggle();
        this.fullscreen_toggle();
    }
};