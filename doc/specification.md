Project Specification & Database Design

THE GUIDELINE OF THIS PROJECT ;

●	Requires PHP to process logic (e.g., user input, decision-making)
●	Uses MariaDB to read/write meaningful data
●	Produces dynamic content on the webpage



1. Demo Scenario Overview
●	A user fills out a Amount($), Date, Category, Notes.
●	The system processes the input, saves it , and input into the booking to the database.
●	A save button displays in the log box.
●	An admin/demo page shows a list of all saved expenses.


2. Planned URL Endpoints

public/

index.php
create.php
edit.php
delete.php
categories.php
summary.php

func/
db.php
