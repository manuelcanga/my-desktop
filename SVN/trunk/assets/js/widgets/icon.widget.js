TW = TW || {};
TW.widget = TW.widget || {};

/**
 * Class Icon.
 * @param element DOM element
 * @param desktop Instance of desktop object.
 *
 * @constructor
 */
TW.widget.Icon = function (element, desktop) {
    this.desktop = desktop;

    var main_separator = -1 !== element.dataset.url.indexOf('?')? '&': '?';

    this.element = element;
    this.group = TW.drag.createSimpleGroup(element);
    this.image = element.tag("img").src;
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
    this.external = element.dataset.external;

    /**
     * Open widget url.
     *
     * @returns false Avoid click event.
     */
    this.openUrl = function () {
        if (/* 0 == this.url.indexOf('http') */ this.external) {
            var options = 'toolbar=no,status=no,location=no,fullscreen=yes,titlebar=no,menubar=no,width=' + window.innerWidth + ',height=' + window.innerHeight;
            window.open(this.url, this.title, options);
        } else {
            /* $.Window.minimizeAll(); */
            $(TW.desktop).window({
                "title": this.title,
                "url": this.url,
                dockArea: $('#taskbar'),
                icon: this.image,
                showFooter: true
            }).maximize();
        }

        return false;
    }

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

        /* Calculos de absolutas a relativas */
        var win_with = window.innerWidth;
        var win_height = window.innerHeight;

        var xpercent = parseInt(posx, 10) * 100 / win_with;
        var ypercent = parseInt(posy, 10) * 100 / win_height;

        /* Medidas relativas */
        this.posx = xpercent.toFixed(3);
        this.posy = ypercent.toFixed(3);

        /* guardamos los nuevos valores relativos */
        this.posx = this.element.style.left = this.posx + "%";
        this.posy = this.element.style.top = this.posy + "%";


        this.save();
    }

    /**
     * Set a target position.
     *
     * @param posx
     * @param posy
     */
    this.setPosition = function (posx, posy) {
        this.posx = posx || this.posx;
        this.posy = posy || this.posy;

        this.element.style.left = this.posx;
        this.element.style.top = this.posy;
    }

    /**
     * Save widget size/position using AJAX.
     */
    this.save = function () {
        TW.ajax({
            url: "/widget/icon-save",
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


    /* _____ Inicialización __________ */
    /* Ponemos límites al drag&drop */
    this.group.addTransform(function (coordinate, dragEvent) {
        if (!this.desktop.is_edit_mode()) {
            return TW.coordinates.create(dragEvent.topLeftOffset.x, dragEvent.topLeftOffset.y)
        }

        var offset = TW.coordinates.create(50, 50)
        var origin = TW.coordinates.topLeftPosition(TW.desktop);
        var webtop = TW.coordinates.bottomRightPosition(TW.desktop).minus(offset);

        return coordinate.constrainTo(origin, webtop);
    }.bind(this));

    this.element.ondblclick = this.openUrl.bind(this);
    this.setPosition();

    this.group.register("dragend", function (change) {
        if (!this.desktop.is_edit_mode()) {
            return;
        }

        /**
         *  Comprobamos la variacion con respecto a los ejes para determinar si hubo  o no movimiento de los iconos
         */
        var varx = change.mouseOffset.x - change.group._initialMouseOffset.x;
        var vary = change.mouseOffset.y - change.group._initialMouseOffset.y;
        if (varx !== 0 || vary !== 0) {
            this.savePosition();
        }
    }.bind(this));
};
