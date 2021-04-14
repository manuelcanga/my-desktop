jQuery(document).ready(function () {
    var desktop = new TW.Desktop();
    desktop.display();

    var panel = new TW.Panel();
    panel.display(desktop);
});