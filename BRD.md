# 📘 Business Requirements Document (BRD)

## 📌 Project Title
**Multi-User Document Editor System**

---

## 📌 1. Project Overview

The Multi-User Document Editor System is a collaborative platform that allows multiple users to create, edit, share, and manage documents in real-time. The system supports document versioning, commenting, activity tracking, and permission-based collaboration. It provides an efficient solution for teams working together on shared documents in a centralized environment.

---

## 📌 2. Purpose of the System

The purpose of this system is to:

- Enable multiple users to collaborate on documents
- Maintain version history of documents
- Manage document access using permissions
- Allow users to comment on shared documents
- Organize documents into folders
- Track user activities for audit and monitoring

---

## 📌 3. Scope

### ✔ In Scope
- User registration and login
- Document creation and editing
- Folder management
- Document sharing with permissions
- Version control system
- Commenting on documents
- Activity logging
- Multi-user collaboration

### ❌ Out of Scope
- Real-time chat between users
- Video/audio collaboration
- Cloud storage integration
- Third-party authentication

---

## 📌 4. Stakeholders

| Stakeholder | Role |
|-------------|------|
| System Users | Create and manage documents |
| Admin | Manage users and permissions |
| Developers | Develop and maintain system |
| Project Team | Implementation and testing |

---

## 📌 5. Functional Requirements

### 5.1 User Management
- Users must be able to register and log in
- Users must be assigned roles
- Users can create folders
- Users can create documents

---

### 5.2 Document Management
- Users can create new documents
- Users can edit existing documents
- Users can delete documents
- Users can organize documents into folders

---

### 5.3 Collaboration Management
- Users can share documents with others
- Users can assign permission types:
  - View
  - Edit
- Shared users can access documents

---

### 5.4 Version Control
- Each document modification creates a new version
- Previous versions must be stored
- Users can view document history

---

### 5.5 Comment Management
- Users can add comments to documents
- Comments must store timestamp
- Comments linked with user and document

---

### 5.6 Activity Logging
- All user activities must be tracked:
  - Create
  - Edit
  - Share
  - Comment
- Activity logs include:
  - User ID
  - Document ID
  - Action Type
  - Timestamp

---

## 📌 6. Non-Functional Requirements

| Requirement | Description |
|-------------|-------------|
| Performance | System should support multiple users simultaneously |
| Security | Access control using permission types |
| Reliability | Data must be stored without loss |
| Scalability | System should support future expansion |
| Usability | Simple and user-friendly interface |

---

## 📌 7. Assumptions

- Users have internet access
- Database server is available
- System will be used within an organization

---

## 📌 8. Constraints

- Limited development time
- System depends on MySQL database
- Basic authentication only

---

## 📌 9. Data Requirements

System stores:

- User Information
- Document Details
- Folder Information
- Collaboration Permissions
- Document Versions
- Comments
- Activity Logs

---

## 📌 10. System Requirements

### Hardware:
- Computer with minimum 4GB RAM

### Software:
- MySQL Database
- XAMPP Server
- MySQL Workbench
- Draw.io

---

## 📌 11. Risks

- Unauthorized access
- Data redundancy
- System downtime

---

## 📌 12. Deliverables

- ER Diagram
- Normalized EER Diagram
- SQL Schema
- README File
- Project Report

---