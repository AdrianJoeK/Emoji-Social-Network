# Emoji Social Network

The Emoji Social Network is a social media website where users can communicate exclusively using emojis. This project aims to explore new forms of expression in digital communication, limiting text usage to encourage creativity with emojis.

## Features

- **User Authentication:** Secure login and registration system.
- **Emoji Messaging:** Send and receive messages using only emojis.
- **Group Chats:** Create a group conversations.
- **Profile Customization:** Set a profile picture.

## Prerequisites

- PHP 7.4 or higher
- MariaDB 10.6.17 or any compatible MySQL database

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/AdrianJoeK/emoji-social-network.git
   cd emoji-social-network
2. **Import SQL File Into Your Database**
- Import "setup-files-do-not-upload-to-web-server/emoji_social_network.sql" into your database. This will create the database structure for you.
3. **Create db.php Using Template**
  - Copy "setup-files-do-not-upload-to-web-server/db-template.php" into your root folder, rename it to "db.php", then edit the file with your database connection information.
