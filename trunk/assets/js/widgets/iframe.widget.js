TW = TW || {};
TW.widget = TW.widget || {};

/**
 * Class Iframe.
 * @param element DOM element
 * @param desktop Instance of desktop object.
 *
 * @constructor
 */
TW.widget.Iframe = function (element, desktop) {
    this.desktop = desktop;

    var main_separator = -1 !== element.dataset.url.indexOf('?')? '&': '?';

    this.element = element;
    this.label = element.tag("p");
    this.iframe = element.tag("iframe");
    this.group = TW.drag.createSimpleGroup(element, this.label);
    this.id = element.id;
    this.widget_id = element.dataset.widget_id;
    this.uid = element.dataset.uid || '';
    this.url = element.dataset.url + main_separator + "my_desktop_widget=" + this.widget_id + "&my_desktop_widget_uid=" + this.uid;
    this.title = element.dataset.title;
    this.posx = element.dataset.posx;
    this.posy = element.dataset.posy;
    this.width = element.dataset.width;
    this.height = element.dataset.height;
    this.number = element.dataset.number;
    this.refresh = element.dataset.refresh;

    /**
     * Moving widget. Save widget position.
     *
     * @param posx
     * @param posy
     */
    this.savePosition = function (posx, posy) {
        /* Medidas absolutas ( supuestamente ) */
        posx = posx || this.element.style.left;
        posy = posy || this.element.style.top;

        /* Tamaño ventana actual */
        var win_with = window.innerWidth;
        var win_height = window.innerHeight;

        /* Calculos de absolutas a relativas */
        var xpercent = parseInt(posx, 10) * 100 / win_with;
        var ypercent = parseInt(posy, 10) * 100 / win_height;


        /* Posiciones relativas */
        this.posx = xpercent.toFixed(4);
        this.posy = ypercent.toFixed(4);


        /* guardamos las nuevas posiciones relativos */
        this.posx = this.element.style.left = this.posx + "%";
        this.posy = this.element.style.top = this.posy + "%";

        /* Salvamos posicion mediante peticion ajax */
        this.save();
    }

    /**
     * Resizing widget
     *
     * @param width
     * @param height
     */
    this.saveSize = function (width, height) {
        width = width || this.element.style.width;
        height = height || this.element.style.height;

        /* Tamaño ventana actual */
        var win_with = window.innerWidth;
        var win_height = window.innerHeight;

        /* Calculos de absolutas a relativas */
        var width_percent = parseInt(width, 10) * 100 / win_with;
        var height_percent = parseInt(height, 10) * 100 / win_height;

        /* Medidas relativas */
        this.width = width_percent.toFixed(4);
        this.height = height_percent.toFixed(4);

        /* guardamos los nuevos valores relativos */
        this.width = this.element.style.width = this.width + "%";
        this.height = this.element.style.height = this.height + "%";

        /* Salvamos posicion mediante peticion ajax */
        this.save();
    }

    /**
     * Save widget size/position
     */
    this.save = function () {
        TW.ajax({
            url: "/widget/iframe-save",
            title: this.title,
            posx: this.posx,
            posy: this.posy,
            width: this.width,
            height: this.height,
            number: this.number,
            id: this.widget_id,
            my_desktop_token: my_desktop_token
        });
    }

    /**
     * Place in a position on desktop.
     *
     * @param posx
     * @param posy
     * @param width
     * @param height
     */
    this.setPosition = function (posx, posy, width, height) {
        this.posx = posx || this.posx;
        this.posy = posy || this.posy;
        this.width = width || this.width;
        this.height = height || this.height;

        this.element.style.left = this.posx;
        this.element.style.top = this.posy;
        this.element.style.width = this.width;
        this.element.style.height = this.height;
    }

    /* _____ Initialize __________ */
    /* Ponemos límites al drag&drop de widgets iframe */
    this.group.addTransform(function (coordinate, dragEvent) {
        var win_width = window.innerWidth;
        var win_height = window.innerHeight;

        var width = (parseInt(this.width, 10) / 100) * win_width;
        var height = (parseInt(this.height, 10) / 100) * win_height;

        /* Lo suyo sería +44 pero entonces cuando se quitan los verdes de edicion queda despegado */
        width = parseInt(width.toFixed(0), 10) + 30;
        height = parseInt(height.toFixed(0), 10);

        var origin = TW.coordinates.topLeftPosition(TW.desktop);
        var offset = TW.coordinates.create(width, height);

        var webtop = TW.coordinates.bottomRightPosition(TW.desktop).minus(offset);

        return coordinate.constrainTo(origin, webtop);
    }.bind(this));


    this.setPosition();

    /* Movemos la ventana */
    this.group.register("dragend", function (change) {
        if (!this.desktop.is_edit_mode()) {
            return;
        }

        /**
         *  Comprobamos la variacion con respecto a los ejes para determinar si hubo  o no movimiento del iframe.
         */
        var varx = change.mouseOffset.x - change.group._initialMouseOffset.x;
        var vary = change.mouseOffset.y - change.group._initialMouseOffset.y;

        /* @TODO: Si se ha pasado x segundos desde que el inicio y fin del arrastre, tambien se tendria que guardar */

        if (0 !== varx || 0 !== vary) {
            /* hubo movimiento */
            this.savePosition();

        }
    }.bind(this));

    this.element.onmouseout = function (e) {
        if (!this.desktop.is_edit_mode()) {
            return;
        }

        /* Si no fue en el container salimos */
        if (this.id !== e.target.id) return;
        if (this.element.style.width === this.width && this.element.style.height === this.height) return;

        /* Tamaño ventana actual */
        var win_with = window.innerWidth;
        var win_height = window.innerHeight;
        if (this.element.style.width > win_with) return;
        if (this.element.style.height > win_height) return;

        this.saveSize();

    }.bind(this);

    this.reload = function () {
        this.iframe.src = this.url + "&reload=" + Date.now();
    }

    if (this.refresh > 0) {
        setInterval(function () {
            this.reload();
        }.bind(this), parseInt(this.refresh, 10) * 1000 /* refresh * 1seg */);
    }

    /* Añadimos para que se visualize el contenido */
    this.element.style.visibility = "visible";
    this.reload();
};
