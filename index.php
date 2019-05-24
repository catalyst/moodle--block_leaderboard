<?php
// This file is part of Moodle - http:// moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Creates and displays the leaderboard in its own page.
 *
 * @package    blocks_leaderboard
 * @copyright  2019 Erik Fox
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");

global $CFG, $DB;

class date_selector_form extends moodleform {

    /**
     * A form for selecting a starting date and an ending date.
     *
     * @return void
     */
    public function definition() {
        $mform = & $this->_form; // Don't forget the underscore!
        $mform->addElement('header', 'h', get_string('changedaterange', 'block_leaderboard'));

        // Parameters required for the page to load.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'start');
        $mform->setType('start', PARAM_RAW);
        $mform->addElement('hidden', 'end');
        $mform->setType('end', PARAM_RAW);

        // The form elements for selecting dates with defaults set to the current date range.
        $mform->addElement('date_selector', 'startDate', get_string('start', 'block_leaderboard'));
        $mform->setDefault('startDate', $this->_customdata['startDate']);
        $mform->addElement('date_selector', 'endDate', get_string('end', 'block_leaderboard'));
        $mform->setDefault('endDate', $this->_customdata['endDate']);

        // The buttons to update the leaderboard with new dates or reset to the default dates.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('update', 'block_leaderboard'));
        $buttonarray[] = $mform->createElement('cancel', 'resetbutton', get_string('resettodefault', 'block_leaderboard'));
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }
}


// Url for icon to expand and collapse the table.
$expandurl = new moodle_url('/blocks/leaderboard/pix/expand.svg');

// Get required parameters from the url.
$cid = required_param('id', PARAM_INT);
$start = required_param('start', PARAM_RAW);
$end = required_param('end', PARAM_RAW);

// Get the current course.
$course = $DB->get_record('course', array('id' => $cid), '*', MUST_EXIST);
require_course_login($course, true);

// This page's url.
$url = new moodle_url('/blocks/leaderboard/index.php', array('id' => $cid, 'start' => $start, 'end' => $end));
$functions = new block_leaderboard_functions;

// Setup the page.
$PAGE->requires->js(new moodle_url('/blocks/leaderboard/javascript/leaderboardTable.js'));
$PAGE->set_pagelayout('incourse');
$PAGE->set_url($url);
$PAGE->set_title(get_string('leaderboard', 'block_leaderboard'));
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class("leaderboard page");

$isstudent = false;
if (user_has_role_assignment($USER->id, 5)) {
    $isstudent = true;
}

// CREATE LEADERBOARD TABLE.
$groups = $DB->get_records('groups', array('courseid' => $cid));
if (count($groups) > 0) { // There are groups to display.
    // Create the table.
    $table = new html_table();
    $table->head = array("", get_string('rank', 'block_leaderboard'), "",
                    get_string('name', 'block_leaderboard'), get_string('points', 'block_leaderboard'));
    $table->attributes['class'] = 'generaltable leaderboardtable';

    // Get average group size.
    $numgroups = count($groups);
    $numstudents = 0;
    foreach ($groups as $group) {
        // Get each member of the group.
        $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
        $numstudents += count($students);
    }
    // Get the average group size.
    $averagegroupsize = ceil($numstudents / $numgroups);

    // Get all group data.
    $groupdataarray = [];
    foreach ($groups as $group) {
        $groupdataarray[] = $functions->get_group_data($group, $averagegroupsize, $start, $end);
    }

    // Sort the groups by points.
    if (count($groupdataarray) > 1) { // Only sort if there is something to sort.
        usort($groupdataarray, function ($a, $b) {
            return $b->points <=> $a->points;
        });
    }

    // Make teams that are tied have the same rank.
    $rankarray = $functions->rank_groups($groupdataarray);

    // Display each group in the table.
    $groupindex = 0;
    foreach ($groupdataarray as $groupdata) {
        // Set groups change in position icon.
        $currentstanding = $rankarray[$groupindex];

        $symbol = $functions->update_standing($groupdata, $currentstanding);

        // Add the groups row to the table.
        if ($groupdata->isusersgroup || !$isstudent) { // Include group students.
            $grouprow = new html_table_row(array('<img class = "dropdown" src = '.$expandurl.'>',
                                            $currentstanding, $symbol, $groupdata->name, round($groupdata->points)));
            if ($groupdata->isusersgroup) { // Bold the group.
                $grouprow->attributes['class'] = 'group this_group collapsible rank'.$currentstanding;
                $grouprow->attributes['name'] = $groupindex;
            } else { // Don't bold the group.
                $grouprow->attributes['class'] = 'group collapsible rank'.$currentstanding;
                $grouprow->attributes['name'] = $groupindex;
            }
            $table->data[] = $grouprow;
        } else {
            $grouprow = new html_table_row(array('', $currentstanding, $symbol, $groupdata->name, round($groupdata->points)));
            $grouprow->attributes['class'] = 'group rank'.$currentstanding;
            $table->data[] = $grouprow;
        }

        if (!$isstudent || $groupdata->isusersgroup) { // If this is the teacher or current user group.
            // Add the students to the table.
            $studentsdata = $groupdata->studentsdata;
            $count = 0;
            foreach ($studentsdata as $key => $value) {
                $studentdata = $value;

                // Add the student to the table.
                if (!$isstudent || $studentdata->id == $USER->id) { // Include student history.
                    if (empty($studentdata->history) != 1) {
                        $individualrow = new html_table_row(array("", '<img class = "dropdown" src = '.$expandurl.'>', "",
                                                $studentdata->firstname." ".$studentdata->lastname, round($studentdata->points)));
                    } else {
                        $individualrow = new html_table_row(array("", "", "",
                                                $studentdata->firstname." ".$studentdata->lastname, round($studentdata->points)));
                    }
                    if ($studentdata->id === $USER->id) { // Bold the current user.
                        $individualrow->attributes['class'] = 'this_user content';
                    } else { // Don't bold.
                        $individualrow->attributes['class'] = 'content';
                    }
                    $individualrow->attributes['name'] = 'c'.$groupindex;
                    $individualrow->attributes['child'] = 's'.$count;
                    $table->data[] = $individualrow;

                    if (empty($studentdata->history) != 1) {
                        // Add the students data to the table.
                        $infocount = 0;
                        foreach ($studentdata->history as $pointsmodule) {
                            // Add a row to the table with the name of the module and the number of points earned.
                            if (property_exists($pointsmodule, "isresponse")) { // Forum modules.
                                if ($pointsmodule->isresponse == 0) {
                                    $modulerow = new html_table_row(array("", "", "",
                                                        "Forum Post", round($pointsmodule->pointsearned)));
                                } else if ($pointsmodule->isresponse == 1) {
                                    $modulerow = new html_table_row(array("", "", "",
                                                        "Forum Response", round($pointsmodule->pointsearned)));
                                }
                            } else { // Modules with their own names.
                                if (property_exists($pointsmodule, "daysearly") && $pointsmodule->pointsearned > 0) {
                                    $modulerow = new html_table_row(array("", '<img class = "dropdown" src = '.$expandurl.'>', "",
                                                        $pointsmodule->modulename, round($pointsmodule->pointsearned)));
                                } else {
                                    $modulerow = new html_table_row(array("", "", "",
                                                        $pointsmodule->modulename, round($pointsmodule->pointsearned)));
                                }
                            }
                            $modulerow->attributes['class'] = 'subcontent';
                            $modulerow->attributes['child'] = 'i'.$infocount;
                            $modulerow->attributes['name'] = 'c'.$groupindex.'s'.$count;
                            $table->data[] = $modulerow;

                            // Add a rows to the table with info on what criteria were met and the number of points earned.
                            $earlypoints = 0;
                            $attemptspoints = 0;
                            $spacingpoints = 0;

                            // Include info about how many days early a task was completed.
                            if (property_exists($pointsmodule, "daysearly")) {
                                $daysearly = $pointsmodule->daysearly;
                                if (property_exists($pointsmodule, "attempts")) {
                                    $earlypoints = $functions->get_early_submission_points($daysearly, 'quiz');
                                } else {
                                    $earlypoints = $functions->get_early_submission_points($daysearly, 'assignment');
                                }
                                if ($earlypoints > 0) {
                                    $modulerow = new html_table_row(array("", "", "",
                                                    "Submitted ".abs(round($pointsmodule->daysearly))." days early",
                                                    $earlypoints));
                                    $modulerow->attributes['class'] = 'contentInfo';
                                    $modulerow->attributes['name'] = 'c'.$groupindex.'s'.$count.'i'.$infocount;
                                    $table->data[] = $modulerow;
                                }
                            }
                            // Include info about how many times a quiz was attempted.
                            if (property_exists($pointsmodule, "attempts")) {
                                $attemptspoints = $functions->get_quiz_attempts_points($pointsmodule->attempts);
                                if ($attemptspoints > 0) {
                                    $modulerow = new html_table_row(array("", "", "",
                                                        $pointsmodule->attempts." attempts", $attemptspoints));
                                    $modulerow->attributes['class'] = 'contentInfo';
                                    $modulerow->attributes['name'] = 'c'.$groupindex.'s'.$count.'i'.$infocount;
                                    $table->data[] = $modulerow;
                                }
                            }

                            // Include info about how long quizzes were spaced out.
                            if (property_exists($pointsmodule, "daysspaced")) {
                                $quizspacing = round($pointsmodule->daysspaced, 5);
                                $unit = " days spaced";
                                if ($quizspacing >= 5) {
                                    $unit = " or more days spaced";
                                }
                                $spacingpoints = $functions->get_quiz_spacing_points($quizspacing);

                                if ($spacingpoints > 0) {
                                    $modulerow = new html_table_row(array("", "", "", $quizspacing.$unit, $spacingpoints));
                                    $modulerow->attributes['class'] = 'contentInfo';
                                    $modulerow->attributes['name'] = 'c'.$groupindex.'s'.$count.'i'.$infocount;
                                    $table->data[] = $modulerow;
                                }
                            }
                            $infocount++;
                        }
                    }
                } else { // Don't include student history.
                    $individualrow = new html_table_row(array("", "", "",
                                        $studentdata->firstname." ".$studentdata->lastname, round($studentdata->points)));
                    // Don't bold student.
                    $individualrow->attributes['class'] = 'content';
                    $individualrow->attributes['name'] = 'c'.$groupindex;
                    $table->data[] = $individualrow;
                }
                $count++;
            }
            // If the teams are not equal add visible bonus points to the table.
            if ($groupdata->bonuspoints > 0) {
                $individualrow = new html_table_row(array("", "", "",
                                    get_string('extrapoints', 'block_leaderboard'), round($groupdata->bonuspoints)));
                $individualrow->attributes['class'] = 'content';
                $individualrow->attributes['name'] = 'c'.$groupindex;
                $individualrow->attributes['child'] = 's'.$count;
                $table->data[] = $individualrow;
            }
        }
        $groupindex++;
    }
} else { // There are no groups in the class.
    $table = new html_table();
    $table->head = array("", get_string('rank', 'block_leaderboard'), "",
                    get_string('name', 'block_leaderboard'), get_string('points', 'block_leaderboard'));
    $row = new html_table_row(array("", "", get_string('nogroupsfound', 'block_leaderboard'), "", ""));
    $table->data[] = $row;
}

// Prepare the date selector to be displayed.
$mform = new date_selector_form(null, array('startDate' => $start, 'endDate' => $end));
$toform = new stdClass;
$toform->id = $cid;
$toform->start = $start;
$toform->end = $end;
$mform->set_data($toform);

if ($mform->is_cancelled()) { // Logic for the reset to default button.
    $daterange = $functions->get_date_range($cid);
    $start = $daterange->start;
    $end = $daterange->end;

    $defaulturl = new moodle_url('/blocks/leaderboard/index.php', array('id' => $cid, 'start' => $start, 'end' => $end));
    redirect($defaulturl);
} else if ($fromform = $mform->get_data()) { // Logic for the update button.
    $nexturl = new moodle_url('/blocks/leaderboard/index.php',
                array('id' => $cid, 'start' => $fromform->startDate, 'end' => $fromform->endDate));
    redirect($nexturl);
}

// DISPLAY PAGE CONTENT.
echo $OUTPUT->header();
echo '<h2>'.get_string('leaderboard', 'block_leaderboard').'</h2>';
echo html_writer::table($table);

// Load csv file with student data.
if (!$isstudent) {
    // Display the download button.
    $mform->display();
    echo html_writer::div($OUTPUT->single_button(new moodle_url('classes/data_loader.php',
                                                array('id' => $cid, 'start' => $start, 'end' => $end)),
                                                get_string('downloaddata', 'block_leaderboard'), 'get'), 'download_button');
}

// Display the Q/A.
echo '<div class = "info">'.get_string('info', 'block_leaderboard').'</div>';
echo '<div class = "description">'.get_string('description', 'block_leaderboard').'</div>';
echo '<div class = "info">'.get_string('QA', 'block_leaderboard').'</div>';
echo '<div class = "q">'.get_string('q0', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a">'.get_string('a0', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "q">'.get_string('q1', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a">'.get_string('a1', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "q">'.get_string('q2', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a partone">'.get_string('a2', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a levels">'.get_string('a22', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "q">'.get_string('q6', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a">'.get_string('a6', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "q">'.get_string('q7', 'block_leaderboard').'</div>';
echo '<br/>';
echo '<div class = "a">'.get_string('a7', 'block_leaderboard').'</div>';
echo $OUTPUT->footer();


// TEMPORARY CODE TO FIX ISSUES.
foreach ($groups as $group) {
    // Get each member of the group.
    $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
    foreach ($students as $student) {
        $sql = "SELECT quiz_attempts.id AS ID2, quiz.*, quiz_attempts.attempt, quiz_attempts.timestart, quiz_attempts.timefinish
                FROM {quiz_attempts} quiz_attempts
                INNER JOIN {quiz} quiz ON quiz.id = quiz_attempts.quiz
                WHERE quiz_attempts.userid = ?;";
        $quizes = $DB->get_records_sql($sql, array($student->id));

        foreach ($quizes as $quiz) {
            $quiztable = $DB->get_record('block_leaderboard_quiz',
                array('quizid' => $quiz->id, 'studentid' => $student->id),
                $fields = '*',
                $strictness = IGNORE_MISSING);
            if (!$quiztable) {
                // Create a new quiz.
                $quiztable = new \stdClass();
                $quiztable->timestarted = 0;
                $quiztable->quizid = $quiz->id;
                $quiztable->studentid = $student->id;
                $quiztable->attempts = $quiz->attempt;
                $quiztable->daysearly = 0;
                $quiztable->daysspaced = 0;
                $quiztable->modulename = $quiz->name;
                $DB->insert_record('block_leaderboard_quiz', $quiztable);
                $quiztable = $DB->get_record('block_leaderboard_quiz',
                    array('quizid' => $quiz->id, 'studentid' => $student->id),
                    $fields = '*',
                    $strictness = IGNORE_MISSING);
            }
            if ($quiz->attempt > $quiztable->attempts) {
                $quiztable->attempts = $quiz->attempt;
            }
            if ($quiz->attempt == 1) {
                $quiztable->timestarted = $quiz->timestart;
                $quiztable->timefinished = $quiz->timefinish;
                $quiztable->daysearly = intdiv(($quiz->timeclose - $quiz->timefinish), 86400);

            }
            $DB->update_record('block_leaderboard_quiz', $quiztable);
        }

        $pastquizzes = $DB->get_records('block_leaderboard_quiz', array('studentid' => $student->id), $sort = 'timestarted ASC');
        $cleanquizzes = [];
        foreach ($pastquizzes as $pastquiz) {
            if ($pastquiz->timefinished != null) {
                $cleanquizzes[] = $pastquiz;
            }
        }
        $previoustime = 0;
        foreach ($cleanquizzes as $quiz) {
            $daysbeforesubmission = $quiz->daysearly;
            $pointsearned = 0;
            if (abs($daysbeforesubmission) < 50) { // Quizzes without duedates will produce a value like -17788.
                $quiz->daysearly = $daysbeforesubmission;
                for ($x = 1; $x <= 5; $x++) {
                    $currenttime = get_config('leaderboard', 'quiztime'.$x);
                    if ($x < 5) {
                        $nexttime = get_config('leaderboard', 'quiztime'.($x + 1));
                        if ($daysbeforesubmission >= $currenttime && $daysbeforesubmission < $nexttime) {
                            $pointsearned = get_config('leaderboard', 'quizpoints'.$x);
                        }
                    } else {
                        if ($daysbeforesubmission >= $currenttime) {
                            $pointsearned = get_config('leaderboard', 'quizpoints'.$x);
                        }
                    }
                }
            } else {
                $quiz->daysearly = 0;
                $pointsearned = 0;
            }

            $quiz->pointsearned = $pointsearned;

            $spacingpoints = 0;
            $quizspacing = ($quiz->timestarted - $previoustime) / (float)86400;

            // Make sure that days spaced doesn't go above a maximum of 5 days.
            $quiz->daysspaced = min($quizspacing, 5.0);

            for ($x = 1; $x <= 3; $x++) {
                $currentspacing = get_config('leaderboard', 'quizspacing'.$x);
                if ($x < 3) {
                    $nextspacing = get_config('leaderboard', 'quizspacing'.($x + 1));
                    if ($quizspacing >= $currentspacing && $quizspacing < $nextspacing) {
                        $spacingpoints = get_config('leaderboard', 'quizspacingpoints'.$x);
                        break;
                    }
                } else {
                    if ($currentspacing <= $quizspacing) {
                        $spacingpoints = get_config('leaderboard', 'quizspacingpoints'.$x);
                    }
                }
            }
            $previoustime = $quiz->timefinished;
            $quiz->pointsearned += $spacingpoints;
            $multipleattemptpoints = 0;
            $points = 0;
            $quizattempts = get_config('leaderboard', 'quizattempts');

            $multipleattemptpoints = get_config('leaderboard', 'quizattemptspoints');

            $points += $multipleattemptpoints * ($quiz->attempts - 1);
            $quiz->pointsearned += $multipleattemptpoints * ($quiz->attempts - 1);

            $DB->update_record('block_leaderboard_quiz', $quiz);
        }
    }
}

foreach ($groups as $group) {
    // Get each member of the group.
    $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
    foreach ($students as $student) {
        // The data of the submission.
        $sql = "SELECT assign.*, assign_submission.userid, assign_submission.timemodified
                FROM {assign_submission} assign_submission
                INNER JOIN {assign} assign ON assign.id = assign_submission.assignment
                WHERE assign_submission.userid = ? AND assign_submission.latest = 1;";

        $assignments = $DB->get_records_sql($sql, array($student->id));
        foreach ($assignments as $assignment) {
            $assignmenttable = $DB->get_record('block_leaderboard_assignment',
                array('studentid' => $student->id, 'activityid' => $assignment->id), $fields = '*',
                $strictness = IGNORE_MISSING);

            if (!$assignmenttable) {
                // Create a new quiz.
                $assignmenttable = new \stdClass();
                $assignmenttable->pointsearned = 0;
                $assignmenttable->studentid = $student->id;
                $assignmenttable->activityid = $assignment->id;
                $assignmenttable->timefinished = $assignment->timemodified;
                $assignmenttable->modulename = $assignment->name;
                $assignmenttable->daysearly = intdiv(($assignment->duedate - $assignment->timemodified), 86400);
                $DB->insert_record('block_leaderboard_assignment', $assignmenttable);

                $assignmenttable = $DB->get_record('block_leaderboard_assignment',
                    array('activityid' => $assignment->id, 'studentid' => $student->id),
                    $fields = '*', $strictness = IGNORE_MISSING);
            }
            $points = 0;
            $daysearly = $assignmenttable->daysearly;
            for ($x = 1; $x <= 5; $x++) {
                $currenttime = get_config('leaderboard', 'assignmenttime'.$x);
                if ($x < 5) {
                    $nexttime = get_config('leaderboard', 'assignmenttime'.($x + 1));
                    if ($daysearly >= $currenttime && $daysearly < $nexttime) {
                        $points = get_config('leaderboard', 'assignmentpoints'.$x);
                        break;
                    }
                } else {
                    if ($daysearly >= $currenttime) {
                        $points = get_config('leaderboard', 'assignmentpoints'.$x);
                        break;
                    }
                }
            }
            $assignmenttable->pointsearned = $points;
            $DB->update_record('block_leaderboard_assignment', $assignmenttable);
        }
    }
}

foreach ($groups as $group) {
    // Get each member of the group.
    $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
    foreach ($students as $student) {
        $sql = "SELECT choice_answers.*, choice.name
            FROM {choice_answers} choice_answers
            INNER JOIN {choice} choice ON choice.id = choice_answers.choiceid
            WHERE choice_answers.userid = ?;";

        $choices = $DB->get_records_sql($sql, array($student->id));
        foreach ($choices as $choice) {
            $choicetable = $DB->get_record('block_leaderboard_choice',
                array('choiceid' => $choice->id, 'studentid' => $student->id),
                $fields = '*', $strictness = IGNORE_MISSING);
            if (!$choicetable) {
                $choicedata = new \stdClass();
                $choicedata->studentid = $choice->userid;
                $choicedata->choiceid = $choice->id;
                $choicedata->pointsearned = get_config('leaderboard', 'choicepoints');
                $choicedata->timefinished = $choice->timemodified;
                $choicedata->modulename = $choice->name;

                $DB->insert_record('block_leaderboard_choice', $choicedata);
                $choicetable = $DB->get_record('block_leaderboard_choice',
                    array('choiceid' => $choice->id, 'studentid' => $student->id),
                    $fields = '*', $strictness = IGNORE_MISSING);
            }
        }
    }
}

foreach ($groups as $group) {
    // Get each member of the group.
    $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
    foreach ($students as $student) {
        $sql = "SELECT moodleoverflow_discussions.*
            FROM {moodleoverflow_discussions} moodleoverflow_discussions
            WHERE moodleoverflow_discussions.userid = ?;";

        $discussions = $DB->get_records_sql($sql, array($student->id));
        foreach ($discussions as $discussion) {
            $discussiontable = $DB->get_record('block_leaderboard_forum',
            array('studentid' => $student->id, 'discussionid' => $discussion->id, 'isresponse' => false),
                $fields = '*', $strictness = IGNORE_MISSING);

            if (!$discussiontable) {
                // Create data for table.
                $forumdata = new \stdClass();
                $forumdata->studentid = $student->id;
                $forumdata->forumid = $discussion->moodleoverflow;
                $forumdata->postid = $discussion->firstpost;
                $forumdata->discussionid = $discussion->id;
                $forumdata->isresponse = false;
                $forumdata->pointsearned = get_config('leaderboard', 'forumpostpoints');
                $forumdata->timefinished = $discussion->timestart;
                $forumdata->modulename = $discussion->name;
                $DB->insert_record('block_leaderboard_forum', $forumdata);
            }
        }
    }
}

foreach ($groups as $group) {
    // Get each member of the group.
    $students = groups_get_members($group->id, $fields = 'u.*', $sort = 'lastname ASC');
    foreach ($students as $student) {
        $sql = "SELECT moodleoverflow_posts.*, moodleoverflow_discussions.moodleoverflow
            FROM {moodleoverflow_posts} moodleoverflow_posts
            INNER JOIN {moodleoverflow_discussions} moodleoverflow_discussions
            ON moodleoverflow_posts.discussion = moodleoverflow_discussions.id
            WHERE moodleoverflow_posts.userid = ?;";

        $discussions = $DB->get_records_sql($sql, array($student->id));
        foreach ($discussions as $discussion) {
            $discussiontable = $DB->get_record('block_leaderboard_forum',
            array('studentid' => $student->id, 'postid' => $discussion->id, 'isresponse' => true),
                $fields = '*', $strictness = IGNORE_MISSING);

            if (!$discussiontable) {
                // Create data for table.
                $forumdata = new \stdClass();
                $forumdata->studentid = $student->id;
                $forumdata->forumid = $discussion->moodleoverflow;
                $forumdata->postid = $discussion->id;
                $forumdata->discussionid = $discussion->discussion;
                $forumdata->isresponse = true;
                $forumdata->pointsearned = get_config('leaderboard', 'forumresponsepoints');
                $forumdata->timefinished = $discussion->created;
                $forumdata->modulename = "Forum Post";
                $DB->insert_record('block_leaderboard_forum', $forumdata);
            }
        }
    }
}