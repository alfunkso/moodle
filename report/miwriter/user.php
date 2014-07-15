<?php

require('../../config.php');
require_once('util.php');

$courseid = required_param('id', PARAM_INT);
$userid = required_param('uid', PARAM_INT);

$PAGE->set_url('/report/miwriter/index.php');
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$user = $DB->get_record('user',array('id'=>$userid), '*', MUST_EXIST);
require_login($course);

$context = context_course::instance($course->id);

$PAGE->set_title($course->shortname . ': MI-Writer Report');
$PAGE->set_heading($course->fullname);

require_capability('report/miwriter:view', $context);

echo $OUTPUT->header();

$conditions = array();
$conditions['userid'] = $userid;
$conditions['courseid'] = $courseid;

$rSet = $DB->get_records('miwriter', $conditions);
$quizzes = $DB->get_records_sql("SELECT moduleid FROM {miwriter} WHERE courseid = $courseid AND userid = $userid AND moduleid != 0 GROUP BY moduleid");
$forums = $DB->get_records_sql("SELECT forumid FROM {miwriter} WHERE courseid = $courseid AND userid = $userid AND forumid != 0 GROUP BY forumid");

$summary = make_miwriter_quiz_summary($rSet);
$mistakes = make_mistake_counts($rSet);
$counts = make_word_counts($rSet);

echo $OUTPUT->heading(get_string('pluginname','report_miwriter') . " for $user->firstname $user->lastname", 2);

foreach ($quizzes as $q)
{
    $cm = get_fast_modinfo($courseid)->get_cm($q->moduleid);
    echo $OUTPUT->heading($cm->name,3);
    $qtable = new html_table();
    $qtable->head = array('Attempt','Text Area','Edits','Total Errors','Average Spell Errors per Edit','Details');
    $qtable->attributes['class'] = 'generaltable boxaligncenter';
    $qdata = Array();
    
    foreach ($summary as $r)
    {
        if($r['module'] == $q->moduleid)
        {
            array_push($qdata, array($r['attempt'], $r['textarea'], $r['edits'], $r['toterr'], $r['avgerr'], '<a href="#" class="miwriterdetailslink" id="'.$q->moduleid.$r['attempt'].$r['textarea'].'">See Details</a>'));
        }
    }
    $qtable->data = $qdata;
    echo html_writer::table($qtable);
    echo "<div class=\"boxaligncenter\">";
    echo "<div style=\"width: 50%; float:left;\" id=\"echart$q->moduleid\"></div>";
    echo "<div style=\"width: 50%; float:right;\" id=\"wchart$q->moduleid\"></div>";
    echo "</div>";
}
?>

<script type="text/javascript">
    var mistakes = <?php echo json_encode($mistakes); ?>;
    var counts = <?php echo json_encode($counts); ?>;
    var currentMData = new Array();
    var currentCData = new Array();
    var quiz = 0;
    $(document).ready
    (
        function()
        {
            $(".miwriterdetailslink").attr( "href", "#" );
            $(".miwriterdetailslink").on('click', function(event)
            {
                linkid = event.target.id;
                for (var i = 0; i < mistakes.length; i++)
                {
                    if (mistakes[i].key === linkid)
                    {
                        currentMData = mistakes[i].errors;
                        quiz = mistakes[i].quiz;
                    }
                }
                for (var i = 0; i < counts.length; i++)
                {
                    if (counts[i].key === linkid)
                        currentCData = counts[i].words;
                }
                var wordname = "#wchart" + quiz;
                var errname = "#echart" + quiz;
                $(wordname).highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Word Count at Each Edit'
                    },
                    xAxis: {
                        title: {
                            text: 'Edit #'
                        }
                    },
                    yAxis: {
                        title: {
                            text: '# of Words'
                        }
                    },
                    series: [{
                        name: 'Words',
                        data: currentCData
                    }]
                });
                $(errname).highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Errors at Each Edit'
                    },
                    xAxis: {
                        title: {
                            text: 'Edit #'
                        }
                    },
                    yAxis: {
                        title: {
                            text: '# of Errors'
                        }
                    },
                    series: [{
                        name: 'Errors',
                        data: currentMData
                    }]
                });
            });
        }
    )
    
    $(function () { 
    
});
</script>    

<?php

echo $OUTPUT->footer();
?>
