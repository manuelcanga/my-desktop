TW = TW || {};

TW.Desktop = function (element) {
    this.edit_mode = false;
    this.full_screen = false;

    /**
     * Disable text selection in desktop.
     * @param e
     * @returns {boolean}
     */
    this.disable_text_selection = function (e) {
        var target = e.originalTarget || e.toElement;

        try {
            if ('webtop' === target.id) {
                return false;
            }
        } catch (e) {
            return true;
        }
    }

    this.initialize_globa_vars = function () {
        TW.coordinates = ToolMan.coordinates()
        TW.drag = ToolMan.drag()
        TW.desktop = TW.element("webtop");
    }

    this.prepare_windows = function () {
        $.window.prepare({
            dock: 'top',       /* change the dock direction: 'left', 'right', 'top', 'bottom' */
            dockArea: $('#taskbar'), /* set the dock area */
            animationSpeed: 200,  /* set animation speed */
            minWinLong: 180       /* set minimized window long dimension width in pixel */
        });
    }

    this.register_widgets = function () {
        /** Recorremos los iconos para asignarles sus tareas y habilidades */
        var list = TW.desktop.elements("widget_icon");
        var max = list.length;
        for (var i = 0; i < max; i++) {
            new TW.widget.Icon(list[i], this);
        }

        /* Recorremos los elementos de tipo iframe */
        var list = TW.desktop.elements("widget_iframe");
        var max = list.length;
        for (var i = 0; i < max; i++) {
            new TW.widget.Iframe(list[i], this);
        }
    }

    this.display = function () {
        document.onmousedown = this.disable_text_selection;
        this.initialize_globa_vars();
        this.prepare_windows();
        this.register_widgets()
    }

    this.set_edit_mode = function (state) {
        this.edit_mode = state;
    }

    this.is_edit_mode = function () {
        return this.edit_mode;
    }

    this.set_full_screen = function (state) {
        this.full_screen = state;
    }

    this.is_full_screen = function () {
        return this.full_screen;
    }
};


