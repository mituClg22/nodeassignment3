<?php
$conn = new mysqli("localhost", "root", "root", "college");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$xml = simplexml_load_file("students.xml");

if ($xml === false) {
    die("Unable to read XML file.");
}

$stmt = $conn->prepare(
    "INSERT INTO students(id,name,course,marks)
     VALUES (?,?,?,?)
     ON DUPLICATE KEY UPDATE
     name=VALUES(name), course=VALUES(course), marks=VALUES(marks)"
);

foreach ($xml->student as $student) {
    $id = (int)$student->id;
    $name = (string)$student->name;
    $course = (string)$student->course;
    $marks = (int)$student->marks;

    $stmt->bind_param("issi", $id, $name, $course, $marks);

    if ($stmt->execute()) {
        echo "Student $name inserted/updated successfully.<br>";
    } else {
        echo "Error: " . htmlspecialchars($stmt->error) . "<br>";
    }
}

$stmt->close();
$conn->close();
?>