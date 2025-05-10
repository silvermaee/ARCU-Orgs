# ARCU Project

## Overview
The ARCU project is a web application designed for managing user authentication and providing dashboards for both students and administrators. The application includes features tailored to the needs of each user type, ensuring a seamless experience.

## File Structure
```
arcu
├── ARCU-Orgs
│   ├── src
│   │   ├── ARCU-Login.php          # Handles user authentication and session management.
│   │   ├── ARCU-Dashboard-Admin.php # Admin dashboard with admin-specific functionalities.
│   │   ├── ARCU-Dashboard-Student.php # Student dashboard with essential features for students.
│   │   └── styles
│       └── dashboard.css            # CSS styles for the dashboards, ensuring responsiveness.
├── img
│   ├── ARCULOGO.png                 # Logo image for the application.
│   └── ARCUBG.jpg                   # Background image for the application.
├── node_modules
│   └── bootstrap
│       └── dist
│           └── js
│               └── bootstrap.bundle.min.js # Bootstrap JavaScript library for responsive design.
├── db_connection.php                 # Manages database connections and error handling.
└── README.md                         # Documentation for the project, including setup and usage instructions.
```

## Setup Instructions
1. **Clone the Repository**: Clone this repository to your local machine using `git clone <repository-url>`.
2. **Install Dependencies**: Navigate to the project directory and run `npm install` to install necessary dependencies.
3. **Database Configuration**: Update the `db_connection.php` file with your database credentials.
4. **Run the Application**: Use a local server environment (like XAMPP) to run the application. Place the project folder in the `htdocs` directory and access it via your web browser.

## Usage Guidelines
- **Login**: Users can log in using their credentials through the `ARCU-Login.php` page.
- **Dashboards**: After logging in, users will be redirected to their respective dashboards:
  - **Admin Dashboard**: Accessible via `ARCU-Dashboard-Admin.php`, tailored for administrative tasks.
  - **Student Dashboard**: Accessible via `ARCU-Dashboard-Student.php`, providing essential features for students.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any improvements or bug fixes.

## License
This project is licensed under the MIT License. See the LICENSE file for more details.