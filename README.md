# 📄 Multi-User Document Editor System

## 📌 Project Description

The **Multi-User Document Editor System** is a collaborative platform that allows multiple users to create, edit, share, and manage documents in real-time. The system supports user access control, document versioning, commenting, activity tracking, and folder-based organization for efficient document management.

This project demonstrates database design concepts such as Entity-Relationship Modeling (ER Diagram), relational schema, and SQL operations including CRUD functionalities.

---

## 🎯 Objectives

- To allow multiple users to create and manage documents
- To enable document sharing and collaboration
- To maintain version history of documents
- To support commenting on documents
- To track user activities on documents
- To organize documents into folders

---

## 🧩 Features

- User Registration and Management  
- Document Creation and Editing  
- Document Sharing with Permissions  
- Version Control  
- Comment System  
- Activity Logging  
- Folder Management  
- Multi-user Collaboration  

---

## 🗃️ Database Schema

The system consists of the following tables:

- **User** – Stores user details  
- **Document** – Stores document information  
- **Collaboration** – Manages document sharing among users  
- **Version** – Maintains document edit history  
- **Comment** – Stores comments on documents  
- **Activity_Log** – Tracks user actions  
- **Folder** – Organizes documents  
- **Document_Folder** – Maps documents to folders  

---

## 🔗 Entity Relationships

- A User can create multiple Documents  
- A Document belongs to one User  
- Multiple Users can collaborate on a Document  
- A Document can have multiple Versions  
- Users can comment on Documents  
- Activity logs track user interactions  
- Documents can be stored in multiple Folders  

---

## 💻 Technologies Used

| Technology | Usage |
|------------|--------|
| HTML | Frontend Structure |
| CSS | Styling |
| PHP | Backend |
| MySQL | Database |
| XAMPP | Server |
| Draw.io | ER Diagram |

---

## ⚙️ SQL Operations Implemented

- CREATE Database and Tables  
- INSERT Records  
- UPDATE Records  
- DELETE Records  
- SELECT Queries  
- JOIN Queries  

---

## 📊 ER Diagram

The ER Diagram represents the relationship between different entities involved in the Multi-User Document Editor System such as User, Document, Collaboration, Version, Comment, Folder, and Activity Log.

---

## Database Normalization (Up to 3NF)

The database schema has been designed and normalized to ensure data integrity and reduce redundancy.

### 1. First Normal Form (1NF)
- **Rule**: Eliminate repeating groups and ensure atomicity.
- **Implementation**: All attributes in the tables contain atomic values. There are no repeating groups or arrays. Each table has a Primary Key to uniquely identify records.

### 2. Second Normal Form (2NF)
- **Rule**: Eliminate partial dependencies (apply to tables with composite keys).
- **Implementation**: All tables are in 1NF. For tables with composite primary keys (e.g., `Collaboration`, `Document_Folder`), all non-key attributes are fully dependent on the entire primary key, not just a part of it.

### 3. Third Normal Form (3NF)
- **Rule**: Eliminate transitive dependencies.
- **Implementation**: All tables are in 2NF. There are no transitive dependencies; non-key attributes depend *only* on the Primary Key. For example, user details are stored only in the `User` table and referenced via foreign keys elsewhere.

### Normalized EER Diagram

![Normalization Diagram](Normalization%20using%20MySQL%20Workbench.png)

The database schema was normalized up to Third Normal Form (3NF) using MySQL Workbench. Reverse engineering was used to generate the EER diagram from the implemented schema. All tables satisfy normalization conditions with no partial or transitive dependencies.

---

## 👨‍💻 Team Members

- Team Leader: *Ritesh Kumar*  
- Member 1: *Kajal Kiran*  
- Member 2: *Rudra Mohan*  
- Member 3: *Raj Dixit*  

---

## 🔗 GitHub Repository

Add your GitHub repository link here.

---

## 📅 Submission

This project is submitted as part of the Database Management System coursework.