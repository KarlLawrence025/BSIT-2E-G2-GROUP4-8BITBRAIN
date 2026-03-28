<?php
require_once 'db.php';

function insertQuiz($title, $category, $difficulty = 'medium') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO quizzes (title, category, difficulty) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $category, $difficulty);
    $stmt->execute();
    return $conn->insert_id;
}

function insertQuestion($quizId, $questionText) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $quizId, $questionText);
    $stmt->execute();
    return $conn->insert_id;
}

function insertAnswer($questionId, $answerText, $isCorrect = false) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $questionId, $answerText, $isCorrect);
    $stmt->execute();
}

// Clear existing data
$conn->query("DELETE FROM answers");
$conn->query("DELETE FROM questions");
$conn->query("DELETE FROM quizzes");

// Computer Hardware Quiz
$hardwareQuizId = insertQuiz("Computer Hardware Fundamentals", "Computer Hardware", "easy");

$hardwareQuestions = [
    [
        "question" => "Which computer component is responsible for executing instructions?",
        "options" => ["A. Hard drive", "B. RAM", "C. CPU", "D. Monitor"],
        "correct" => "C"
    ],
    [
        "question" => "Which of the following is an example of computer hardware?",
        "options" => ["A. Microsoft Word", "B. Keyboard", "C. Google Chrome", "D. Windows"],
        "correct" => "B"
    ],
    [
        "question" => "What type of software manages the hardware and allows other software to run?",
        "options" => ["A. Application software", "B. Operating system", "C. Utility software", "D. Web software"],
        "correct" => "B"
    ],
    [
        "question" => "Which software is used to create documents, spreadsheets, or presentations?",
        "options" => ["A. System software", "B. Application software", "C. Network software", "D. Firmware"],
        "correct" => "B"
    ],
    [
        "question" => "What does RAM do in a computer?",
        "options" => ["A. Stores files permanently", "B. Temporarily stores data and programs in use", "C. Controls Internet connections", "D. Displays images"],
        "correct" => "B"
    ]
];

foreach ($hardwareQuestions as $q) {
    $questionId = insertQuestion($hardwareQuizId, $q['question']);
    foreach ($q['options'] as $option) {
        $firstChar = strtoupper(substr(trim($option), 0, 1));
        $isCorrect = ($firstChar === strtoupper(trim($q['correct']))) ? 1 : 0;
        insertAnswer($questionId, $option, $isCorrect);
    }
}

// Computer Software Quiz
$softwareQuizId = insertQuiz("Computer Software Essentials", "Computer Software", "easy");

$softwareQuestions = [
    [
        "question" => "What is software?",
        "options" => ["A. Physical parts of a computer", "B. A set of instructions that tells a computer what to do", "C. Internet connection", "D. A storage device"],
        "correct" => "B"
    ],
    [
        "question" => "Which of the following is software?",
        "options" => ["A. Monitor", "B. Mouse", "C. Microsoft Word", "D. Printer"],
        "correct" => "C"
    ],
    [
        "question" => "What type of software controls and manages computer hardware?",
        "options" => ["A. Application software", "B. System software", "C. Programming software", "D. Utility software"],
        "correct" => "B"
    ],
    [
        "question" => "Which is an example of system software?",
        "options" => ["A. Google Chrome", "B. Operating system", "C. Microsoft Excel", "D. Photoshop"],
        "correct" => "B"
    ],
    [
        "question" => "What type of software helps users perform tasks such as typing, browsing, or editing photos?",
        "options" => ["A. System software", "B. Application software", "C. Middleware", "D. Driver software"],
        "correct" => "B"
    ],
    [
        "question" => "What does middleware do?",
        "options" => ["A. Stores data", "B. Allows different software programs to communicate", "C. Controls hardware", "D. Runs applications"],
        "correct" => "B"
    ],
    [
        "question" => "What kind of software is used to create other software?",
        "options" => ["A. Application software", "B. System software", "C. Programming software", "D. Utility software"],
        "correct" => "C"
    ],
    [
        "question" => "Which of the following is an example of application software?",
        "options" => ["A. Windows", "B. BIOS", "C. Microsoft PowerPoint", "D. Device driver"],
        "correct" => "C"
    ],
    [
        "question" => "What does an operating system do?",
        "options" => ["A. Creates documents", "B. Manages hardware and runs other software", "C. Connects to the Internet", "D. Stores data permanently"],
        "correct" => "B"
    ],
    [
        "question" => "Why is system software important?",
        "options" => ["A. It makes games work", "B. It allows the computer to function and run applications", "C. It connects the Internet", "D. It saves files"],
        "correct" => "B"
    ],
    [
        "question" => "Which of the following is NOT a type of software?",
        "options" => ["A. Application software", "B. System software", "C. Programming software", "D. Keyboard"],
        "correct" => "D"
    ],
    [
        "question" => "What type of software helps users browse the Internet or write documents?",
        "options" => ["A. System software", "B. Application software", "C. Middleware", "D. Firmware"],
        "correct" => "B"
    ],
    [
        "question" => "What is the role of programming software?",
        "options" => ["A. To control hardware", "B. To play music", "C. To help developers write and test programs", "D. To store data"],
        "correct" => "C"
    ],
    [
        "question" => "Which software runs first when you turn on a computer?",
        "options" => ["A. Word processor", "B. Operating system", "C. Web browser", "D. Game software"],
        "correct" => "B"
    ],
    [
        "question" => "Why is software necessary in a computer?",
        "options" => ["A. To supply electricity", "B. To cool the system", "C. To tell the hardware how to work", "D. To store files"],
        "correct" => "C"
    ]
];

foreach ($softwareQuestions as $q) {
    $questionId = insertQuestion($softwareQuizId, $q['question']);
    foreach ($q['options'] as $option) {
        $firstChar = strtoupper(substr(trim($option), 0, 1));
        $isCorrect = ($firstChar === strtoupper(trim($q['correct']))) ? 1 : 0;
        insertAnswer($questionId, $option, $isCorrect);
    }
}

// Internet Quiz
$internetQuizId = insertQuiz("Internet Basics", "Internet", "medium");

$internetQuestions = [
    [
        "question" => "What is the Internet?",
        "options" => ["A. A single supercomputer", "B. A global system of interconnected computer networks", "C. A social media platform", "D. A type of software"],
        "correct" => "B"
    ],
    [
        "question" => "Which term best describes how the Internet is organized?",
        "options" => ["A. Centralized", "B. Isolated", "C. Decentralized", "D. Manual"],
        "correct" => "C"
    ],
    [
        "question" => "What allows different networks on the Internet to communicate with each other?",
        "options" => ["A. Browsers", "B. Cables", "C. Communication protocols", "D. Web pages"],
        "correct" => "C"
    ],
    [
        "question" => "Which protocol is fundamental to Internet communication?",
        "options" => ["A. HTML", "B. FTP", "C. TCP/IP", "D. USB"],
        "correct" => "C"
    ],
    [
        "question" => "Which early network is considered the foundation of today's Internet?",
        "options" => ["A. Ethernet", "B. Bluetooth", "C. ARPANET", "D. Intranet"],
        "correct" => "C"
    ],
    [
        "question" => "What is packet switching?",
        "options" => ["A. Storing data in one place", "B. Sending data in small pieces across different paths", "C. Encrypting data", "D. Blocking data"],
        "correct" => "B"
    ],
    [
        "question" => "What is a server on the Internet?",
        "options" => ["A. A user's laptop", "B. A computer that provides data or services to other computers", "C. A cable", "D. A router"],
        "correct" => "B"
    ],
    [
        "question" => "What do clients do on the Internet?",
        "options" => ["A. Store websites", "B. Request information or services from servers", "C. Route data", "D. Manage cables"],
        "correct" => "B"
    ],
    [
        "question" => "Which physical medium carries much of today's Internet traffic?",
        "options" => ["A. Copper wires", "B. Radio waves only", "C. Fiber-optic cables", "D. Paper"],
        "correct" => "C"
    ],
    [
        "question" => "What is the World Wide Web?",
        "options" => ["A. The entire Internet", "B. A system of interlinked web pages accessed over the Internet", "C. An email service", "D. A computer network"],
        "correct" => "B"
    ],
    [
        "question" => "Which of the following is NOT a use of the Internet?",
        "options" => ["A. Communication", "B. Commerce", "C. Education", "D. Electricity generation"],
        "correct" => "D"
    ],
    [
        "question" => "What makes the Internet able to grow and connect many networks?",
        "options" => ["A. One controlling company", "B. Open standards and protocols", "C. Local cables only", "D. Offline software"],
        "correct" => "B"
    ],
    [
        "question" => "What role do Internet Service Providers (ISPs) play?",
        "options" => ["A. Create web pages", "B. Provide users with access to the Internet", "C. Store all Internet data", "D. Control all websites"],
        "correct" => "B"
    ],
    [
        "question" => "Which invention made the Internet more user-friendly for the public?",
        "options" => ["A. Modems", "B. Routers", "C. The World Wide Web", "D. Firewalls"],
        "correct" => "C"
    ],
    [
        "question" => "Why is the Internet important to modern society?",
        "options" => ["A. It replaces computers", "B. It stores electricity", "C. It enables global communication, information sharing, and online services", "D. It only supports entertainment"],
        "correct" => "C"
    ]
];

foreach ($internetQuestions as $q) {
    $questionId = insertQuestion($internetQuizId, $q['question']);
    foreach ($q['options'] as $option) {
        $firstChar = strtoupper(substr(trim($option), 0, 1));
        $isCorrect = ($firstChar === strtoupper(trim($q['correct']))) ? 1 : 0;
        insertAnswer($questionId, $option, $isCorrect);
    }
}

// Network Quiz
$networkQuizId = insertQuiz("Computer Networking", "Network", "medium");

$networkQuestions = [
    [
        "question" => "What is computer networking?",
        "options" => ["A. Running programs on a computer", "B. Connecting computers and devices to share data and resources", "C. Building computer hardware", "D. Creating websites"],
        "correct" => "B"
    ],
    [
        "question" => "What is the main purpose of a network?",
        "options" => ["A. To make computers faster", "B. To allow devices to communicate and share resources", "C. To store electricity", "D. To play games"],
        "correct" => "B"
    ],
    [
        "question" => "Which of the following is an example of a networked device?",
        "options" => ["A. Calculator", "B. Smartphone", "C. Flash drive", "D. Battery"],
        "correct" => "B"
    ],
    [
        "question" => "What does a router do in a network?",
        "options" => ["A. Stores files", "B. Sends data between different networks", "C. Displays images", "D. Runs applications"],
        "correct" => "B"
    ],
    [
        "question" => "What is a LAN?",
        "options" => ["A. Long Area Network", "B. Local Area Network", "C. Large Access Network", "D. Linked Area Network"],
        "correct" => "B"
    ],
    [
        "question" => "What type of network connects devices across large geographic areas?",
        "options" => ["A. LAN", "B. WAN", "C. PAN", "D. VPN"],
        "correct" => "B"
    ],
    [
        "question" => "What does TCP/IP do in networking?",
        "options" => ["A. Displays web pages", "B. Protects against viruses", "C. Provides rules for how data is sent and received", "D. Stores data"],
        "correct" => "C"
    ],
    [
        "question" => "What is a server in a network?",
        "options" => ["A. A personal computer", "B. A computer that provides services or data to other computers", "C. A cable", "D. A keyboard"],
        "correct" => "B"
    ],
    [
        "question" => "What is a client in a network?",
        "options" => ["A. A router", "B. A server", "C. A device that requests services or data", "D. A modem"],
        "correct" => "C"
    ],
    [
        "question" => "What is bandwidth?",
        "options" => ["A. The size of a computer", "B. The number of devices", "C. The amount of data that can be transmitted", "D. The type of network"],
        "correct" => "C"
    ],
    [
        "question" => "Which of these is a benefit of networking?",
        "options" => ["A. Higher electricity use", "B. Sharing files and printers", "C. Slower computers", "D. Less security"],
        "correct" => "B"
    ],
    [
        "question" => "What device connects computers inside the same network?",
        "options" => ["A. Router", "B. Switch", "C. Monitor", "D. Scanner"],
        "correct" => "B"
    ],
    [
        "question" => "What type of connection uses radio signals instead of cables?",
        "options" => ["A. Wired network", "B. Wireless network", "C. Fiber network", "D. LAN"],
        "correct" => "B"
    ],
    [
        "question" => "Why are networks important in businesses?",
        "options" => ["A. To play music", "B. To share information and communicate efficiently", "C. To save power", "D. To install games"],
        "correct" => "B"
    ],
    [
        "question" => "What is one key role of networking in modern life?",
        "options" => ["A. It replaces computers", "B. It removes software", "C. It enables communication, data sharing, and online services", "D. It stops Internet access"],
        "correct" => "C"
    ]
];

foreach ($networkQuestions as $q) {
    $questionId = insertQuestion($networkQuizId, $q['question']);
    foreach ($q['options'] as $option) {
        $firstChar = strtoupper(substr(trim($option), 0, 1));
        $isCorrect = ($firstChar === strtoupper(trim($q['correct']))) ? 1 : 0;
        insertAnswer($questionId, $option, $isCorrect);
    }
}

// CyberSecurity Quiz
$cyberQuizId = insertQuiz("CyberSecurity Fundamentals", "CyberSecurity", "hard");

$cyberQuestions = [
    [
        "question" => "What is cybersecurity mainly concerned with?",
        "options" => ["A. Creating websites", "B. Protecting systems, networks, and data from attacks", "C. Building computers", "D. Selling software"],
        "correct" => "B"
    ],
    [
        "question" => "Which best describes a cyber threat?",
        "options" => ["A. A slow computer", "B. A software update", "C. Any action that can harm a digital system or data", "D. A strong password"],
        "correct" => "C"
    ],
    [
        "question" => "What is malware?",
        "options" => ["A. Useful software", "B. Malicious software designed to damage or steal data", "C. A firewall", "D. A password"],
        "correct" => "B"
    ],
    [
        "question" => "Which is malware?",
        "options" => ["A. Web browser", "B. Ransomware", "C. Email", "D. Spreadsheet"],
        "correct" => "B"
    ],
    [
        "question" => "What does ransomware do?",
        "options" => ["A. Protects files", "B. Locks files and demands payment", "C. Backs up data", "D. Cleans viruses"],
        "correct" => "B"
    ],
    [
        "question" => "What is phishing?",
        "options" => ["A. Fixing computers", "B. Tricking users into giving personal info", "C. Encrypting files", "D. Updating software"],
        "correct" => "B"
    ],
    [
        "question" => "Why are strong passwords important?",
        "options" => ["A. Make computers faster", "B. Prevent unauthorized access", "C. Save electricity", "D. Delete viruses"],
        "correct" => "B"
    ],
    [
        "question" => "What does encryption do?",
        "options" => ["A. Deletes data", "B. Converts data into coded form", "C. Sends emails", "D. Turns off computers"],
        "correct" => "B"
    ],
    [
        "question" => "Which helps keep system secure?",
        "options" => ["A. Ignoring updates", "B. Installing security patches", "C. Using weak passwords", "D. Sharing passwords"],
        "correct" => "B"
    ],
    [
        "question" => "What is authentication?",
        "options" => ["A. Downloading software", "B. Sending emails", "C. Verifying user identity", "D. Cleaning viruses"],
        "correct" => "C"
    ],
    [
        "question" => "What is MFA?",
        "options" => ["A. One password", "B. Two or more ways to prove identity", "C. Removing security", "D. Encrypting files"],
        "correct" => "B"
    ],
    [
        "question" => "What is a firewall?",
        "options" => ["A. A virus", "B. A password", "C. Controls network traffic for security", "D. A hacker"],
        "correct" => "C"
    ],
    [
        "question" => "Why be careful with email links?",
        "options" => ["A. Waste time", "B. May lead to phishing or malware", "C. Slow internet", "D. Delete"],
        "correct" => "B"
    ],
    [
        "question" => "What is a data breach?",
        "options" => ["A. System update", "B. Sensitive data accessed without permission", "C. Power failure", "D. Network upgrade"],
        "correct" => "B"
    ],
    [
        "question" => "Why is cybersecurity important?",
        "options" => ["A. Stop the internet", "B. Increase file size", "C. Protect digital information and systems", "D. Remove software"],
        "correct" => "C"
    ]
];

foreach ($cyberQuestions as $q) {
    $questionId = insertQuestion($cyberQuizId, $q['question']);
    foreach ($q['options'] as $option) {
        $firstChar = strtoupper(substr(trim($option), 0, 1));
        $isCorrect = ($firstChar === strtoupper(trim($q['correct']))) ? 1 : 0;
        insertAnswer($questionId, $option, $isCorrect);
    }
}

echo "Database populated successfully with all quiz questions!";
$conn->close();
?>