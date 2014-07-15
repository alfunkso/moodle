$(document).ready(
    function() {
        var baseareas = $("textarea");
        var areas = [];
        for (var i = 0; i < baseareas.length; i++)
            areas.push(new TextDocument(baseareas[i]));
        $("textarea").on("keyup", function(event)
        {
            var id = event.target.id;
            var e = getTextDocumentFromArray(areas, id);
            if (event.keyCode === 32 || event.keyCode === 190 || event.keyCode === 191 || event.keyCode === 49)
              e.processEvent();
        });
        //$("<div><textarea>Welcome to MI-Writer!\r\n - possible spelling error: 'form' versus 'from'?</textarea></div>").insertAfter($("textarea"));
    }
);

function getTextDocumentFromArray(areas, searchID)
{
    for (var i = 0; i < areas.length; i++)
    {
        textDoc = areas[i];
        if (textDoc.textarea.id === searchID)
            return textDoc;
    }
    return null;
}

/*
 * Function that creates a local ISO time stamp for use with the application. Prototyped on
 * to the Date object for convenience.
 */
Date.prototype.toISOLocalDateTimeString = function() {
    var padDigits = function padDigits(number, digits) {
        return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
    };

    return this.getFullYear() 
            + "-" + padDigits((this.getMonth()+1),2) 
            + "-" + padDigits(this.getDate(),2) 
            + "T" 
            + padDigits(this.getHours(),2)
            + ":" + padDigits(this.getMinutes(),2)
            + ":" + padDigits(this.getSeconds(),2)
            + "." + padDigits(this.getMilliseconds(),2);
};

/* ----- The TextDocument Object -----
 * The TextDocument object is responsible for doing all of the handling for a single
 * textarea on the page. A TextArea object consists of the textarea DOM object, and a 
 * number of variables that keep track of AJAX requests to the server. The event handling 
 * that JQuery does for the page will call the methods and affect the variables 
 * of TextArea objects.
 */

/*
 * Constructor: Creates a new TextDocument object with a given textarea and a given 
 * value.
 */
function TextDocument(textarea)
{
    this.textarea = textarea;
    this.lastValue = "";
    this.processEvent = processEvent;
}

function processEvent()
{
    var text = this.textarea.value;
    var addition = "";
    var jsonObj = "";
    var xmlObj = "";
    if (text === this.lastValue)
        return;
    if (text.substring(0, text.length - 1) === this.lastValue)
    {
        var ch = text.charAt(text.length - 1);
        addition = "~" + ch;
    }
    else
    {
        addition = encodeURIComponent(text);
    }
    jsonObj = "{text: \"" + addition + "\", time: " + new Date().getTime() + "}";
    xmlObj = '<xml version="1.0">\r\n' +
            '<SensorData>\r\n' +
            '  <Timestamp>' + new Date().toISOLocalDateTimeString() + '</Timestamp>\r\n' +
            '  <Runtime>' + new Date().toISOLocalDateTimeString() + '</Runtime>\r\n' + 
            '  <Tool>Moodle</Tool>\r\n' +
            '  <SensorDataType>MI-Writer</SensorDataType>\r\n' +
            '  <Resource>' + document.URL + '</Resource>\r\n' +
            '  <Owner>js7777@outlook.com</Owner>\r\n' +
            '  <Properties>\r\n' +
            '    <Property>\r\n' + 
            '      <Key>Document</Key>' + '\r\n' + 
            '      <Value>' + addition + '</Value>' + '\r\n' +
            '    </Property>\r\n' +
            '    <Property>\r\n' + 
            '      <Key>Timestamp</Key>\r\n' + 
            '      <Value>' + new Date().toISOLocalDateTimeString() + '</Value>' + '\r\n' +
            '    </Property>\r\n' +
            '    <Property>\r\n' + 
            '      <Key>Site</Key>\r\n' + 
            '      <Value>' + M.cfg.wwwroot + '</Value>' + '\r\n' +
            '    </Property>\r\n' +
            '  </Properties>\r\n' + 
            '</SensorData>';
    this.lastValue = text;
    console.log(M);
    $.ajax({
        url: M.cfg.wwwroot + "/blocks/miwriter/miwriter.php",
        data: {
            text: text,
            resource: document.URL,
            site: M.cfg.wwwroot
        },
        type: "POST",
        dataType: "text",
        success: function(r) {
            console.log(r);
        },
        error: function(xhr,r) {
            console.log(xhr);
            console.log(r);
        }
    });
}


/*
 * - Functionality to select the proper object based on the ID of the textarea throwing 
 *   the event.
 * - On each event, get a snapshot of the current value of the textarea.
 * - Keep track of the previous snapshot as well.
 * - Keep an encoded string of changes to the textArea's value.
 * - If the current value = the previous value, ignore this event.
 * - If the current value is equal to the previous value plus one character, add 
 *   a tilde ~ to the encoded character string, plus the new character, and replace 
 *   the previous snapshot with the current snapshot.
 * - If the current value is not a simple append, then add the full text of the new snapshot to the encoded string.
 * - Use encodeURIComponent() to get rid of all special characters and (hopefully?) quotes.
 * - JSON?? {text: "~o", time: 1234567890}{}
 */

/* ----- END TextDocument Object ----- */


