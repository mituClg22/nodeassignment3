<?php
$conn = new mysqli("localhost", "root", "root", "college");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT id,name,course,marks FROM students ORDER BY id");

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><students/>');

while ($row = $result->fetch_assoc()) {
    $student = $xml->addChild("student");
    $student->addChild("id", (string)$row["id"]);
    $student->addChild("name", htmlspecialchars($row["name"], ENT_XML1, "UTF-8"));
    $student->addChild("course", htmlspecialchars($row["course"], ENT_XML1, "UTF-8"));
    $student->addChild("marks", (string)$row["marks"]);
}

$filename = __DIR__ . "/students_export.xml";

if ($xml->asXML($filename)) {
    echo "Student data exported successfully to students_export.xml";
} else {
    echo "Unable to create XML file.";
}

$conn->close();
?>