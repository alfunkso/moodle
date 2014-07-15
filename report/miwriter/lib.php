<?php

defined('MOODLE_INTERNAL') || die;

function report_miwriter_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/miwriter:view', $context)) {
        $url = new moodle_url('/report/miwriter/index.php', array('id'=>$course->id));
        $navigation->add(get_string('pluginname', 'report_miwriter'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

?>
