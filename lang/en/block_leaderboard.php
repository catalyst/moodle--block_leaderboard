<?php
// This file is part of Moodle - http://moodle.org/
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
 * Created by PhpStorm.
 * User: erikfox
 * Date: 5/22/18
 * Time: 11:22 PM
 */
defined("TAB1") or define("TAB1", "\t");

// Block name.
$string['pluginname'] = 'Leaderboard';
$string['leaderboard'] = 'Leaderboard';

// Settings strings.
$string['assignment_early_submission'] = 'Assignments Early Submission';
$string['assignment_early_submission_desc'] = 'Set the points awarded for submitting an assignment early. (Order from most to least days early)';

$string['quiz_early_submission'] = 'Quiz Early Submission';
$string['quiz_early_submission_desc'] = 'Set the points awarded for submitting a quiz early. (Order from most to least days early)';

$string['quiz_spacing'] = 'Quiz Spacing';
$string['quiz_spacing_desc'] = 'Set the points awarded for spacing out quizzes. (Order from most to least days spaced)';

$string['quiz_attempts'] = 'Quiz Attempts';
$string['quiz_attempts_desc'] = 'Set the points awarded for retaking quizzes. (Order from most to least retakes)';

$string['choice_settings'] = 'Choice Settings';
$string['choice_settings_desc'] = 'Set points values and how those points values are assigned.';

$string['forum_settings'] = 'Forum Settings';
$string['forum_settings_desc'] = 'Set points values and how those points values are assigned.';

$string['glossary_settings'] = 'Glossary Settings';
$string['glossary_settings_desc'] = 'Set points values and how those points values are assigned.';

$string['other_settings'] = 'Other Settings';
$string['other_settings_desc'] = 'Set points values and how those points values are assigned.';

$string['multiplier_settings'] = 'Multiplier Settings';
$string['multiplier_settings_desc'] = 'Set the points required to reach the next level and multiplier. (Order from highest to lowest level)';

$string['days_submitted_early'] = 'Days Submitted Early';
$string['points_earned'] = 'Pointes Earned';

$string['days_between_quizzes'] = 'Days Between Quizzes';
$string['number_of_attempts'] = 'Number of Attempts';

$string['level'] = 'Level ';
$string['multiplier'] = ' Multiplier';
$string['points_to_get_to_level'] = ' Points to Get to Level ';
$string['points_to_stay_at_level'] = ' Points to Stay at Level ';

$string['label_choice_points'] = 'Choice Points';
$string['desc_choice_points'] = 'Points earned for participating in a choice';

$string['label_forum_post_points'] = 'Post Points';
$string['desc_forum_post_points'] = 'Points earned for posting a question';

$string['label_forum_response_points'] = 'Response Points';
$string['desc_forum_response_points'] = 'Points earned for responding to a question';

$string['reset_settings'] = 'Leaderboard Reset Settings';
$string['reset_settings_desc'] = 'Set the dates when you want the leaderboard to reset.';

$string['label_reset1'] = 'First Reset';
$string['desc_reset1'] = 'Type using format MM/DD/YYYY';

$string['label_reset2'] = 'Second Reset';
$string['desc_reset2'] = 'Type using format MM/DD/YYYY';

// Block strings.
$string['group_multiplier'] = 'Group Multiplier';
$string['mult'] = 'Info';
$string['mult_help'] = '&nbsp;&nbsp;&nbsp; The group multiplier will reward your team with bonus points for continual good study habits. <br/>
                        &nbsp;&nbsp;&nbsp; Increase your team\'s average points per week to increase how many extra points you get, but your team can loose it if your team\'s average points per week drops too low!<br/>
                        &nbsp;&nbsp;&nbsp; Your team\'s current points per week and progress to the next multiplier are shown in the progress bar below.';
$string['points_week'] = 'points/week';
$string['rankings'] = 'Rankings';
$string['num'] = '#';
$string['group'] = 'Group';
$string['points'] = 'Points';
$string['view_full_leaderboard'] = 'View Full Leaderboard';

// Full leaderboard page strings.
$string['leaderboard'] = 'Leaderboard';
$string['rank'] = 'Rank';
$string['name'] = 'Name';
$string['download_data'] = 'Download Data';
$string['extra_points'] = 'Extra Points';
$string['no_Groups_Found'] = 'No Groups Found';
$string['info'] = 'Info';
$string['description'] = 'On this Page you can see your full teams points breakdown as well as each of your individual contributions.';

$string['QA'] = 'Q/A';
$string['q0'] = '<strong>Q0</strong>: What if my team is smaller than other teams? Will we be at a disadvantage?';
$string['a0'] = '<strong>A0</strong>: If your team happens to be smaller than the average team size, don\'t worry, you will get extra points based on your teams average points per person. These will be displayed as "Extra Points" in the table.';
$string['q1'] = '<strong>Q1</strong>: What are ways that we can earn points?';
$string['a1'] = '<strong>A1</strong>: Your team can earn points by practicing good study habits. Turning in assignments and completing quizzes early will award the most points. Other ways of earning points include spacing out quizzes instead of cramming them into one, retaking quizzes for extra practice, posting and responding to questions on the forum, and even rating your understanding in the choice module will all award points.';
$string['q2'] = '<strong>Q2</strong>: What are the exact point breakdowns?';
$string['a2'] = '<strong>A2</strong>: Here is a breakdown of the points before multipliers:';
$string['a22'] = 'Submit Assignments <strong>'.get_config('leaderboard', 'assignmenttime5').'</strong> days early to earn <strong>'.get_config('leaderboard', 'assignmentpoints5').'</strong> points,
                    <br/>Submit Assignments <strong>'.get_config('leaderboard', 'assignmenttime4').'</strong> days early to earn <strong>'.get_config('leaderboard', 'assignmentpoints4').'</strong> points,
                    <br/>Submit Assignments <strong>'.get_config('leaderboard', 'assignmenttime3').'</strong> days early to earn <strong>'.get_config('leaderboard', 'assignmentpoints3').'</strong> points,
                    <br/>Submit Assignments <strong>'.get_config('leaderboard', 'assignmenttime2').'</strong> days early to earn <strong>'.get_config('leaderboard', 'assignmentpoints2').'</strong> points,
                    <br/>Submit Assignments <strong>'.get_config('leaderboard', 'assignmenttime1').'</strong> day early to earn <strong>'.get_config('leaderboard', 'assignmentpoints1').'</strong> points.
                    <br/><br/>Submit Quizzes <strong>'.get_config('leaderboard', 'quiztime5').'</strong> days early to earn <strong>'.get_config('leaderboard', 'quizpoints5').'</strong> points,
                    <br/>Submit Quizzes <strong>'.get_config('leaderboard', 'quiztime4').'</strong> days early to earn <strong>'.get_config('leaderboard', 'quizpoints4').'</strong> points,
                    <br/>Submit Quizzes <strong>'.get_config('leaderboard', 'quiztime3').'</strong> days early to earn <strong>'.get_config('leaderboard', 'quizpoints3').'</strong> points,
                    <br/>Submit Quizzes <strong>'.get_config('leaderboard', 'quiztime2').'</strong> days early to earn <strong>'.get_config('leaderboard', 'quizpoints2').'</strong> points,
                    <br/>Submit Quizzes <strong>'.get_config('leaderboard', 'quiztime1').'</strong> day early to earn <strong>'.get_config('leaderboard', 'quizpoints1').'</strong> points.
                    <br/><br/>Take <strong>'.get_config('leaderboard', 'quizspacing3').'</strong> days between Quizzes to earn <strong>'.get_config('leaderboard', 'quizspacingpoints3').'</strong> points,
                    <br/>Take <strong>'.get_config('leaderboard', 'quizspacing2').'</strong> days between Quizzes to earn <strong>'.get_config('leaderboard', 'quizspacingpoints2').'</strong> points,
                    <br/>Take <strong>'.get_config('leaderboard', 'quizspacing1').'</strong> days between Quizzes to earn <strong>'.get_config('leaderboard', 'quizspacingpoints1').'</strong> points.
                    <br/><br/>Attempt Quizzes up to <strong>'.get_config('leaderboard', 'quizattempts').'</strong> times to earn <strong>'.get_config('leaderboard', 'quizattemptspoints').'</strong> points each attempt.
                    <br/><br/>Earn <strong>'.get_config('leaderboard', 'forumpostpoints').'</strong> points for posting in a Forum
                    <br/>Earn <strong>'.get_config('leaderboard', 'forumresponsepoints').'</strong> points for responding to a Forum
                    <br/><br/>Earn <strong>'.get_config('leaderboard', 'choicepoints').'</strong> points for participating in a Choice';
$string['q3'] = '<strong>Q3</strong>: What are the multipliers for?';
$string['a3'] = '<strong>A3</strong>: The multipliers are there to encourage continual good study habits. If your team is averaging many points a week then they will be rewarded with a multiplier that will increase all of their pointes earned by that exact amount. If your team continues to earn points the multiplier will increase even more, however you can loose the multiplier if your teams weekly point average drops enough.';
$string['q4'] = '<strong>Q4</strong>: What are the different multiplier values and how many points are needed to reach them?';
$string['a41'] = '<strong>A4</strong>: The multiplier levels are currently set to:';
$string['a42'] = '<strong>'.get_config('leaderboard', 'groupdata1').'</strong> points per week to reach <strong>'.get_config('leaderboard', 'multiplier2').'x</strong> points,
                    <br/><strong>'.get_config('leaderboard', 'groupdata2').'</strong> points per week to reach <strong>'.get_config('leaderboard', 'multiplier3').'x </strong> points,
                    <br/><strong>'.get_config('leaderboard', 'groupdata3').'</strong> points per week to reach <strong>'.get_config('leaderboard', 'multiplier4').'x </strong> points,
                    <br/><strong>'.get_config('leaderboard', 'groupdata4').'</strong> points per week to reach <strong>'.get_config('leaderboard', 'multiplier5').'x </strong> points,
                    <br/><strong>'.get_config('leaderboard', 'groupdata5').'</strong> points per week to stay at <strong>'.get_config('leaderboard', 'multiplier5').'x</strong> points.';
$string['q5'] = '<strong>Q5</strong>: How is the weekly point average for my team determined?';
$string['a5'] = '<strong>A5</strong>: The weekly point average only takes the two most recent weeks into account so that teams can get stuck with low averages or secure high averages throughout the semester. If the teams most recent week has a higher average than the last 2 weeks, the 1 week average is used instead so that teams don\'t loose their multiplier due to a school break.';
$string['q6'] = '<strong>Q3</strong>: I submitted an assignment early, why don\'t I see any points?';
$string['a6'] = '<strong>A3</strong>: This is normal. Your points will not be recorded in the leaderboard until after the due date for an assignment or quiz has passed. This is in place to so that an assignment cannot earn points for submission both before and after a reset.';
$string['q7'] = '<strong>Q4</strong>: Is my data being logged?';
$string['a7'] = '<strong>A4</strong>: Yes. This data is being used for research purposes to track in class study habits and how to improve them. However your names and personal data are not attached to any of the data being logged.';
