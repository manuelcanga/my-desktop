/* Mejoramos el DOM, la ventaja de ser en español es que sabemos que ninguna librería que usemos los machara */

TW = (function (window, document, undefined) {
    var TW = {
        'element': function (element) {
            return document.getElementById(element)
        },
        'elements': function (element) {
            return document.getElementsByClassName(element)
        },
        'type': function (element) {
            return document.getElementsByClassName(element)[0]
        },
        'tag': function (element, context) {
            return document.getElementsByTagName(element)[0]
        },
        'tags': function (element) {
            return document.getElementsByTagName(element)
        },
        'get': function (search) {
            return document.querySelectorAll(search)
        },
        'merge': function (o1, o2) {
            var o1 = o1 || {};
            for (var i in o2) o1[i] = o2[i];
            return o1;
        },
        'ajax': function (data, callback) {
            action = "my-desktop";
            data = TW.merge(data, {"action": action});
            if (typeof callback !== "function") {
                callback = function () {
                    //empty callback
                };
            }

            $.ajax({
                url: ajaxurl,
                method: "POST",
                data: data,
            }).done(callback);
        }
    }

    Element.prototype.elements = function (element) {
        return this.getElementsByClassName(element)
    }
    Element.prototype.type = function (element) {
        return this.getElementsByClassName(element)[0]
    }
    Element.prototype.tag = function (element) {
        return this.getElementsByTagName(element)[0]
    }
    Element.prototype.tags = function (element) {
        return this.getElementsByTagName(element)
    }
    Element.prototype.get = function (search) {
        return this.querySelectorAll(search)
    }

    return TW;
})(window, document);