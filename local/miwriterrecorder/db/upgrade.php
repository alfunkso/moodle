<?php
function xmldb_local_miwriterrecorder_upgrade($oldversion) {
    global $DB, $CFG;
    $dbman = $DB->get_manager();

    $result = TRUE;

    if ($oldversion < 2014040600) {

        // Define table miwriter to be created
        $table = new xmldb_table('miwriter');

        // Adding fields to table miwriter
        $table->add_field('timestampmicro', XMLDB_TYPE_NUMBER, '20, 10', null, null, null, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('wordcount', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sentencecount', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('paragraphcount', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('spellerrors', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('spellerrorwords', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('spellerrorpos', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // miwriterrecorder savepoint reached
        upgrade_plugin_savepoint(true, 2014040600, 'local', 'miwriterrecorder');
    }

    return $result;
}
?>
