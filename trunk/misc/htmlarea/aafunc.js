// APC-AA javascript functions for HTMLArea


    function switchHTML(name) {
        elem = document.getElementById(name+"html");
        //elem = document.inputform.eval(name+"html");
        if ( elem != null ) {
            for (i=0; i<elem.length; i++) {
                if (elem[i].value == "h") {
                    elem[i].checked = true;
                }
            }
        }
        if (HTMLArea.checkSupportedBrowser) {
            elem = document.getElementById("htmlplainspan"+name);
            if ( elem != null ) {
                elem.style.display = "none";
            }
        }
    }

    function showHTMLAreaLink(name) {
        if (HTMLArea.checkSupportedBrowser()) {
            elem = document.getElementById("arealinkspan"+name);
            if( elem && (elem != null) ) {
                elem.style.display = "inline";
            }
        }
    }

    function generateArea(name, tableop, spell, rows, cols, session) { // generate HTMLArea from textarea
        area = new HTMLArea(name);
        area.session = session;
        var config = area.config;
        config.height = eval(rows*16+100)+"px";
        config.width = eval(cols*12)+"px";
        if (tableop == true) area.registerPlugin("TableOperations");
        if (spell == true) area.registerPlugin("SpellChecker");
//        area.registerPlugin("InsertFile");
        area.generate();
        switchHTML(name);
        return false;
    }

    function openHTMLAreaFullscreen(name, session) { // open HTMLArea in popupwindow
        ha = new HTMLArea(name); // create dummy HTMLArea object
        HTMLArea.session = session;
        ha._textArea = document.getElementById(name); // set textarea name
        HTMLArea._object = ha; // HTMLArea object is used in popupwindow
        HTMLArea._object.isnormal = "1"; // parent area is normal textarea
        if (HTMLArea.is_ie) { // different window opening for IE and other browsers
            window.open(long_editor_url+"popups/fullscreen.html", "ha_fullscreen",
                        "toolbar=no,location=no,directories=no,status=no,menubar=no," +
                        "scrollbars=no,resizable=yes,width=640,height=480");
        } else {
            window.open(long_editor_url+"popups/fullscreen.html", "ha_fullscreen",
                        "toolbar=no,menubar=no,personalbar=no,width=640,height=480," +
                        "scrollbars=no,resizable=yes");
        }
        switchHTML(name);
    }
