
# Laravel Project with Docker

This is a Laravel application that integrates with the following APIs:


 - NewApi
 - The Guardian
 - NewYork API


# Prerequisites
Ensure you have the following installed on your machine:

- Docker
- Docker Compose

# Installation
Follow the steps below to install and run the project using Docker:

**1. Clone the Repository**

Clone the project repository to your local machine:

- git clone  https://github.com/muhammad550/newaggrregator.git
- cd newaggrregator

**2. Build the Docker Containers**

Run the following command to build the Docker containers:

  - docker-compose build

**3. Run the Docker Containers**
    Start the containers by executing:

 - docker-compose up

**4. Access the Application**

Open your browser and navigate to **http://localhost:8000** to access the Laravel application.
For database management, go to **http://localhost:8383** to access phpMyAdmin.

**Troubleshooting**

If the database does not save records, follow these steps:

 
- Stop the running containers:

- docker-compose down**

**2. Start the containers again:**

- docker-compose up


