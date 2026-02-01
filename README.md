📚 Advocate Consultation Website (Advo)

A role-based advocate consultation platform developed using PHP, MySQL, HTML, CSS, and JavaScript.
The system allows clients to find advocates, book appointments, chat securely, and upload documents, while advocates and admins manage their respective dashboards.

🚀 Project Overview

The Advocate Consultation Website is designed to digitally connect clients and advocates through a secure and structured online platform.

The system supports:

  . Client–Advocate interaction

  . Appointment booking and approval

  . Secure document sharing

  . Real-time chat after appointment confirmation

  . Full admin control

This project follows real-world role-based access control and secure data handling practices.

👥 User Roles & Features
🔑 1. Admin

  . Manage users (clients & advocates)

  . View and manage appointments

  . Moderate reviews

  . Manage uploaded documents

  . Full system control

⚖️ 2. Advocate

  . Advocate dashboard

  . Manage profile details

  . Accept or reject appointments

  . Secure chat with confirmed clients

  . Upload and view documents

👤 3. Client (User)

  . Register & login

  . Search for advocates

  . Book appointments

  . Chat with advocates after confirmation

  . Upload and view case-related documents

💬 Chat System (Special Feature)

  . Chat is activated only after appointment confirmation

  . Real-time messaging using AJAX

Supports:

 . Text messages

 . Image uploads (JPG)

 . Message deletion

Ensures privacy and security

🗂️ Project Folder Structure
advo-advocate-consultation/
│
├── admin/          # Admin dashboard & controls
├── advocate/       # Advocate features & dashboard
├── users/          # Client features & dashboard
├── appointments/   # Appointment booking & management
├── chat/           # Real-time chat system
├── documents/      # Document upload & viewing
├── search/         # Advocate search module
├── assets/         # CSS, JavaScript, images
├── uploads/        # User uploaded files (ignored in Git)
├── includes/       # Common header, footer, auth files
│
├── index.php       # Homepage
├── .gitignore      # Security & ignored files
└── README.md

🛠️ Technologies Used
Frontend

. HTML

. CSS

. JavaScript

Backend

. PHP (Core PHP, no framework)

Database

. MySQL

Server

. XAMPP / WAMP (Apache + MySQL)

🔐 Security Practices

  . Database configuration file (db.php) is excluded from GitHub

  . File uploads are protected using .gitignore

  . Role-based access control implemented

  . Chat access restricted to confirmed appointments only

⚙️ How to Run This Project Locally
1️⃣ Clone the Repository
git clone https://github.com/shyam7903/advo-advocate-consultation.git

2️⃣ Move Project to Server Folder

Place the folder inside:

C:/xampp/htdocs/

3️⃣ Create Database

. Open phpMyAdmin

. Create a new database (example: advo_db)

. Import the SQL file (if provided)

4️⃣ Configure Database

. Create your local db.php file and update:

. Database name

. Username

. Password

5️⃣ Start Server

. Start Apache and MySQL from XAMPP

. Open browser and visit:

  http://localhost/advo-advocate-consultation/

📌 Key Highlights

. ✔ Role-based authentication

. ✔ Secure real-time chat

. ✔ Document upload system

. ✔ Clean folder structure

. ✔ Industry-level GitHub practices

. ✔ College & resume ready

👨‍💻 Author

Shyam Ranjan Tiwary
BCA Project
GitHub: https://github.com/shyam7903

⭐ Acknowledgement

. This project was developed as an academic project to demonstrate:

. Full-stack web development

. Secure backend logic

. Practical database design

. Real-world system implementation
