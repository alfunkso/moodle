<?php

function make_miwriter_quiz_summary($resources)
{
    $summary = array();
    
    foreach($resources as $r)
    {
        if ($r->forumid != 0)
            continue;
        
        $sortkey = $r->moduleid . $r->attemptid . $r->textarea;
        if (!array_key_exists($sortkey, $summary))
        {
            $summary[$sortkey] = array('module'=>$r->moduleid, 'attempt'=>$r->attemptid, 'textarea'=>$r->textarea, 'edits'=>1, 'toterr'=>$r->spellerrors, 'avgerr'=>$r->spellerrors);
        }
        else
        {
            $summary[$sortkey]['edits'] = $summary[$sortkey]['edits'] + 1;
            $summary[$sortkey]['toterr'] = $summary[$sortkey]['toterr'] + $r->spellerrors;
            $summary[$sortkey]['avgerr'] = $summary[$sortkey]['toterr'] / $summary[$sortkey]['edits'];
        }
    }
    return $summary;
}

function make_mistake_counts($resources)
{
    $summary = array();
    foreach($resources as $r)
    {
        $sortkey = $r->moduleid . $r->attemptid . $r->textarea;
        if (!array_key_exists($sortkey, $summary))
            $summary[$sortkey] = array('key'=>$sortkey, 'quiz'=>$r->moduleid, 'errors'=>array($r->spellerrors));
        else
            array_push($summary[$sortkey]['errors'], $r->spellerrors);
    }
    return array_values($summary);
}

function make_word_counts($resources)
{
    $summary = array();
    foreach($resources as $r)
    {
        $sortkey = $r->moduleid . $r->attemptid . $r->textarea;
        if (!array_key_exists($sortkey, $summary))
            $summary[$sortkey] = array('key'=>$sortkey, 'quiz'=>$r->moduleid, 'words'=>array($r->wordcount));
        else
            array_push($summary[$sortkey]['words'], $r->wordcount);
    }
    return array_values($summary);
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
