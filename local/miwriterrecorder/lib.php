<?php
function local_miwriterrecorder_extends_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE, $USER;
 
    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }
    
    $uid = $USER->id;
    $cid = $PAGE->course->id;
    if (!$PAGE->cm)
        $cmid = 0;
    else
        $cmid = $PAGE->cm->id;
    $qattempt = optional_param('attempt', 0, PARAM_INT);
    $forum = optional_param('forum', 0, PARAM_INT);
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
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
    var cid = <?php echo $cid; ?>;
    var cmid = <?php echo $cmid; ?>;
    var uid = <?php echo $uid; ?>;
    var quizattempt = <?php echo $qattempt; ?>;
    var fid = <?php echo $forum; ?>;
    var text = this.textarea.value;
    var id = this.textarea.id;
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
    this.lastValue = text;
    //console.log(M);
    $.ajax({
        url: M.cfg.wwwroot + "/local/miwriterrecorder/miwriter.php",
        data: {
            text: text,
            resource: document.URL,
            site: M.cfg.wwwroot,
            textarea: id,
            course: cid,
            module: cmid,
            attempt: quizattempt,
            forum: fid,
            user: uid
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
</script>

<?php
}
?>
