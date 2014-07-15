<?php

require('../../config.php');
require_once('util.php');

$courseid = required_param('id', PARAM_INT);


$PAGE->set_url('/report/miwriter/index.php');
$PAGE->set_pagelayout('report');

$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
require_login($course);

$context = context_course::instance($course->id);

$PAGE->set_title($course->shortname . ': MI-Writer Report');
$PAGE->set_heading($course->fullname);

require_capability('report/miwriter:view', $context);

if (!has_capability('report/miwriter:viewusers'))
    redirect(new moodle_url('/report/miwriter/user.php', array('id'=>$courseid, 'uid'=>$USER->id)));

echo $OUTPUT->header();

$users = $DB->get_records_sql("SELECT userid FROM {miwriter} WHERE courseid = $courseid AND userid IS NOT NULL GROUP BY userid");

echo $OUTPUT->heading(get_string('pluginname','report_miwriter'), 2);

$usertable = new html_table();
$usertable->head = array('Users');
$usertable->attributes['class'] = 'generaltable boxaligncenter';
$userdata = Array();

foreach ($users as $uid)
{
    $user = $DB->get_record('user',array('id'=>$uid->userid));
    $link = html_writer::link(new moodle_url('/report/miwriter/user.php',array('id'=>$courseid,'uid'=>$user->id)), "$user->firstname $user->lastname");
    array_push($userdata, array($link));
}

$usertable->data = $userdata;

echo html_writer::table($usertable);

echo $OUTPUT->footer();

?>
