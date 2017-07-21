<!DOCTYPE html>
<html>
<head>

</head>
<body>

<?php

$keyword        = $_GET['keyword'];
$astr_choice    = intval($_GET['astr_choice']);
$skill_choice   = intval($_GET['skill_choice']);
$type_choice    = intval($_GET['type_choice']);

# Load user credentials
$iniData        = file_get_contents('/etc/mysql/user.cnf');
$iniData        = preg_replace('/#.*$/m', '', $iniData);
$mysqlConfig    = parse_ini_string($iniData, true);

# Connect to database
$con = mysqli_connect('dbint.astro4dev.org',$mysqlConfig['client']['user'],$mysqlConfig['client']['password'],'toolkit_db');

if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

# Search and find anything in the database which matches the Id or the keyword
$query_search           = "SELECT * FROM courses WHERE (IF(LENGTH('".$keyword."') > 0, courses.title LIKE '%".$keyword."%' , 0)) UNION SELECT * FROM examples WHERE (IF(LENGTH('".$keyword."') > 0, examples.title LIKE '%".$keyword."%' , 0)) UNION SELECT * FROM assessments WHERE (IF(LENGTH('".$keyword."') > 0, assessments.title LIKE '%".$keyword."%' , 0));";
$query_astr_category    = "SELECT * FROM topics_astr WHERE Id='".$astr_choice."';";
$query_skill_category   = "SELECT * FROM skills WHERE Id='".$skill_choice."';";


$query_topics_astr__examples = "\n
SELECT topics_astr.topics_astr,examples.last_updated,examples.title,examples.links,authors.name,authors.author_img\n
FROM topics_astr,examples,authors,topics_astr__examples,authors__examples\n
WHERE topics_astr__examples.topic_id = topics_astr.id\n
AND topics_astr__examples.example_id = examples.id\n
AND authors__examples.author_id = authors.id\n
AND authors__examples.example_id = examples.id\n
AND topics_astr.id = '".$astr_choice."';";

$query_topics_astr__courses = "\n
SELECT topics_astr.topics_astr,courses.last_updated,courses.title,courses.links,authors.name,authors.author_img\n
FROM topics_astr,courses,authors,topics_astr__courses,authors__examples\n
WHERE topics_astr__courses.topic_id = topics_astr.id\n
AND topics_astr__courses.course_id = courses.id\n
AND authors__courses.author_id = authors.id\n
AND authors__courses.course_id = courses.id\n
AND topics_astr.id = '".$astr_choice."';";

$query_skills__examples = "\n
SELECT skills.skills,examples.last_updated,examples.title,examples.links,authors.name,authors.author_img\n
FROM skills,examples,authors,skills__examples,authors__examples\n
WHERE skills__examples.skill_id = skills.id\n
AND skills__examples.example_id = examples.id\n
AND authors__examples.author_id = authors.id\n
AND authors__examples.example_id = examples.id\n
AND skills.id = '".$skill_choice."';";

$query_skills__courses = "\n
SELECT skills.skills,courses.last_updated,courses.title,courses.links,authors.name,authors.author_img\n
FROM skills,courses,authors,skills__courses,authors__courses\n
WHERE skills__courses.skill_id = skills.id\n
AND skills__courses.course_id = courses.id\n
AND authors__courses.author_id = authors.id\n
AND authors__courses.course_id = courses.id\n
AND skills.id = '".$skill_choice."';";

$query_skills__assessments = "\n
SELECT skills.skills,assessments.last_updated,assessments.title,assessments.links,authors.name,authors.author_img\n
FROM skills,assessments,authors,skills__assessments,authors__assessments\n
WHERE skills__assessments.skill_id = skills.id\n
AND skills__assessments.assessment_id = assessments.id\n
AND authors__assessments.author_id = authors.id\n
AND authors__assessments.assessment_id = assessments.id\n
AND skills.id = '".$skill_choice."';";


$query_skills__courses = "\n
SELECT skills.skills,courses.last_updated,courses.title,courses.links,authors.name,authors.author_img\n
FROM skills,courses,authors,skills__courses,authors__courses\n
WHERE skills__courses.skill_id = skills.id\n
AND skills__courses.course_id = courses.id\n
AND authors__courses.author_id = authors.id\n
AND authors__courses.course_id = courses.id\n
AND skills.id = '".$skill_choice."';";


$query_search__skills__courses = "\n
SELECT skills.skills,courses.last_updated,courses.title,courses.links,authors.name,authors.author_img\n
FROM skills,courses,authors,skills__courses,authors__courses\n
WHERE skills__courses.skill_id = skills.id\n
AND skills__courses.course_id = courses.id\n
AND authors__courses.author_id = authors.id\n
AND authors__courses.course_id = courses.id\n
AND skills.id = '".$skill_choice."';";


$search_results     = mysqli_fetch_array(mysqli_query($con, $query_search));

$astr_topic         = mysqli_fetch_array(mysqli_query($con, $query_astr_category))['topics_astr'];
$skill_topic        = mysqli_fetch_array(mysqli_query($con, $query_skill_category))['skills'];


$search_query       = mysqli_query($con, $query_search);


$example_astr       = mysqli_query($con, $query_topics_astr__examples);
$example_skill      = mysqli_query($con, $query_skills__examples);

$course_astr        = mysqli_query($con, $query_topics_astr__courses);
$course_skill       = mysqli_query($con, $query_skills__courses);

$assessment_skill   = mysqli_query($con, $query_skills__assessments);

$title_skill        = array();
$title_course       = array();


if ($astr_choice == 6 || $skill_choice == 5){
    echo "<div class=\"column column-four\"><h3>Help contribute to the toolkit</h3>If you have ever taught astronomy using data science techniques or you are a data scientist who has used astronomy examples to teach data science, please consider <a href=\"index.php#contribute\">contributing</a> your teaching materials to the toolkit. You can either directly contribute to <a href=\"https://github.com/astro4dev/OAD-Data-Science-Toolkit\" target=\"_blank\">github</a> or send us the materials directly via email.</div>";
}


echo "<table>";

if( mysqli_num_rows($example_astr) ) {
    echo "<tr>
    <th colspan='4'>" . $astr_topic . " Examples</th>
    </tr><tr>";
    while($row_example_astr = mysqli_fetch_array($example_astr)) {
        echo "<td  width='60%'> <a href=\"" . $row_example_astr['links'] . "\" target=\"_blank\"><i>" . $row_example_astr['title'] . "</td></i></a><td width='30%'>" . $row_example_astr['name'] . "</td> <td width='10%'>". $row_example_astr['last_updated'] . "</td>";
        echo "</tr>";
        $title_skill[] = $row_example_astr['title'];
    } 
}

if( mysqli_num_rows($example_skill) ) {
    echo "<tr>
    <th colspan='4'>" . $skill_topic . " Examples</th>
    </tr><tr>";
    while($row_example_skill = mysqli_fetch_array($example_skill)) {
        if ($title_skill[0] != $row_example_skill['title']) {
        echo "<tr>";
        echo "<td width='60%'> <a href=\"" . $row_example_skill['links'] . "\" target=\"_blank\"><i>" . $row_example_skill['title'] . "</td></i></a><td width='25%'>" . $row_example_skill['name'] . "</td> <td><img src='" . $row_example_skill['author_img'] . "' width='60'><td>". $row_example_skill['last_updated'] . "</td>";
        echo "</tr>";
        }
    }
}



if( mysqli_num_rows($course_astr) ) {
    echo "<tr>
    <th colspan='4'>" . $astr_topic . " Courses</th>
    </tr><tr>";
    while($row_course_astr = mysqli_fetch_array($course_astr)) {
        echo "<tr>";
        echo "<td width='60%'> <a href=\"" . $row_course_astr['links'] . "\" target=\"_blank\"><i>" . $row_course_astr['title'] . "</td></i></a><td width='25%'>" . $row_course_astr['name'] . "</td> <td><img src='" . $row_course_astr['author_img'] . "' width='60'><td>". $row_course_astr['last_updated'] . "</td>";
        echo "</tr>";
        $title_course[] = $row_course_astr['title'];
    }
}


if( mysqli_num_rows($course_skill) ) {
    echo "<tr>
    <th colspan='4'>" . $skill_topic . " Courses</th>
    </tr><tr>";
    while($row_course_skill = mysqli_fetch_array($course_skill)) {
        if ($title_course[0] != $row_course_skill['title']) {
        echo "<tr>";
        echo "<td width='60%'> <a href=\"" . $row_course_skill['links'] . "\" target=\"_blank\"><i>" . $row_course_skill['title'] . "</td></i></a><td width='25%'>" . $row_course_skill['name'] . "</td> <td><img src='" . $row_course_skill['author_img'] . "' width='60'><td>". $row_course_skill['last_updated'] . "</td>";
        echo "</tr>";
        }
    }
}


if( mysqli_num_rows($assessment_skill) ) {
    echo "<tr>
    <th colspan='4'>" . $skill_topic . " Assessments</th>
    </tr><tr>";
    while($row_assessment_skill = mysqli_fetch_array($assessment_skill)) {
        echo "<tr>";
        echo "<td> <a href=\"" . $row_assessment_skill['links'] . "\" target=\"_blank\"><i>" . $row_assessment_skill['title'] . "</td></i></a><td width='25%'>" . $row_assessment_skill['name'] . "</td> <td><img src='" . $row_assessment_skill['author_img'] . "' width='60'><td>". $row_assessment_skill['last_updated'] . "</td>";
        echo "</tr>";
        }
}


if (!empty($search_query)) {

if( mysqli_num_rows($search_query) ) {
    echo "<tr>
    <th colspan='4'>Search results for: '" . $keyword . "'</th>
    </tr><tr>";
    while($row_search_query = mysqli_fetch_array($search_query)) {
        echo "<tr>";
        echo "<td> <a href=\"" . $row_search_query['links'] . "\" target=\"_blank\"><i>" . $row_search_query['title'] . "</td></i></a><td width='25%'>" . $row_search_query['name'] . "</td> <td><img src='" . $row_search_query['author_img'] . "' width='60'><td>". $row_search_query['last_updated'] . "</td>";
        echo "</tr>";
        }
}
}

echo "</table>";

// Free results
mysqli_free_result($example_astr);
mysqli_free_result($example_skill);
mysqli_free_result($course_astr);
mysqli_free_result($course_skill);
mysqli_free_result($assessment_skill);
mysqli_free_result($search_query);

mysqli_close($con);
?>
</body>
</html>