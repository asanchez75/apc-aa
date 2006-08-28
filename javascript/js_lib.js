function getElementByName(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=getElementByName(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}


// function replaces html code of a an HTML element (identified by id)
// by another code
function SetContent(id,txt,d) {
  if(!d) d=document;
  if (d.all) {                    // IE 4+
    el = d.all[id];
    if ( el != null ) {
      el.innerHTML=txt;
    }
  } else if (d.layers) {            // NS 4
    getElementByName(id, d).document.write(txt);   // eval('document.ids.'+id).document.write(txt);
    getElementByName(id, d).document.close();
  } else if (d.getElementById) {   // NS 6 (new DOM)
    rng = d.createRange();
    el = d.getElementById(id);
    if ( el != null ) {  // in case the element do not exist => do nothing
      rng.setStartBefore(el);
      htmlFrag = rng.createContextualFragment(txt);
      while (el.hasChildNodes())
        el.removeChild(el.lastChild);
      el.appendChild(htmlFrag);
    }
  }
}

function GetContent(id, d)
{
    if(!d) d=document;
    if(d.getElementById) {
        return d.getElementById(id).innerHTML;
    }
    else if(d.layers) {
        for (i=0; i < d.layers.length; i++) {
            var whichEl = d.layers[i];
            if (whichEl.id.indexOf(id) != -1) {
                return whichEl;
            }
        }
    }
    else if(d.all) {
        return d.all(id);
    }
    return "";
}

function MoveElement(id, d, direction) {
    var el,swap, content;
    if(!d) d=document;
    if(!d.getElementById) {  // do not work in dom1
        return ;
    }
    el = d.getElementById(id);
    if ( el != null ) {  // in case the element do not exist => do nothing
        if ( direction == 'up' ) {
            swap = el.previousSibling;
        } else {
            swap = el.nextSibling;
        }
    }

    var a = el.parentNode.rows;

    if (this.descending)
        a.reverse();

    if (SortableTable.removeBeforeSort) {
        // remove from doc
        var nextSibling = tBody.nextSibling;
        var p = tBody.parentNode;
        p.removeChild(tBody);
    }

    // insert in the new order
    var l = a.length;
    for (var i = 0; i < l; i++)
        tBody.appendChild(a[i].element);

    if (SortableTable.removeBeforeSort) {
        // insert into doc
        p.insertBefore(tBody, nextSibling);
    }

    this.updateHeaderArrows();

    this.destroyCache(a);

    if (typeof this.onsort == "function")
        this.onsort();
};

function CacheRows() {
    var rows = this.tBody.rows;
    var l = rows.length;
    var a = new Array(l);
    var r;
    for (var i = 0; i < l; i++) {
        r = rows[i];
        a[i] = {
            value:		this.getRowValue(r, sType, nColumn),
            element:	r
        };
    };
    return a;
};

function destroyCache(oArray) {
    var l = oArray.length;
    for (var i = 0; i < l; i++) {
        oArray[i].value = null;
        oArray[i].element = null;
        oArray[i] = null;
    }
}


function getRowValue(oRow, sType, nColumn) {
    var s;
    var c = oRow.cells[nColumn];
    if (typeof c.innerText != "undefined")
        s = c.innerText;
    else
        s = getInnerText(c);
    return s;
}

function getInnerText(oNode) {
    var s = "";
    var cs = oNode.childNodes;
    var l = cs.length;
    for (var i = 0; i < l; i++) {
        switch (cs[i].nodeType) {
            case 1: //ELEMENT_NODE
                s += SortableTable.getInnerText(cs[i]);
                break;
            case 3:	//TEXT_NODE
                s += cs[i].nodeValue;
                break;
        }
    }
    return s;
}


function MoveRowUp(id, d) {
    return MoveElement(id, d, 'up');
}

function MoveRowDown(id, d) {
    return MoveElement(id, d, 'down');
}

function MoveElement(id, d, direction) {
    var el,swap, content;
    if(!d) d=document;
    if(!d.getElementById) {  // do not work in dom1
        return ;
    }
    el = d.getElementById(id);
    if ( el != null ) {  // in case the element do not exist => do nothing
        if ( direction == 'up' ) {
            swap = el.previousSibling;
        } else {
            swap = el.nextSibling;
        }
        if ( swap != null ) {  // in case the element do not exist => do nothing
            content = el.innerHTML;
            alert(el.innerHTML);
            alert(swap.innerHTML);
            el.innerHTML   = swap.innerHTML;
            swap.innerHTML = content;
        }
    }
}

var popupwindows = Array();
var popup_date = new Date();         // we need just to create unique id for the
var popupwindows_id = popup_date.getTime();   // popups (think of popup in popup)

function OpenWindowTop(url) {
    popupwindows[popupwindows.length] = open(url,'popup'+popupwindows_id+popupwindows.length,'scrollbars=1,resizable=1');
}

//moves selected row of left listbox to the right one
function MoveSelected(left, right)
{
  var i=eval(left).selectedIndex
  if( !eval(left).disabled && ( i >= 0 ) )
  {
    var temptxt = eval(left).options[i].text
    var tempval = eval(left).options[i].value
    var length = eval(right).length
    if( (length == 1) && (eval(right).options[0].value=='wIdThTor') ){  // blank rows are just for <select> size setting
      eval(right).options[0].text = temptxt;
      eval(right).options[0].value = tempval;
    } else
      eval(right).options[length] = new Option(temptxt, tempval);
    eval(left).options[i] = null
    if( eval(left).length != 0 )
      if( i==0 )
        eval(left).selectedIndex=0
       else
        eval(left).selectedIndex=i-1
  }
}

// moves selected row of listbox up
function MoveSelectedUp(listbox)
{
  var i=eval(listbox).selectedIndex
  if( !eval(listbox).disabled && ( i > 0 ) )
  {
    var temptxt = eval(listbox).options[i].text
    var tempval = eval(listbox).options[i].value
    eval(listbox).options[i].text = eval(listbox).options[i-1].text;
    eval(listbox).options[i].value = eval(listbox).options[i-1].value;
    eval(listbox).options[i-1].text = temptxt;
    eval(listbox).options[i-1].value = tempval;
    eval(listbox).selectedIndex=i-1
  }
}

// moves selected row of listbox down
function MoveSelectedDown(listbox)
{
  var i=eval(listbox).selectedIndex
  if( !eval(listbox).disabled && ( i < eval(listbox).length-1 ) )
  {
    var temptxt = eval(listbox).options[i].text
    var tempval = eval(listbox).options[i].value
    eval(listbox).options[i].text = eval(listbox).options[i+1].text;
    eval(listbox).options[i].value = eval(listbox).options[i+1].value;
    eval(listbox).options[i+1].text = temptxt;
    eval(listbox).options[i+1].value = tempval;
    eval(listbox).selectedIndex=i+1
  }
}

// Encodes all values from listbox to comma delimeted string
// prepared for sending as url parameter
function CommaDelimeted(listbox) {
  var foo=''
  var delimeter=''
  for (var i = 0; i < eval(listbox).options.length; i++) {
    if( eval(listbox).options[i].value != 'wIdThTor' ){  // blank rows are just for <select> size setting
      foo += delimeter + escape(eval(listbox).options[i].value)
      delimeter=','
    }
  }
  return foo
}

function GoIfConfirmed(url, text) {
  if (confirm(text)) {
    document.location = url;
  }
}



/** Appends any number of QUERY_STRING parameters (separated by &) to given URL,
 *  using apropriate ? or &. */
function GetUrl(url, params) {
    url_components = url.split('#', 2);
    url_path       = url_components[0];
    url_fragment   = (url_components.length > 1) ? ('#' + url_components[1]) : '';
    url_params     = params.join('&');
    return url_path + ((url_path.search(/\?/) == -1) ? '?' : '&') + url_params + url_fragment;
}


