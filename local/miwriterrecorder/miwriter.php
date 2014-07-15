<?php
require_once('../../config.php');
require_once('HackystatSensor.php');
require_once('phpspellcheck/include.php');

use NlpTools\Tokenizers\WhitespaceTokenizer;

$text = required_param('text', PARAM_TEXT);
$res = required_param('resource',PARAM_TEXT);
$site = required_param('site',PARAM_TEXT);
$cid = required_param('course',PARAM_INT);
$qid = required_param('module',PARAM_INT);
$attempt = required_param('attempt',PARAM_INT);
$fid = required_param('forum',PARAM_INT);
$tarea = required_param('textarea',PARAM_TEXT);
$uid = required_param('user',PARAM_INT);
$arr = array();
$arr["text"] = $text;
$arr["site"] = $site;
$arr["course"] = $cid;
$arr["module"] = $qid;
$arr["attempt"] = $attempt;
$arr["forum"] = $fid;
$arr["textarea"] = $tarea;
$arr["user"] = $uid;

$r = writeMIWriterToDB($text, $cid, $uid, $tarea, $qid, $attempt, $fid);
$hackySensor = new HackystatSensor();
$hackySensor->putSensorData1("Moodle", "MI-Writer", $res, 'alfunkso@hotmail.com', $arr);

function writeMIWriterToDB($text, $course, $user, $textarea, $module, $attempt, $forum)
{
    global $DB;
    $data = new stdClass();
    $data->text = $text;
    $data->courseid = $course;
    $data->textarea = $textarea;
    $data->userid = $user;
    $data->moduleid = $module;
    $data->attemptid = $attempt;
    $data->forumid = $forum;
    $data->timestampmicro = microtime(true);
    $data->questionid = get_question_id($textarea, $module);
    append_nlp_calcs($data);
    echo(print_r($data));
    $returned = $DB->insert_record('miwriter', $data);
    echo("<p>Added a new record with ID $returned.</p>");
    return $returned;
}

function append_nlp_calcs(&$data)
{
    $words = preg_split('/\s+/',$data->text, -1, PREG_SPLIT_NO_EMPTY);
    $data->wordcount = count($words);
    
    $sentences = preg_split('/(?<=[.?!])\s+/',$data->text, -1, PREG_SPLIT_NO_EMPTY);
    $data->sentencecount = count($sentences);
    
    $paragraphs = preg_split('/\v+/',$data->text, -1, PREG_SPLIT_NO_EMPTY);
    $data->paragraphcount = count($paragraphs);
    
    echo(print_r($paragraphs));
    
    $speller = new PHPSpellCheck();
    
    $speller->LicenceKey = "TRIAL";
    $speller->DictionaryPath = ("phpspellcheck/dictionaries/");
    $speller->LoadDictionary("Espanol");
    
    $data->spellerrors = 0;
    $badwords = array();
    $positions = array();
            
    
    for ($i = 0; $i < count($words); $i++)
    {
        if (endsWith($words[$i],'.') || endsWith($words[$i],'!') || endsWith($words[$i],'?') || endsWith($words[$i],','))
            $words[$i] = substr($words[$i],0,strlen($words[$i])-1);
        
        if (!$speller->SpellCheckWord($words[$i]))
        {
            $data->spellerrors = $data->spellerrors + 1;
            array_push($badwords, $words[$i]);
            array_push($positions, $i);
        }
    }
    
    $data->spellerrorwords = implode(',',$badwords);
    $data->spellerrorpos = implode(',',$positions);
}

function get_question_id($area, $moduleid)
{
    global $DB;
    
    if ($moduleid === 0 or $moduleid === NULL)
        return 0;
    
    $cm = $DB->get_record('course_modules', array('id'=>$moduleid));
    $quiz = $DB->get_record('quiz', array('id'=>$cm->instance));
    $questions = explode(",", $quiz->questions);
    $len = strpos($area,"_") - strpos($area,":") - 1;
    $q = intval(substr($area, strpos($area,":")+1, $len));
    echo ('Question ID: ' . $questions[$q-1]);
    return $questions[$q-1];
}

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

?>