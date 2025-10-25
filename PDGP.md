# Product Display Generator Project

## Overview

This project aims to create a streamlined system for generating and deploying product display interfaces to embedded devices. Users provide product data through a simple application, which is processed on the server side to automatically generate frontend code using an AI model. The generated package is then ingested by Yocto-based devices for display.

The system is designed for ease of use, allowing non-technical users to input data via spreadsheets and images, while the backend handles code generation, asset management, and packaging. Not all details are finalized, so this document outlines the high-level concept and planned components.

## Key Features

- **User-Friendly Input**: A simple app for copying/pasting spreadsheet data and uploading images.
- **AI-Driven Code Generation**: Server uses a lightweight (1-bit) model to generate frontend code based on a predefined framework.
- **Asset Management**: Handles product images, names, and other assets.
- **Deployment to Devices**: Packages generated code and assets for ingestion by Yocto devices.
- **Tech Stack**: Rust for the client app, PHP for the server, and Yocto for the target devices.

## Architecture

The system consists of three main components:

1. **Client (Rust-based App)**:
   - A lightweight application built in Rust.
   - Allows users to input product data by copying/pasting from spreadsheets (e.g., CSV format).
   - Supports adding images via file upload or drag-and-drop.
   - Packages the data (spreadsheet values + images) into a structured payload.
   - Sends the payload to the server via API (e.g., HTTP POST).

2. **Server (PHP-based)**:
   - Receives and ingests the user-submitted payload.
   - Processes the data: Parses spreadsheet values into a backend struct (e.g., for products with fields like item name, image name, assets, and sample image).
   - Uses a 1-bit AI model to generate frontend code based on a predefined framework (details TBD).
   - Arranges assets (e.g., images) and integrates them into the generated code.
   - Packages the output (code + assets) into a deployable format for devices.
   - The server focuses primarily on code generation: Ingests a prompt derived from the user data, generates content/code, and prepares the package.

3. **Device (Yocto-based)**:
   - Embedded devices running a Yocto Linux distribution.
   - Ingests the packaged output from the server (e.g., via download or push mechanism).
   - Displays the generated frontend, showcasing products with names, images, and assets.

## Data Structure

- **Spreadsheet Input Example**:
  A simple CSV or similar format with columns like:
  - `item_name`: Product name (string).
  - `image_name`: Filename of the associated image (string).
  - `assets`: Additional assets or descriptions (string or JSON).
  - `sample_image`: Path or reference to a sample image (optional).

  Backend processes this into a struct, e.g., in PHP:
  ```php
  class Product {
      public string $itemName;
      public string $imageName;
      public array $assets; // e.g., ['asset1' => 'value']
      public string $sampleImage; // URL or base64
  }
  ```

## Workflow

1. **User Input**:
   - Open the Rust client app.
   - Copy-paste spreadsheet data (e.g., product rows).
   - Click to add/upload images for each product.
   - Submit to package and send to the server.

2. **Server Processing**:
   - Ingest the payload.
   - Parse data into backend structs.
   - Generate a prompt for the 1-bit AI model based on the data and framework.
   - Run the model to produce frontend code (e.g., HTML/CSS/JS for display).
   - Arrange and bundle assets (e.g., compress images, embed in package).
   - Create a final package (e.g., tarball or custom format).

3. **Device Ingestion**:
   - Device pulls or receives the package.
   - Unpacks and runs the generated frontend to display products.

## Implementation Status

### ✅ Completed: Server Infrastructure

The server component is fully implemented with:

- **Docker Environment**: Multi-container setup with PHP 8.2, Nginx, MySQL 8.0, and phpMyAdmin
- **Database Schema**: Complete schema with users, sites, products, uploads, and packages tables
- **JWT Authentication**: Secure token-based authentication with login, register, and refresh endpoints
- **RESTful API**: Full CRUD operations for sites, product uploads, and package management
- **Web Console**: Bootstrap-based management interface with dashboard, site management, and upload functionality
- **Asset Management**: Image upload, validation, optimization, and storage system
- **CSV Template Generator**: Sample template for product data structure

**Technology Stack:**
- PHP 8.2 with Composer
- MySQL 8.0
- Nginx web server
- JWT (Firebase PHP-JWT library)
- Bootstrap 5 for UI
- Docker & Docker Compose

**Access Points:**
- Web Console: http://localhost:8080
- API Base: http://localhost:8080/api
- phpMyAdmin: http://localhost:8081

See [server/README.md](server/README.md) for detailed documentation.

### 🚧 Open Decisions and TODOs

- **AI Model Details**: Specify the 1-bit model (e.g., integration with an existing lightweight LLM or custom implementation).
- **Framework for Frontend**: Define the base framework/template for code generation (e.g., a simple webview or embedded UI kit).
- **Rust Client**: Design and implement the desktop application for data upload.
- **Package Generation**: Implement AI-driven code generation and packaging system.
- **Yocto Integration**: Set up device ingestion and display mechanisms.
- **Error Handling**: Enhance retries, logging, and user feedback.
- **Scalability**: Consider multiple devices or batch processing.
- **Testing**: Set up unit tests for client/server and integration tests with sample Yocto images.

## Getting Started

(This section will be expanded as development progresses.)

- **Prerequisites**: Rust toolchain, PHP environment, Yocto build setup.
- **Installation**: Clone the repo and follow component-specific READMEs (TBD).
- **Running Locally**: Start PHP server, build Rust client, simulate Yocto device.

This Markdown serves as a starting point and can be updated as more details are decided.
